<?php

/**
 * Authors: 
 *  - Tyrence (RK2)
 *  - Mindrila (RK1)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'online',
 *		accessLevel = 'member',
 *		description = 'Shows who is online',
 *		help        = 'online.txt'
 *	)
 */
class OnlineController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $setting;
	
	/** @Inject */
	public $accessLevel;
	
	/** @Inject */
	public $buddylistManager;
	
	/** @Inject */
	public $ircRelayController;
	
	/** @Inject */
	public $alts;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Logger */
	public $logger;
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "online");
		
		$this->setting->add($this->moduleName, "online_expire", "How long to wait before clearing online list", "edit", "time", "15m", "2m;5m;10m;15m;20m", '', "mod");
		$this->setting->add($this->moduleName, "chatlist_tell", "Mode for Chatlist Cmd in tells", "edit", "options", "1", "Shows online privatechat members;Shows online guild members", "1;0");
		$this->setting->add($this->moduleName, "fancy_online", "Show fancy delimiters on the online display", "edit", "options", "1", "true;false", "1;0");
		$this->setting->add($this->moduleName, "icon_fancy_online", "Show profession icons in the online display", "edit", "options", "1", "true;false", "1;0");
		$this->setting->add($this->moduleName, "online_group_by", "How to group online list", "edit", "options", "profession", "profession;guild");
		$this->setting->add($this->moduleName, "online_show_org_guild", "Show org/rank for players in guild channel", "edit", "options", "1", "Show org and rank;Show rank only;Show org only;Show no org info", "2;1;3;0");
		$this->setting->add($this->moduleName, "online_show_org_priv", "Show org/rank for players in private channel", "edit", "options", "2", "Show org and rank;Show rank only;Show org only;Show no org info", "2;1;3;0");
		$this->setting->add($this->moduleName, "online_colorful", "Use fancy coloring for online list", "edit", "options", "1", "true;false", "1;0");
		$this->setting->add($this->moduleName, "online_admin", "Show admin levels in online list", "edit", "options", "0", "true;false", "1;0");
	}
	
	/**
	 * @HandlesCommand("online")
	 * @Matches("/^online$/i")
	 * @Matches("/^online (.*)$/i")
	 */
	public function onlineCommand($message, $channel, $sender, $sendto, $args) {
		if (count($args) == 2) {
			$prof = strtolower($args[1]);
			if ($prof != 'all') {
				$prof = $this->util->get_profession_name($prof);
			}

			if ($prof == null) {
				return false;
			}
		} else {
			$prof = 'all';
		}

		list($numonline, $msg, $blob) = $this->get_online_list($prof);
		if ($numonline != 0) {
			$msg = $this->text->make_blob($msg, $blob);
			$sendto->reply($msg);
		} else {
			$sendto->reply($msg);
		}
	}
	
	/**
	 * @Event("logOn")
	 * @Description("Records an org member login in db")
	 */
	public function recordLogonEvent($eventObj) {
		$sender = $eventObj->sender;
		if (isset($this->chatBot->guildmembers[$sender])) {
			$this->addPlayerToOnlineList($sender, $chatBot->vars['guild'], 'guild');
		}
	}
	
	/**
	 * @Event("logOff")
	 * @Description("Records an org member logoff in db")
	 */
	public function recordLogoffEvent($eventObj) {
		$sender = $eventObj->sender;
		if (isset($this->chatBot->guildmembers[$sender])) {
			$this->removePlayerFromOnlineList($sender, 'guild');
		}
	}
	
	/**
	 * @Event("logOn")
	 * @Description("Sends a tell to players on logon showing who is online in org")
	 */
	public function showOnlineOnLogonEvent($eventObj) {
		$sender = $eventObj->sender;
		if (isset($this->chatBot->guildmembers[$sender]) && $this->chatBot->is_ready()) {
			list($numonline, $msg, $blob) = $this->get_online_list();
			if ($numonline != 0) {
				$msg = $this->text->make_blob($msg, $blob);
				$this->chatBot->sendTell($msg, $sender);
			} else {
				$this->chatBot->sendTell($msg, $sender);
			}
		}
	}
	
	/**
	 * @Event("10mins")
	 * @Description("Online check")
	 */
	public function onlineCheckEvent($eventObj) {
		if ($this->chatBot->is_ready()) {
			$this->db->begin_transaction();
			$data = $this->db->query("SELECT name, channel_type FROM `online`");

			$guildArray = array();
			$privArray = array();
			$ircArray = array();

			forEach ($data as $row) {
				switch ($row->channel_type) {
					case 'guild': $guildArray []= $row->name; break;
					case 'priv' : $privArray []= $row->name; break;
					case 'irc'  : $ircArray []= $row->name; break;
					default     : $this->logger->log("WARN", "ONLINE_MODULE", "Unknown channel type: '$row->channel_type'. Expected: 'guild', 'priv' or 'irc'");
				}
			}

			$time = time();

			forEach ($this->chatBot->guildmembers as $name => $rank) {
				if ($this->buddylistManager->is_online($name)) {
					if (in_array($name, $guildArray)) {
						$sql = "UPDATE `online` SET `dt` = ? WHERE `name` = ? AND added_by = '<myname>' AND channel_type = 'guild'";
						$this->db->exec($sql, $time, $name);
					} else {
						$sql = "INSERT INTO `online` (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES (?, '<myguild>', 'guild', '<myname>', ?)";
						$this->db->exec($sql, $name, $time);
					}
				}
			}

			forEach ($this->chatBot->chatlist as $name => $value) {
				if (in_array($name, $privArray)) {
					$sql = "UPDATE `online` SET `dt` = ? WHERE `name` = ? AND added_by = '<myname>' AND channel_type = 'priv'";
					$this->db->exec($sql, $time, $name);
				} else {
					$sql = "INSERT INTO `online` (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES (?, '<myguild> Guest', 'priv', '<myname>', ?)";
					$this->db->exec($sql, $name, $time);
				}
			}

			if ($this->ircRelayController !== null) {
				$ircSocket = $this->ircRelayController->getIRCSocket();
				if (IRC::isConnectionActive($ircSocket)) {
					forEach (IRC::getUsersInChannel($ircSocket, $this->setting->get('irc_channel')) as $name) {
						if (in_array($name, $ircArray)) {
							$sql = "UPDATE `online` SET `dt` = ? WHERE `name` = ? AND added_by = '<myname>' AND channel_type = 'irc'";
							$this->db->exec($sql, $time, $name);
						} else if ($name != $this->setting->get('irc_nickname')) {
							$sql = "INSERT INTO `online` (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES (?, ?, 'irc', '<myname>', ?)";
							$this->db->exec($sql, $name, $this->setting->get('irc_channel'), $time);
						}
					}
				}
			}

			$sql = "DELETE FROM `online` WHERE (`dt` < ? AND added_by = '<myname>') OR (`dt` < ?)";
			$this->db->exec($sql, $time, ($time - $this->setting->get('online_expire')));
			$this->db->commit();
		}
	}
	
	/**
	 * @Event("priv")
	 * @Description("Afk check")
	 * @Help("afk")
	 */
	public function afkCheckPrivateChannelEvent($eventObj) {
		$this->afkCheck($eventObj->sender, $eventObj->message, $eventObj->type);
	}
	
	/**
	 * @Event("guild")
	 * @Description("Afk check")
	 * @Help("afk")
	 */
	public function afkCheckGuildChannelEvent($eventObj) {
		$this->afkCheck($eventObj->sender, $eventObj->message, $eventObj->type);
	}
	
	/**
	 * @Event("priv")
	 * @Description("Sets a member afk")
	 * @Help("afk")
	 */
	public function afkPrivateChannelEvent($eventObj) {
		$this->afk($eventObj->sender, $eventObj->message, $eventObj->type);
	}
	
	/**
	 * @Event("guild")
	 * @Description("Sets a member afk")
	 * @Help("afk")
	 */
	public function afkGuildChannelEvent($eventObj) {
		$this->afk($eventObj->sender, $eventObj->message, $eventObj->type);
	}
	
	public function afkCheck($sender, $message, $type) {
		// to stop raising and lowering the cloak messages from triggering afk check
		if (!$this->util->isValidSender($sender)) {
			return;
		}

		if (!preg_match("/^.?afk(.*)$/i", $message)) {
			$row = $this->db->queryRow("SELECT afk FROM online WHERE `name` = ? AND added_by = '<myname>' AND channel_type = ?", $sender, $type);

			if ($row !== null && $row->afk != '') {
				// $sender is back
				$this->db->exec("UPDATE online SET `afk` = '' WHERE `name` = ? AND added_by = '<myname>' AND channel_type = ?", $sender, $type);
				$msg = "<highlight>{$sender}<end> is back";
				
				if ('priv' == $type) {
					$this->chatBot->sendPriv($msg);
				} else if ('guild' == $type) {
					$this->chatBot->sendGuild($msg);
				}
			}
		}
	}
	
	public function afk($sender, $message, $type) {
		if (preg_match("/^.?afk$/i", $message)) {
			$this->db->exec("UPDATE online SET `afk` = ? WHERE `name` = ? AND added_by = '<myname>' AND channel_type = ?", '1', $sender, $type);
			$msg = "<highlight>$sender<end> is now AFK";
		} else if (preg_match("/^.?brb(.*)$/i", $message, $arr)) {
			$this->db->exec("UPDATE online SET `afk` = ? WHERE `name` = ? AND added_by = '<myname>' AND channel_type = ?", 'brb', $sender, $type);
			$msg = "<highlight>$sender<end> is now AFK";
		} else if (preg_match("/^.?afk (.*)$/i", $message, $arr)) {
			$reason = $arr[1];
			$this->db->exec("UPDATE online SET `afk` = ? WHERE `name` = ? AND added_by = '<myname>' AND channel_type = ?", $reason, $sender, $type);
			$msg = "<highlight>$sender<end> is now AFK";
		} else if (preg_match("/^.?kiting$/i", $message, $arr) && $numrows != 0) {
			$this->db->exec("UPDATE online SET `afk` = ? WHERE `name` = ? AND added_by = '<myname>' AND channel_type = ?", 'kiting', $sender, $type);
			$msg = "<highlight>$sender<end> is now kiting";
		}

		if ('' != $msg) {
			if ('priv' == $type) {
				$this->chatBot->sendPriv($msg);
			} else if ('guild' == $type) {
				$this->chatBot->sendGuild($msg);
			}
			throw new StopExecutionException();
		}
	}
	
	public function addPlayerToOnlineList($sender, $channel, $channelType) {
		$sql = "SELECT name FROM `online` WHERE `name` = ? AND `channel_type` = ? AND added_by = '<myname>'";
		$data = $this->db->query($sql, $sender, $channelType);
		if (count($data) == 0) {
			$sql = "INSERT INTO `online` (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES (?, ?, ?, '<myname>', ?)";
			$this->db->exec($sql, $sender, $channel, $channelType, time());
		}
	}
	
	public function removePlayerFromOnlineList($sender, $channelType) {
		$sql = "DELETE FROM `online` WHERE `name` = ? AND `channel_type` = ? AND added_by = '<myname>'";
		$this->db->exec($sql, $sender, $channelType);
	}
	
	public function get_online_list($prof = "all") {
		if ($prof != 'all') {
			$prof_query = "AND `profession` = '$prof'";
		}

		if ($this->setting->get('online_group_by') == 'profession') {
			$order_by = "ORDER BY `profession`, `level` DESC";
		} else if ($this->setting->get('online_group_by') == 'guild') {
			$order_by = "ORDER BY `channel` ASC, `name` ASC";
		}

		$blob = '';

		// Guild Channel Part
		$data = $this->db->query("SELECT p.*, o.name, o.channel, o.afk FROM `online` o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE o.channel_type = 'guild' {$prof_query} {$order_by}");
		$numguild = count($data);

		if ($numguild >= 1) {
			$blob .= "<header2> :::::: $numguild ".($numguild == 1 ? "Member":"Members")." online ".($this->chatBot->vars['my_guild'] != '' ? "[<myguild>] ":"")." ::::::<end>\n";

			// create the list with alts shown
			$blob .= $this->createList($data, $list, true, $this->setting->get("online_show_org_guild"));
		}

		// Private Channel Part
		$data = $this->db->query("SELECT p.*, o.name, o.channel, o.afk FROM `online` o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE o.channel_type = 'priv' {$prof_query} {$order_by}");
		$numguest = count($data);

		if ($numguest >= 1) {
			if ($numguild >= 1) {
				$blob .= "\n\n<header2>$numguest ".($numguest == 1 ? "User":"Users")." in Private Channel<end>\n";
			} else {
				$blob .= "<header2> :::::: $numguest ".($numguest == 1 ? "User":"Users")." in Private Channel ::::::<end>\n";
			}

			// create the list of guests, without showing alts
			$blob .= $this->createList($data, $list, true, $this->setting->get("online_show_org_priv"));
		}

		// IRC part
		$data = $this->db->query("SELECT o.name, o.afk, o.channel, o.channel_type, '' AS profession FROM `online` o WHERE o.channel_type = 'irc' AND o.name <> '<myname>' ORDER BY `name` ASC");
		$numirc = count($data);

		if ($numirc >= 1) {
			if ($numguild + $numguest >= 1) {
				$blob .= "\n\n<header2>$numirc ".($numirc == 1 ? "User":"Users")." in IRC Channel(s) <end>\n";
			} else {
				$blob .= "<header2> :::::: $numirc ".($numirc == 1 ? "User":"Users")." in IRC Channel(s) :::::: <end>\n";
			}

			// create the list of guests
			$blob .= $this->createListByChannel($data, $list, false, false);
		}

		$numonline = $numguild + $numguest + $numirc;

		$msg .= "$numonline ".($numonline == 1 ? "member":"members")." online";

		// BBIN part
		if ($this->setting->get("bbin_status") == 1) {
			// members
			$data = $this->db->query("SELECT * FROM bbin_chatlist_<myname> WHERE (`guest` = 0) {$prof_query} ORDER BY `profession`, `level` DESC");
			$numbbinmembers = count($data);

			if ($numbbinmembers >= 1) {
				$blob .= "\n\n<header2>$numbbinmembers ".($numbbinmembers == 1 ? "Member":"Members")." in BBIN<end>\n";

				$blob .= $this->createListByProfession($data, $list, false, true);
			}

			// guests
			$data = $this->db->query("SELECT * FROM bbin_chatlist_<myname> WHERE (`guest` = 1) {$prof_query} ORDER BY `profession`, `level` DESC");
			$numbbinguests = count($data);

			if ($numbbinguests >= 1) {
				$blob .= "\n\n<header2>$numbbinguests ".($numbbinguests == 1 ? "Guest":"Guests")." in BBIN<end>\n";

				$blob .= $this->createListByProfession($data, $list, false, true);
			}

			$numonline += $numbbinguests + $numbbinmembers;

			$msg .= " <green>BBIN<end>:".($numbbinguests + $numbbinmembers)." online";
		}

		return array($numonline, $msg, $blob);
	}

	public function createList(&$data, &$list, $show_alts, $show_org_info) {
		if ($this->setting->get('online_group_by') == 'profession') {
			return $this->createListByProfession($data, $list, $show_alts, $show_org_info);
		} else if ($this->setting->get('online_group_by') == 'guild') {
			return $this->createListByChannel($data, $list, $show_alts, $show_org_info);
		}
	}

	public function createListByChannel(&$data, &$list, $show_alts, $show_org_info) {
		//Colorful temporary var settings (avoid a mess of if statements later in the function)
		$fancyColon = ($this->setting->get("online_colorful") == "1") ? "<highlight>::<end>":"::";

		$blob = '';
		forEach ($data as $row) {
			$name = $this->text->make_chatcmd($row->name, "/tell $row->name");

			if ($current_channel != $row->channel) {
				$current_channel = $row->channel;
				$blob .= "\n<tab><highlight>$current_channel<end>\n";
			}

			$afk = $this->get_afk_info($row->afk, $fancyColon);
			$alt = ($show_alts == true) ? $this->get_alt_char_info($row->name, $fancyColon):"";

			switch ($row->profession) {
				case "":
					$blob .= "<tab><tab>$name - Unknown$alt\n";
					break;
				default:
					$admin = ($show_alts == true) ? $this->get_admin_info($row->name, $fancyColon):"";
					$guild = $this->get_org_info($show_org_info, $fancyColon, $row->guild, $row->guild_rank);
					$blob .= "<tab><tab>$name (Lvl $row->level/<green>$row->ai_level<end>)$guild$afk$alt$admin\n";
			}
		}

		return $blob;
	}

	public function createListByProfession(&$data, &$list, $show_alts, $show_org_info) {
		//Colorful temporary var settings (avoid a mess of if statements later in the function)
		$fancyColon = ($this->setting->get("online_colorful") == "1") ? "<highlight>::<end>":"::";

		$current_profession = "";

		$blob = '';
		forEach ($data as $row) {
			if ($current_profession != $row->profession) {
				if ($this->setting->get("fancy_online") == 0) {
					// old style delimiters
					$blob .= "\n<tab><highlight>$row->profession<end>\n";
				} else {
					// fancy delimiters
					$blob .= "\n<img src=tdb://id:GFX_GUI_FRIENDLIST_SPLITTER>\n";

					if ($this->setting->get("icon_fancy_online") == 1) {
						if ($row->profession == "Adventurer") {
							$blob .= "<img src=rdb://84203>";
						} else if ($row->profession == "Agent") {
							$blob .= "<img src=rdb://16186>";
						} else if ($row->profession == "Bureaucrat") {
							$blob .= "<img src=rdb://46271>";
						} else if ($row->profession == "Doctor") {
							$blob .= "<img src=rdb://44235>";
						} else if ($row->profession == "Enforcer") {
							$blob .= "<img src=rdb://117926>";
						} else if ($row->profession == "Engineer") {
							$blob .= "<img src=rdb://16307>";
						} else if ($row->profession == "Fixer") {
							$blob .= "<img src=rdb://16300>";
						} else if ($row->profession == "Keeper") {
							$blob .= "<img src=rdb://38911>";
						} else if ($row->profession == "Martial Artist") {
							$blob .= "<img src=rdb://16289>";
						} else if ($row->profession == "Meta-Physicist") {
							$blob .= "<img src=rdb://16283>";
						} else if ($row->profession == "Nano-Technician") {
							$blob .= "<img src=rdb://45190>";
						} else if ($row->profession == "Soldier") {
							$blob .= "<img src=rdb://16195>";
						} else if ($row->profession == "Shade") {
							$blob .= "<img src=rdb://39290>";
						} else if ($row->profession == "Trader") {
							$blob .= "<img src=rdb://118049>";
						} else {
							$blob .= "<img src=rdb://46268>";
						}
					}

					$blob .= " <highlight>$row->profession<end>\n<img src=tdb://id:GFX_GUI_FRIENDLIST_SPLITTER>\n";
				}

				$current_profession = $row->profession;
			}

			$name = $this->text->make_chatcmd($row->name, "/tell $row->name");
			$afk  = $this->get_afk_info($row->afk, $fancyColon);
			$alt  = ($show_alts == true) ? $this->get_alt_char_info($row->name, $fancyColon):"";

			switch ($row->profession) {
				case "":
					$blob .= "<tab><tab>$name - Unknown$alt\n";
					break;
				default:
					$admin = ($show_alts == true) ? $this->get_admin_info($row->name, $fancyColon):"";
					$guild = $this->get_org_info($show_org_info, $fancyColon, $row->guild, $row->guild_rank);
					$blob .= "<tab><tab>$name (Lvl $row->level/<green>$row->ai_level<end>)$guild$afk$alt$admin\n";
			}
		}

		return $blob;
	}

	public function get_org_info($show_org_info, $fancyColon, $guild, $guild_rank) {
		switch ($show_org_info) {
			case  3: return $guild != "" ? " $fancyColon {$guild}":" $fancyColon Not in a guild";
			case  2: return $guild != "" ? " $fancyColon {$guild} ({$guild_rank})":" $fancyColon Not in a guild";
			case  1: return $guild != "" ? " $fancyColon {$guild_rank}":"";
			default: return "";
		}
	}

	public function get_admin_info($name, $fancyColon) {
		if ($this->setting->get("online_admin") != 1) {
			return "";
		}

		switch ($this->accessLevel->getAccessLevelForCharacter($name)) {
			case 'superadmin': return " $fancyColon <red>SuperAdmin<end>";
			case 'admin'     : return " $fancyColon <red>Admin<end>";
			case 'mod'       : return " $fancyColon <green>Mod<end>";
			case 'rl'        : return " $fancyColon <orange>RL<end>";
		}
	}

	public function get_afk_info($afk, $fancyColon) {
		switch ($afk) {
			case       "": return "";
			case "kiting": return " $fancyColon <red>KITING<end>";
			case      "1": return " $fancyColon <red>AFK<end>";
			default      : return " $fancyColon <red>AFK - {$afk}<end>";
		}
	}

	public function get_alt_char_info($name, $fancyColon) {
		$altinfo = $this->alts->get_alt_info($name);

		if (count($altinfo->alts) > 0) {
			$alt = " $fancyColon <a href='chatcmd:///tell <myname> alts {$name}'>".($altinfo->main == $name ? "Alts":"Alt of {$altinfo->main}")."</a>";
		}
		return $alt;
	}
}

?>
