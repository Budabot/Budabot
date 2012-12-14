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
	public $settingManager;
	
	/** @Inject */
	public $accessManager;
	
	/** @Inject */
	public $buddylistManager;
	
	/** @Inject */
	public $altsController;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Logger */
	public $logger;
	
	private $instances = array();
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "online");
		
		$this->settingManager->add($this->moduleName, "online_expire", "How long to wait before clearing online list", "edit", "time", "15m", "2m;5m;10m;15m;20m", '', "mod");
		$this->settingManager->add($this->moduleName, "chatlist_tell", "Mode for Chatlist Cmd in tells", "edit", "options", "1", "Shows online privatechat members;Shows online guild members", "1;0");
		$this->settingManager->add($this->moduleName, "fancy_online", "Show fancy delimiters on the online display", "edit", "options", "1", "true;false", "1;0");
		$this->settingManager->add($this->moduleName, "icon_fancy_online", "Show profession icons in the online display", "edit", "options", "1", "true;false", "1;0");
		$this->settingManager->add($this->moduleName, "online_group_by", "How to group online list", "edit", "options", "profession", "profession;guild");
		$this->settingManager->add($this->moduleName, "online_show_org_guild", "Show org/rank for players in guild channel", "edit", "options", "1", "Show org and rank;Show rank only;Show org only;Show no org info", "2;1;3;0");
		$this->settingManager->add($this->moduleName, "online_show_org_priv", "Show org/rank for players in private channel", "edit", "options", "2", "Show org and rank;Show rank only;Show org only;Show no org info", "2;1;3;0");
		$this->settingManager->add($this->moduleName, "online_colorful", "Use fancy coloring for online list", "edit", "options", "1", "true;false", "1;0");
		$this->settingManager->add($this->moduleName, "online_admin", "Show admin levels in online list", "edit", "options", "0", "true;false", "1;0");
	}
	
	public function register($instance) {
		$this->instances []= $instance;
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
			$this->addPlayerToOnlineList($sender, $this->chatBot->vars['guild'], 'guild');
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
					case 'guild':
						$guildArray []= $row->name;
						break;
					case 'priv':
						$privArray []= $row->name;
						break;
					case 'irc':
						$ircArray []= $row->name;
						break;
					default:
						$this->logger->log("WARN", "ONLINE_MODULE", "Unknown channel type: '$row->channel_type'. Expected: 'guild', 'priv' or 'irc'");
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

			$sql = "DELETE FROM `online` WHERE (`dt` < ? AND added_by = '<myname>') OR (`dt` < ?)";
			$this->db->exec($sql, $time, ($time - $this->settingManager->get('online_expire')));
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
				list($time, $reason) = explode('|', $row->afk);
				$timeString = $this->util->unixtime_to_readable(time() - $time);
				// $sender is back
				$this->db->exec("UPDATE online SET `afk` = '' WHERE `name` = ? AND added_by = '<myname>' AND channel_type = ?", $sender, $type);
				$msg = "<highlight>{$sender}<end> is back after $timeString.";
				
				if ('priv' == $type) {
					$this->chatBot->sendPrivate($msg);
				} else if ('guild' == $type) {
					$this->chatBot->sendGuild($msg);
				}
			}
		}
	}
	
	public function afk($sender, $message, $type) {
		if (preg_match("/^.?afk$/i", $message)) {
			$reason = time();
			$this->db->exec("UPDATE online SET `afk` = ? WHERE `name` = ? AND added_by = '<myname>' AND channel_type = ?", $reason, $sender, $type);
			$msg = "<highlight>$sender<end> is now AFK.";
		} else if (preg_match("/^.?brb(.*)$/i", $message, $arr)) {
			$reason = time() . '|brb';
			$this->db->exec("UPDATE online SET `afk` = ? WHERE `name` = ? AND added_by = '<myname>' AND channel_type = ?", $reason, $sender, $type);
			$msg = "<highlight>$sender<end> is now AFK.";
		} else if (preg_match("/^.?afk (.*)$/i", $message, $arr)) {
			$reason = time() . '|' . $arr[1];
			$this->db->exec("UPDATE online SET `afk` = ? WHERE `name` = ? AND added_by = '<myname>' AND channel_type = ?", $reason, $sender, $type);
			$msg = "<highlight>$sender<end> is now AFK.";
		}

		if ('' != $msg) {
			if ('priv' == $type) {
				$this->chatBot->sendPrivate($msg);
			} else if ('guild' == $type) {
				$this->chatBot->sendGuild($msg);
			}
			
			// if 'afk' was used as a command, throw StopExecutionException to prevent
			// normal command handling to occur
			if ($message[0] == $this->settingManager->get('symbol')) {
				throw new StopExecutionException();
			}
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

		if ($this->settingManager->get('online_group_by') == 'profession') {
			$order_by = "ORDER BY `profession`, `level` DESC";
		} else if ($this->settingManager->get('online_group_by') == 'guild') {
			$order_by = "ORDER BY `channel` ASC, `name` ASC";
		}

		$blob = '';

		// Guild Channel Part
		$data = $this->db->query("SELECT p.*, o.name, o.channel, o.afk FROM `online` o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE o.channel_type = 'guild' {$prof_query} {$order_by}");
		$numguild = count($data);

		if ($numguild >= 1) {
			$blob .= "<header2>Guild Channel ($numguild)<end>\n";

			// create the list with alts shown
			$blob .= $this->createList($data, true, $this->settingManager->get("online_show_org_guild"));
		}

		// Private Channel Part
		$data = $this->db->query("SELECT p.*, o.name, o.channel, o.afk FROM `online` o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE o.channel_type = 'priv' {$prof_query} {$order_by}");
		$numguest = count($data);

		if ($numguest >= 1) {
			if ($numguild >= 1) {
				$blob .= "\n\n";
			}
			$blob .= "<header2>Private Channel ($numguest)<end>\n";

			// create the list of guests, without showing alts
			$blob .= $this->createList($data, true, $this->settingManager->get("online_show_org_priv"));
		}
		
		$numonline = $numguild + $numguest;

		// IRC/BBIN part
		forEach ($this->instances as $instance) {
			list($num, $window) = $instance->getOnlineList();
			$numonline += $num;
			$blob .= $window;
		}

		$msg .= "Online ($numonline)";

		return array($numonline, $msg, $blob);
	}

	public function createList(&$data, $show_alts, $show_org_info) {
		if ($this->settingManager->get('online_group_by') == 'profession') {
			return $this->createListByProfession($data, $show_alts, $show_org_info);
		} else if ($this->settingManager->get('online_group_by') == 'guild') {
			return $this->createListByChannel($data, $show_alts, $show_org_info);
		}
	}

	public function createListByChannel(&$data, $show_alts, $show_org_info) {
		//Colorful temporary var settings (avoid a mess of if statements later in the function)
		$fancyColon = ($this->settingManager->get("online_colorful") == "1") ? "<highlight>::<end>":"::";

		$blob = '';
		forEach ($data as $row) {
			$name = $this->text->make_chatcmd($row->name, "/tell $row->name");

			if ($current_channel != $row->channel) {
				$current_channel = $row->channel;
				$blob .= "\n<tab><highlight>$current_channel<end>\n";
			}

			$afk = $this->get_afk_info($row->afk, $fancyColon);
			$alt = ($show_alts === true) ? $this->get_alt_char_info($row->name, $fancyColon) : "";

			switch ($row->profession) {
				case "":
					$blob .= "<tab><tab>$name - Unknown$alt\n";
					break;
				default:
					$admin = ($show_alts === true) ? $this->get_admin_info($row->name, $fancyColon) : "";
					$guild = $this->get_org_info($show_org_info, $fancyColon, $row->guild, $row->guild_rank);
					$blob .= "<tab><tab>$name (Lvl $row->level/<green>$row->ai_level<end>)$guild$afk$alt$admin\n";
			}
		}

		return $blob;
	}

	public function createListByProfession(&$data, $show_alts, $show_org_info) {
		//Colorful temporary var settings (avoid a mess of if statements later in the function)
		$fancyColon = ($this->settingManager->get("online_colorful") == "1") ? "<highlight>::<end>":"::";

		$current_profession = "";

		$blob = '';
		forEach ($data as $row) {
			if ($current_profession != $row->profession) {
				if ($this->settingManager->get("fancy_online") == 0) {
					// old style delimiters
					$blob .= "\n<tab><highlight>$row->profession<end>\n";
				} else {
					// fancy delimiters
					$blob .= "\n<img src=tdb://id:GFX_GUI_FRIENDLIST_SPLITTER>\n";

					if ($this->settingManager->get("icon_fancy_online") == 1) {
						if ($row->profession == "Adventurer") {
							$blob .= $this->text->make_image(84203);
						} else if ($row->profession == "Agent") {
							$blob .= $this->text->make_image(16186);
						} else if ($row->profession == "Bureaucrat") {
							$blob .= $this->text->make_image(296548);
						} else if ($row->profession == "Doctor") {
							$blob .= $this->text->make_image(44235);
						} else if ($row->profession == "Enforcer") {
							$blob .= $this->text->make_image(117926);
						} else if ($row->profession == "Engineer") {
							$blob .= $this->text->make_image(287091);
						} else if ($row->profession == "Fixer") {
							$blob .= $this->text->make_image(16300);
						} else if ($row->profession == "Keeper") {
							$blob .= $this->text->make_image(38911);
						} else if ($row->profession == "Martial Artist") {
							$blob .= $this->text->make_image(16289);
						} else if ($row->profession == "Meta-Physicist") {
							$blob .= $this->text->make_image(16308);
						} else if ($row->profession == "Nano-Technician") {
							$blob .= $this->text->make_image(45190);
						} else if ($row->profession == "Soldier") {
							$blob .= $this->text->make_image(16195);
						} else if ($row->profession == "Shade") {
							$blob .= $this->text->make_image(39290);
						} else if ($row->profession == "Trader") {
							$blob .= $this->text->make_image(118049);
						} else {
							$blob .= $this->text->make_image(46268);
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
		if ($this->settingManager->get("online_admin") != 1) {
			return "";
		}

		switch ($this->accessManager->getAccessLevelForCharacter($name)) {
			case 'superadmin': return " $fancyColon <red>SuperAdmin<end>";
			case 'admin'     : return " $fancyColon <red>Admin<end>";
			case 'mod'       : return " $fancyColon <green>Mod<end>";
			case 'rl'        : return " $fancyColon <orange>RL<end>";
		}
	}

	public function get_afk_info($afk, $fancyColon) {
		list($time, $reason) = explode("|", $afk);
		if (empty($afk)) {
			return '';
		} else if (empty($reason)) {
			$timeString = $this->util->unixtime_to_readable(time() - $time);
			return " $fancyColon <red>AFK for $timeString<end>";
		} else {
			$timeString = $this->util->unixtime_to_readable(time() - $time);
			return " $fancyColon <red>AFK for $timeString - {$reason}<end>";
		}
	}

	public function get_alt_char_info($name, $fancyColon) {
		$altinfo = $this->altsController->get_alt_info($name);

		if (count($altinfo->alts) > 0) {
			$altsLink = $this->text->make_chatcmd(($altinfo->main == $name ? "Alts":"Alt of {$altinfo->main}"), "/tell <myname> alts {$name}");
			$alt = " $fancyColon $altsLink";
		}
		return $alt;
	}
}

?>
