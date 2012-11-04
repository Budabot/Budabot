<?php
/**
 * Authors: 
 *  - Tyrence (RK2)
 *  - Mindrila (RK1)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'members', 
 *		accessLevel = 'all', 
 *		description = "Member list", 
 *		help        = 'private_channel.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'sm', 
 *		accessLevel = 'all', 
 *		description = "Shows who is in the private channel", 
 *		help        = 'private_channel.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'adduser', 
 *		accessLevel = 'guild', 
 *		description = "Adds a player to the members list", 
 *		help        = 'private_channel.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'remuser', 
 *		accessLevel = 'guild', 
 *		description = "Removes a player from the members list", 
 *		help        = 'private_channel.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'inviteuser', 
 *		accessLevel = 'guild', 
 *		description = "Invite players to the private channel", 
 *		help        = 'private_channel.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'kickuser', 
 *		accessLevel = 'guild', 
 *		description = "Kick players from the private channel", 
 *		help        = 'private_channel.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'autoinvite', 
 *		accessLevel = 'member', 
 *		description = "Enable or disable autoinvite", 
 *		help        = 'autoinvite.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'count', 
 *		accessLevel = 'all', 
 *		description = "Shows how many characters are in the private channel", 
 *		help        = 'count.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'kickall', 
 *		accessLevel = 'guild', 
 *		description = "Kicks all from the private channel", 
 *		help        = 'kickall.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'lock', 
 *		accessLevel = 'rl', 
 *		description = "Locks the private channel", 
 *		help        = 'lock.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'unlock', 
 *		accessLevel = 'rl', 
 *		description = "Unlocks the private channel", 
 *		help        = 'lock.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'accept', 
 *		accessLevel = 'mod', 
 *		description = "Accept a private channel invitation from another player", 
 *		help        = 'accept.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'join', 
 *		accessLevel = 'member', 
 *		description = "Join command for guests", 
 *		help        = 'private_channel.txt',
 *		channels    = 'guild msg'
 *	)
 *	@DefineCommand(
 *		command     = 'leave', 
 *		accessLevel = 'all', 
 *		description = "Enables Privatechat Kick", 
 *		help        = 'private_channel.txt',
 *		channels    = 'priv msg'
 *	)
 */
class PrivateChannelController {

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
	public $buddylistManager;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $alts;
	
	/** @Inject */
	public $accessLevel;
	
	/** @Inject */
	public $onlineController;
	
	/** @Inject */
	public $relayController;
	
	/** @Inject */
	public $timer;
	
	/** @Inject */
	public $playerManager;
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "private_chat");
		
		$this->setting->add($this->moduleName, "guest_color_channel", "Color for Private Channel relay(ChannelName)", "edit", "color", "<font color=#C3C3C3>");
		$this->setting->add($this->moduleName, "guest_color_guild", "Private Channel relay color in guild channel", "edit", "color", "<font color=#C3C3C3>");
		$this->setting->add($this->moduleName, "guest_color_guest", "Private Channel relay color in private channel", "edit", "color", "<font color=#C3C3C3>");
		$this->setting->add($this->moduleName, "guest_relay", "Relay the Private Channel with the Guild Channel", "edit", "options", "1", "true;false", "1;0");
		$this->setting->add($this->moduleName, "guest_relay_commands", "Relay commands and results from/to Private Channel", "edit", "options", "0", "true;false", "1;0");
		$this->setting->add($this->moduleName, "priv_status", "Private channel status", "edit", "options", "1", "open;closed", "1;0");
		$this->setting->add($this->moduleName, "priv_status_reason", "Reason for private channel status", "edit", "text", "none");
	}

	/**
	 * @HandlesCommand("members")
	 * @Matches("/^members$/i")
	 */
	public function membersCommand($message, $channel, $sender, $sendto, $args) {
		$data = $this->db->query("SELECT * FROM members_<myname> ORDER BY `name`");
		$autoguests = count($data);
		if ($autoguests != 0) {
			$list = '';
			forEach ($data as $row) {
				$online = $this->buddylistManager->is_online($row->name);
				if (isset($this->chatBot->chatlist[$row->name])) {
					$status = "(<green>Online and in channel<end>)";
				} else if ($online === 1) {
					$status = "(<green>Online<end>)";
				} else if ($online === 0) {
					$status = "(<red>Offline<end>)";
				} else {
					$status = "(<orange>Unknown<end>)";
				}

				$list .= "<tab>- $row->name {$status}\n";
			}

			$msg = $this->text->make_blob("Members ($autoguests)", $list);
			$sendto->reply($msg);
		} else {
			$sendto->reply("There are no members of this bot.");
		}
	}
	
	/**
	 * @HandlesCommand("sm")
	 * @Matches("/^sm$/i")
	 */
	public function smCommand($message, $channel, $sender, $sendto, $args) {
		if (count($this->chatBot->chatlist) > 0) {
			$msg = $this->getChatlist();
			$sendto->reply($msg);
		} else {
			$sendto->reply("No players are in the private channel.");
		}
	}
	
	public function getChatlist() {
		$sql = "SELECT p.*, o.name as name FROM online o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE `channel_type` = 'priv' AND added_by = '<myname>' ORDER BY `profession`, `level` DESC";
		$data = $this->db->query($sql);
		$numguest = count($data);

		$blob = '';
		forEach ($data as $row) {
			if ($row->profession == null) {
				$blob .= "<white>$row->name<white> - Unknown\n";
			} else {
				$blob .= "<white>$row->name - $row->level<end><green>/$row->ai_level<end><white> $row->profession, $row->guild<end>\n";
			}
		}

		return $this->text->make_blob("Chatlist ($numguest)", $blob);
	}
	
	/**
	 * @HandlesCommand("adduser")
	 * @Matches("/^adduser (.+)$/i")
	 */
	public function adduserCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$uid = $this->chatBot->get_uid($name);
		if (!$uid) {
			$msg = "Character <highlight>$name<end> does not exist.";
		} else {
			$data = $this->db->query("SELECT * FROM members_<myname> WHERE `name` = ?", $name);
			if (count($data) != 0) {
				$msg = "<highlight>$name<end> is already a member of this bot.";
			} else {
				$this->db->exec("INSERT INTO members_<myname> (`name`, `autoinv`) VALUES (?, ?)", $name, '1');
				$msg = "<highlight>$name<end> has been added as a member of this bot.";
			}

			// always add in case
			$this->buddylistManager->add($name, 'member');
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("remuser")
	 * @Matches("/^remuser (.+)$/i")
	 */
	public function remuserCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$uid = $this->chatBot->get_uid($name);
		if (!$uid) {
			$msg = "Character <highlight>{$name}<end> does not exist.";
		} else {
			$data = $this->db->query("SELECT * FROM members_<myname> WHERE `name` = ?", $name);
			if (count($data) == 0) {
				$msg = "<highlight>$name<end> is not a member of this bot.";
			} else {
				$this->db->exec("DELETE FROM members_<myname> WHERE `name` = ?", $name);
				$msg = "<highlight>$name<end> has been removed as a member of this bot.";
				$this->buddylistManager->remove($name, 'member');
			}
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("inviteuser")
	 * @Matches("/^inviteuser (.+)$/i")
	 */
	public function inviteuserCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$uid = $this->chatBot->get_uid($name);
		if ($this->chatBot->vars["name"] == $name) {
			$msg = "You cannot invite the bot to its own private channel.";
		} else if ($uid) {
			$msg = "Invited <highlight>$name<end> to this channel.";
			$this->chatBot->privategroup_kick($name);
			$this->chatBot->privategroup_invite($name);
			$msg2 = "You have been invited to the <highlight><myname><end> channel by <highlight>$sender<end>.";
			$this->chatBot->sendTell($msg2, $name);
		} else {
			$msg = "Character <highlight>{$name}<end> does not exist.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("kickuser")
	 * @Matches("/^kickuser (.+)$/i")
	 */
	public function kickuserCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$uid = $this->chatBot->get_uid($name);
		if ($uid) {
			if (isset($this->chatBot->chatlist[$name])) {
				$msg = "<highlight>$name<end> has been kicked from the private channel.";
			} else {
				$msg = "<highlight>$name<end> is not in the private channel.";
			}

			// we kick whether they are in the channel or not in case the channel list is bugged
			$this->chatBot->privategroup_kick($name);
		} else {
			$msg = "Character <highlight>{$name}<end> does not exist.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("autoinvite")
	 * @Matches("/^autoinvite (on|off)$/i")
	 */
	public function autoinviteCommand($message, $channel, $sender, $sendto, $args) {
		if ($args[1] == 'on') {
			$onOrOff = 1;
			$this->buddylistManager->add($sender, 'member');
		} else {
			$onOrOff = 0;
			$this->buddylistManager->remove($sender, 'member');
		}

		$data = $this->db->query("SELECT * FROM members_<myname> WHERE `name` = ?", $sender);
		if (count($data) == 0) {
			$msg = "You are not a member of this bot.";
		} else {
			$this->db->exec("UPDATE members_<myname> SET autoinv = ? WHERE name = ?", $onOrOff, $sender);
			$msg = "Your auto invite preference has been updated.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("count")
	 * @Matches("/^count (level|lvl)$/i")
	 */
	public function countLevelCommand($message, $channel, $sender, $sendto, $args) {
		$tl1 = 0;
		$tl2 = 0;
		$tl3 = 0;
		$tl4 = 0;
		$tl5 = 0;
		$tl6 = 0;
		$tl7 = 0;

		$data = $this->db->query("SELECT * FROM online o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE added_by = '<myname>' AND channel_type = 'priv'");
		$numonline = count($data);
		forEach ($data as $row) {
			if ($row->level > 1 && $row->level <= 14) {
				$tl1++;
			} else if ($row->level >= 15 && $row->level <= 49) {
				$tl2++;
			} else if ($row->level >= 50 && $row->level <= 99) {
				$tl3++;
			} else if ($row->level >= 100 && $row->level <= 149) {
				$tl4++;
			} else if ($row->level >= 150 && $row->level <= 189) {
				$tl5++;
			} else if ($row->level >= 190 && $row->level <= 204) {
				$tl6++;
			} else if ($row->level >= 205 && $row->level <= 220) {
				$tl7++;
			}
		}
		$msg = "<highlight>$numonline<end> in total: TL1 <highlight>$tl1<end>, TL2 <highlight>$tl2<end>, TL3 <highlight>$tl3<end>, TL4 <highlight>$tl4<end>, TL5 <highlight>$tl5<end>, TL6 <highlight>$tl6<end>, TL7 <highlight>$tl7<end>";
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("count")
	 * @Matches("/^count (all|prof)$/i")
	 */
	public function countProfessionCommand($message, $channel, $sender, $sendto, $args) {
		$online["Adventurer"] = 0;
		$online["Agent"] = 0;
		$online["Bureaucrat"] = 0;
		$online["Doctor"] = 0;
		$online["Enforcer"] = 0;
		$online["Engineer"] = 0;
		$online["Fixer"] = 0;
		$online["Keeper"] = 0;
		$online["Martial Artist"] = 0;
		$online["Meta-Physicist"] = 0;
		$online["Nano-Technician"] = 0;
		$online["Soldier"] = 0;
		$online["Trader"] = 0;
		$online["Shade"] = 0;

		$data = $this->db->query("SELECT count(*) AS count, profession FROM online o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE added_by = '<myname>' AND channel_type = 'priv' GROUP BY `profession`");
		$numonline = count($data);
		$msg = "<highlight>$numonline<end> in total: ";

		forEach ($data as $row) {
			$online[$row->profession] = $row->count;
		}

		/*
		forEach ($online as $prof => $count) {
			if ($count > 0) {
				$msg .= "<highlight>{$count}<end> {$prof}, ";
			}
		}
		*/

		$msg .= "<highlight>".$online['Adventurer']."<end> Adv, <highlight>".$online['Agent']."<end> Agent, <highlight>".$online['Bureaucrat']."<end> Crat, <highlight>".$online['Doctor']."<end> Doc, <highlight>".$online['Enforcer']."<end> Enf, <highlight>".$online['Engineer']."<end> Eng, <highlight>".$online['Fixer']."<end> Fix, <highlight>".$online['Keeper']."<end> Keeper, <highlight>".$online['Martial Artist']."<end> MA, <highlight>".$online['Meta-Physicist']."<end> MP, <highlight>".$online['Nano-Technician']."<end> NT, <highlight>".$online['Soldier']."<end> Sol, <highlight>".$online['Shade']."<end> Shade, <highlight>".$online['Trader']."<end> Trader";

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("count")
	 * @Matches("/^count org$/i")
	 */
	public function countOrganizationCommand($message, $channel, $sender, $sendto, $args) {
		$sql = "SELECT * FROM online WHERE added_by = '<myname>' AND channel_type = 'priv'";
		$data = $this->db->query($sql);
		$numonline = count($data);

		if ($numonline == 0) {
			$msg = "No players in channel.";
		} else {
			$sql = "SELECT `guild`, count(*) AS cnt, AVG(level) AS avg_level FROM online o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE added_by = '<myname>' AND channel_type = 'priv' GROUP BY `guild` ORDER BY `cnt` DESC, `avg_level` DESC";
			$data = $this->db->query($sql);
			$numorgs = count($data);

			$blob = '';
			forEach ($data as $row) {
				$guild = '(none)';
				if ($row->guild != '') {
					$guild = $row->guild;
				}
				$percent = round($row->cnt / $numonline, 2) * 100;
				$avg_level = round($row->avg_level, 1);
				$blob .= "{$percent}% {$guild} - {$row->cnt} member(s), average level {$avg_level}\n";
			}

			$msg = $this->text->make_blob("Organizations ($numorgs)", $blob);
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("count")
	 * @Matches("/^count (.*)$/i")
	 */
	public function countCommand($message, $channel, $sender, $sendto, $args) {
		$prof = $this->util->get_profession_name($args[1]);
		if ($prof == '') {
			$msg = "Please choose one of these professions: adv, agent, crat, doc, enf, eng, fix, keep, ma, mp, nt, sol, shade, trader or all";
		} else {
			$data = $this->db->query("SELECT * FROM online o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE added_by = '<myname>' AND channel_type = 'priv' AND `profession` = ? ORDER BY `level`", $prof);
			$numonline = count($data);
			$msg = "<highlight>$numonline<end> $prof:";

			forEach ($data as $row) {
				if ($row->afk != "") {
					$afk = "<red>*AFK*<end>";
				} else {
					$afk = "";
				}
				$msg .= " [<highlight>$row->name<end> - ".$row->level.$afk."]";
			}
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("kickall")
	 * @Matches("/^kickall$/i")
	 */
	public function kickallCommand($message, $channel, $sender, $sendto, $args) {
		$msg = "Everyone will be kicked from this channel in 10 seconds. [by <highlight>$sender<end>]";
		$this->chatBot->sendPrivate($msg);
		$this->timer->callLater(10, array($this->chatBot, 'privategroup_kick_all'));
	}
	
	/**
	 * @HandlesCommand("lock")
	 * @Matches("/^lock$/i")
	 * @Matches("/^lock (.+)$/i")
	 */
	public function lockCommand($message, $channel, $sender, $sendto, $args) {
		if ($this->setting->get("priv_status") == "0") {
			$msg = "Private channel is already locked.";
			$sendto->reply($msg);
			return;
		}

		if (count($args) == 2) {
			$reason = $args[1];
			$msg = "The private channel has been locked by <highlight>$sender<end> - Reason: <highlight>$reason<end>.";
			$this->setting->save("priv_status_reason", $reason);
		} else {
			$msg = "The private channel has been locked by <highlight>$sender<end>.";
		}
		$this->chatBot->sendPrivate($msg);
		
		if ($channel != "priv") {
			$msg = "You have locked the private channel.";
			$sendto->reply($msg);
		}

		$this->setting->save("priv_status", "0");
	}
	
	/**
	 * @HandlesCommand("unlock")
	 * @Matches("/^unlock$/i")
	 */
	public function unlockCommand($message, $channel, $sender, $sendto, $args) {
		if ($this->setting->get("priv_status") == "1") {
			$msg = "Private channel is already open.";
			$sendto->reply($msg);
			return;
		}

		$msg = "The private channel has been opened by <highlight>$sender<end>.";
		$this->chatBot->sendPrivate($msg);
		if ($channel != "priv") {
			$msg = "You have opened the private channel.";
			$this->chatBot->sendTell($msg, $sender);
		}

		$this->setting->save("priv_status", "1");
		$this->setting->save("priv_status_reason", "none");
	}
	
	/**
	 * @HandlesCommand("accept")
	 * @Matches("/^accept (.+)/i")
	 */
	public function acceptCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		if (!$this->chatBot->get_uid($name)) {
			$msg = "Character <highlight>$name<end> does not exist.";
		} else {
			$this->chatBot->privategroup_join($name);
			$msg = "Accepted private channel invitation from <highlight>$name<end>.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("join")
	 * @Matches("/^join$/i")
	 */
	public function joinCommand($message, $channel, $sender, $sendto, $args) {
		// if the channel is locked, only raidleaders or higher can join manually
		if ($this->setting->get("priv_status") == "0" && !$this->accessLevel->checkAccess($sender, 'raidleader')) {
			if ($this->setting->get("priv_status_reason") != "none") {
				$sendto->reply("The private channel is locked. Reason: " . $this->setting->get("priv_status_reason"));
			} else {
				$sendto->reply("The private channel is locked.");
			}
			return;
		}

		$this->chatBot->privategroup_kick($sender);
		$this->chatBot->privategroup_invite($sender);
	}
	
	/**
	 * @HandlesCommand("leave")
	 * @Matches("/^leave$/i")
	 */
	public function leaveCommand($message, $channel, $sender, $sendto, $args) {
		$this->chatBot->privategroup_kick($sender);
	}
	
	/**
	 * @Event("connect")
	 * @Description("Adds all members as buddies who have auto-invite enabled")
	 */
	public function connectEvent($eventObj) {
		$sql = "SELECT name FROM members_<myname> WHERE autoinv = 1";
		$data = $this->db->query($sql);
		forEach ($data as $row) {
			$this->buddylistManager->add($row->name, 'member');
		}
	}
	
	/**
	 * @Event("guild")
	 * @Description("Private channel relay from guild channel")
	 */
	public function relayPrivateChannelEvent($eventObj) {
		$sender = $eventObj->sender;
		$message = $eventObj->message;
	
		// Check if the private channel relay is enabled
		if ($this->setting->get("guest_relay") != 1) {
			return;
		}

		// Check that it's not a command or if it is a command, check that guest_relay_commands is not disabled
		if ($message[0] == $this->setting->get("symbol") && $this->setting->get("guest_relay_commands") != 1) {
			return;
		}

		$guest_color_channel = $this->setting->get("guest_color_channel");
		$guest_color_guest = $this->setting->get("guest_color_guest");
		$guest_color_guild = $this->setting->get("guest_color_guild");

		if (count($this->chatBot->chatlist) > 0) {
			//Relay the message to the private channel if there is at least 1 char in private channel
			$guildNameForRelay = $this->relayController->getGuildAbbreviation();
			if (!$this->util->isValidSender($sender)) {
				// for relaying city alien raid messages where $sender == -1
				$msg = "<end>{$guest_color_channel}[$guildNameForRelay]<end> {$guest_color_guest}{$message}<end>";
			} else {
				$msg = "<end>{$guest_color_channel}[$guildNameForRelay]<end> ".$this->text->make_userlink($sender).": {$guest_color_guest}{$message}<end>";
			}
			$this->chatBot->sendPrivate($msg, true);
		}
	}
	
	/**
	 * @Event("priv")
	 * @Description("Guild channel relay from priv channel")
	 */
	public function relayGuildChannelEvent($eventObj) {
		$sender = $eventObj->sender;
		$message = $eventObj->message;
		
		// Check if the private channel relay is enabled
		if ($this->setting->get("guest_relay") != 1) {
			return;
		}

		// Check that it's not a command or if it is a command, check that guest_relay_commands is not disabled
		if ($message[0] == $this->setting->get("symbol") && $this->setting->get("guest_relay_commands") != 1) {
			return;
		}

		$guest_color_channel = $this->setting->get("guest_color_channel");
		$guest_color_guest = $this->setting->get("guest_color_guest");
		$guest_color_guild = $this->setting->get("guest_color_guild");

		//Relay the message to the guild channel
		$msg = "<end>{$guest_color_channel}[Guest]<end> ".$this->text->make_userlink($sender).": {$guest_color_guild}{$message}<end>";
		$this->chatBot->sendGuild($msg, true);
	}
	
	/**
	 * @Event("logOn")
	 * @Description("Auto-invite members on logon")
	 */
	public function logonAutoinviteEvent($eventObj) {
		$sender = $eventObj->sender;
		$data = $this->db->query("SELECT * FROM members_<myname> WHERE name = ? AND autoinv = ?", $sender, '1');
		if (count($data) != 0) {
			$msg = "You have been auto invited to the <highlight><myname><end> channel.";
			$this->chatBot->privategroup_invite($sender);
			$this->chatBot->sendTell($msg, $sender);
		}
	}
	
	/**
	 * @Event("joinPriv")
	 * @Description("Displays a message when a character joins the private channel")
	 */
	public function joinPrivateChannelMessageEvent($eventObj) {
		$sender = $eventObj->sender;
		$whois = $this->playerManager->get_by_name($sender);

		$altInfo = $this->alts->get_alt_info($sender);

		if ($whois !== null) {
			if (count($altInfo->alts) > 0) {
				$msg = $this->playerManager->get_info($whois) . " has joined the private channel. " . $altInfo->get_alts_blob(false, true);
			} else {
				$msg = $this->playerManager->get_info($whois) . " has joined the private channel.";
			}
		} else {
			if (count($altInfo->alts) > 0) {
				$msg .= "$sender has joined the private channel. " . $altInfo->get_alts_blob(false, true);
			} else {
				$msg = "$sender has joined the private channel.";
			}
		}

		if ($this->setting->get("guest_relay") == 1) {
			$this->chatBot->sendGuild($msg, true);
		}
		$this->chatBot->sendPrivate($msg, true);
	}
	
	/**
	 * @Event("leavePriv")
	 * @Description("Displays a message when a character leaves the private channel")
	 */
	public function leavePrivateChannelMessageEvent($eventObj) {
		$sender = $eventObj->sender;
		$msg = "$sender has left the private channel";

		if ($this->setting->get("guest_relay") == 1) {
			$this->chatBot->sendGuild($msg, true);
		}

		// don't need this since the client tells you when someone leaves and we don't add any additional information
		//$this->chatBot->sendPrivate($msg, true);
	}
	
	/**
	 * @Event("joinPriv")
	 * @Description("Updates the database when a character joins the private channel")
	 */
	public function joinPrivateChannelRecordEvent($eventObj) {
		$sender = $eventObj->sender;
		$this->onlineController->addPlayerToOnlineList($sender, $this->chatBot->vars['guild'] . ' Guests', 'priv');
	}
	
	/**
	 * @Event("leavePriv")
	 * @Description("Updates the database when a character leaves the private channel")
	 */
	public function leavePrivateChannelRecordEvent($eventObj) {
		$sender = $eventObj->sender;
		$this->onlineController->removePlayerFromOnlineList($sender, 'priv');
	}
	
	/**
	 * @Event("joinPriv")
	 * @Description("Sends the online list to people as they join the private channel")
	 */
	public function joinPrivateChannelShowOnlineEvent($eventObj) {
		$sender = $eventObj->sender;
		$msg = "";
		list($numonline, $msg, $blob) = $this->onlineController->get_online_list();
		if ($numonline != 0) {
			$msg = $this->text->make_blob($msg, $blob);
			$this->chatBot->sendTell($msg, $sender);
		} else {
			$this->chatBot->sendTell($msg, $sender);
		}
	}
	
	/**
	 * @Event("joinPriv")
	 * @Description("Sends the chatlist to people as they join the private channel")
	 * @DefaultStatus("0")
	 */
	public function joinPrivateChannelShowChatlistEvent($eventObj) {
		if (count($this->chatBot->chatlist) > 0) {
			$msg = $this->getChatlist();
			$this->chatBot->sendTell($msg, $eventObj->sender);
		}
	}
}

