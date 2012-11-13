<?php
/**
 * Authors: 
 *  - Tyrence (RK2)
 *  - Mindrila (RK1)
 *  - Derroylo (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = "logon", 
 *		accessLevel = "guild", 
 *		description = "Set logon message", 
 *		help        = "logon_msg.txt"
 *	)
 *	@DefineCommand(
 *		command     = "logoff", 
 *		accessLevel = "guild", 
 *		description = "Set logoff message", 
 *		help        = "logoff_msg.txt"
 *	)
 *	@DefineCommand(
 *		command     = "logonadmin", 
 *		accessLevel = "mod", 
 *		description = "Admin command for editing another player's logon message", 
 *		help        = "logonadmin.txt"
 *	)
 *	@DefineCommand(
 *		command     = "logoffadmin", 
 *		accessLevel = "mod", 
 *		description = "Admin command for editing another player's logoff message", 
 *		help        = "logoffadmin.txt"
 *	)
 *	@DefineCommand(
 *		command     = "lastseen", 
 *		accessLevel = "guild", 
 *		description = "Shows the last logoff time of a player", 
 *		help        = "lastseen.txt"
 *	)
 *	@DefineCommand(
 *		command     = "recentseen", 
 *		accessLevel = "guild", 
 *		description = "Shows recent org members who logged on", 
 *		help        = "recentseen.txt"
 *	)
 *	@DefineCommand(
 *		command     = "tellall", 
 *		accessLevel = "rl", 
 *		description = "Sends a tell to all online guild members", 
 *		help        = "tellall.txt"
 *	)
 *	@DefineCommand(
 *		command     = "notify", 
 *		accessLevel = "mod", 
 *		description = "Add a player to the notify list manually", 
 *		help        = "notify.txt"
 *	)
 *	@DefineCommand(
 *		command     = "inactivemem", 
 *		accessLevel = "guild", 
 *		description = "Check for inactive members", 
 *		help        = "inactivemem.txt"
 *	)
 *	@DefineCommand(
 *		command     = "updateorg", 
 *		accessLevel = "mod", 
 *		description = "Force an update of the org roster", 
 *		help        = "updateorg.txt"
 *	)
 */
class GuildController {

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
	public $buddylistManager;
	
	/** @Inject */
	public $playerManager;
	
	/** @Inject */
	public $guildManager;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $alts;
	
	/** @Inject */
	public $preferences;
	
	/** @Logger */
	public $logger;
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "org_members");
		
		$this->settingManager->add($this->moduleName, "max_logon_msg_size", "Maximum characters a logon message can have", "edit", "number", "200", "100;200;300;400", '', "mod");
		$this->settingManager->add($this->moduleName, "max_logoff_msg_size", "Maximum characters a logoff message can have", "edit", "number", "200", "100;200;300;400", '', "mod");
		$this->settingManager->add($this->moduleName, "first_and_last_alt_only", "Show logon/logoff for first/last alt only", "edit", "options", "0", "true;false", "1;0");
		
		unset($this->chatBot->guildmembers);
		$data = $this->db->query("SELECT * FROM org_members_<myname> o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE mode <> 'del'");
		if (count($data) != 0) {
			forEach ($data as $row) {
				$this->chatBot->guildmembers[$row->name] = $row->guild_rank_id;
			}
		}
	}

	/**
	 * @HandlesCommand("logon")
	 * @Matches("/^logon$/i")
	 */
	public function logonMessageShowCommand($message, $channel, $sender, $sendto, $args) {
		$logon_msg = $this->preferences->get($sender, 'logon_msg');

		if ($logon_msg === false || $logon_msg == '') {
			$msg = "Your logon message has not been set.";
		} else {
			$msg = "{$sender} logon: {$logon_msg}";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("logon")
	 * @Matches("/^logon (.+)$/i")
	 */
	public function logonMessageSetCommand($message, $channel, $sender, $sendto, $args) {
		$logon_msg = $args[1];

		if ($logon_msg == 'clear') {
			$this->preferences->save($sender, 'logon_msg', '');
			$msg = "Your logon message has been cleared.";
		} else if (strlen($logon_msg) <= $this->settingManager->get('max_logon_msg_size')) {
			$this->preferences->save($sender, 'logon_msg', $logon_msg);
			$msg = "Your logon message has been set.";
		} else {
			$msg = "Your logon message is too large. Your logon message may contain a maximum of " . $this->settingManager->get('max_logon_msg_size') . " characters.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("logoff")
	 * @Matches("/^logoff$/i")
	 */
	public function logoffMessageShowCommand($message, $channel, $sender, $sendto, $args) {
		$logoff_msg = $this->preferences->get($sender, 'logoff_msg');

		if ($logoff_msg === false || $logoff_msg == '') {
			$msg = "Your logoff message has not been set.";
		} else {
			$msg = "{$sender} logoff: {$logoff_msg}";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("logoff")
	 * @Matches("/^logoff (.+)$/i")
	 */
	public function logoffMessageSetCommand($message, $channel, $sender, $sendto, $args) {
		$logoff_msg = $args[1];

		if ($logoff_msg == 'clear') {
			$this->preferences->save($sender, 'logoff_msg', '');
			$msg = "Your logoff message has been cleared.";
		} else if (strlen($logoff_msg) <= $this->settingManager->get('max_logoff_msg_size')) {
			$this->preferences->save($sender, 'logoff_msg', $logoff_msg);
			$msg = "Your logoff message has been set.";
		} else {
			$msg = "Your logoff message is too large. Your logoff message may contain a maximum of " . $this->settingManager->get('max_logoff_msg_size') . " characters.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("logonadmin")
	 * @Matches("/^logonadmin ([a-zA-Z0-9-]+)$/i")
	 */
	public function logonadminMessageShowCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$logon_msg = $this->preferences->get($name, 'logon_msg');

		if ($logon_msg === false || $logon_msg == '') {
			$msg = "The logon message for $name has not been set.";
		} else {
			$msg = "{$name} logon: {$logon_msg}";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("logonadmin")
	 * @Matches("/^logonadmin ([a-zA-Z0-9-]+) (.+)$/i")
	 */
	public function logonadminMessageSetCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$logon_msg = $args[2];

		if ($logon_msg == 'clear') {
			$this->preferences->save($name, 'logon_msg', '');
			$msg = "The logon message for $name has been cleared.";
		} else if (strlen($logon_msg) <= $this->settingManager->get('max_logon_msg_size')) {
			$this->preferences->save($name, 'logon_msg', $logon_msg);
			$msg = "The logon message for $name has been set.";
		} else {
			$msg = "The logon message is too large. The logon message may contain a maximum of " . $this->settingManager->get('max_logon_msg_size') . " characters.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("logoffadmin")
	 * @Matches("/^logoffadmin ([a-zA-Z0-9-]+)$/i")
	 */
	public function logoffadminMessageShowCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$logoff_msg = $this->preferences->get($name, 'logoff_msg');

		if ($logoff_msg === false || $logoff_msg == '') {
			$msg = "The logoff message for $name has not been set.";
		} else {
			$msg = "{$name} logoff: {$logoff_msg}";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("logoffadmin")
	 * @Matches("/^logoffadmin ([a-zA-Z0-9-]+) (.+)$/i")
	 */
	public function logoffadminMessageSetCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$logoff_msg = $args[2];

		if ($logoff_msg == 'clear') {
			$this->preferences->save($name, 'logoff_msg', '');
			$msg = "The logoff message for $name has been cleared.";
		} else if (strlen($logoff_msg) <= $this->settingManager->get('max_logoff_msg_size')) {
			$this->preferences->save($name, 'logoff_msg', $logoff_msg);
			$msg = "The logoff message for $name has been set.";
		} else {
			$msg = "The logoff message is too large. The logoff message may contain a maximum of " . $this->settingManager->get('max_logoff_msg_size') . " characters.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("lastseen")
	 * @Matches("/^lastseen (.+)$/i")
	 */
	public function lastseenCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$uid = $this->chatBot->get_uid($name);
		if (!$uid) {
			$msg = "player <highlight>$name<end> does not exist.";
		} else {
			$altInfo = $this->alts->get_alt_info($name);
			$onlineAlts = $altInfo->get_online_alts();
			if (count($onlineAlts) > 0) {
				$msg = "This player is currently <green>online<end> as " . implode(', ', $onlineAlts) . ".";
			} else {
				$namesSql = '';
				forEach ($altInfo->get_all_alts() as $alt) {
					if ($namesSql) {
						$namesSql .= ", ";
					}
					$namesSql .= "'$alt'";
				}
				$row = $this->db->queryRow("SELECT * FROM org_members_<myname> WHERE `name` IN ($namesSql) AND `mode` != 'del' ORDER BY logged_off DESC");

				if ($row !== null) {
					if ($row->logged_off == 0) {
						$msg = "<highlight>$name<end> has never logged on.";
					} else {
						$msg = "Last seen at " . $this->util->date($row->logged_off) . " on <highlight>" . $row->name . "<end>.";
					}
				} else {
					$msg = "This player is not a member of the org.";
				}
			}
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("recentseen")
	 * @Matches("/^recentseen ([a-z0-9]+)/i")
	 */
	public function recentseenCommand($message, $channel, $sender, $sendto, $args) {
		if (!$this->isGuildBot()) {
			$sendto->reply("The bot must be in an org.");
			return;
		}

		$time = $this->util->parseTime($args[1]);
		if ($time < 1) {
			$msg = "You must enter a valid time parameter.";
			$sendto->reply($msg);
			return;
		}

		$timeString = $this->util->unixtime_to_readable($time, false);
		$time = time() - $time;

		$data = $this->db->query("SELECT case when a.main is null then o.name else a.main end as main ,o.logged_off,o.name FROM org_members_<myname> o LEFT JOIN alts a ON o.name = a.alt WHERE `mode` != 'del'AND `logged_off` > ? ORDER BY 1, o.logged_off desc, o.name", $time); 


		if (count($data) == 0) {
			$sendto->reply("No members recorded.");
			return;
		}

		$numinactive = 0;
		$highlight = 0;
	  
		$blob = "Org members who have logged off since <highlight>{$timeString}<end> ago.\n\n";
		
		$prevtoon = '';
		forEach ($data as $row) {
			if ($row->main != $prevtoon) {
				$prevtoon = $row->main;
				$numrecentcount++;
				$alts = $this->text->make_chatcmd("Alts", "/tell <myname> alts {$row->main}");
				$logged = $row->logged_off;
				$lasttoon = $row->name;

				$player = $row->main." [{$alts}]\nLast seen as [$lasttoon] on " . $this->util->date($logged) . "\n\n";
				if ($highlight == 1) {
					$blob .= "<highlight>$player<end>";
					$highlight = 0;
				} else {
					$blob .= $player;
					$highlight = 1;
				}
			} 
		}
		$msg = $this->text->make_blob("$numrecentcount Recent seen org members", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("tellall")
	 * @Matches("/^tellall (.+)$/i")
	 */
	public function tellallCommand($message, $channel, $sender, $sendto, $args) {
		$tellmsg = $args[1];
		$data = $this->db->query("SELECT name FROM online WHERE channel_type = 'guild'");
		forEach ($data as $row) {
			$this->chatBot->sendTell("Message from $sender: <yellow>$tellmsg<end>", $row->name);
		}

		$sendto->reply("Your message has been sent to all online org members.");
	}
	
	/**
	 * @HandlesCommand("notify")
	 * @Matches("/^notify (on|add) (.+)$/i")
	 */
	public function notifyAddCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[2]));
		$uid = $this->chatBot->get_uid($name);

		if (!$uid) {
			$msg = "<highlight>{$name}<end> does not exist.";
			$sendto->reply($msg);
			return;
		}

		$row = $this->db->queryRow("SELECT mode FROM org_members_<myname> WHERE `name` = ?", $name);

		if ($row !== null && $row->mode != "del") {
			$msg = "<highlight>{$name}<end> is already on the Notify list.";
		} else {
			if ($row === null) {
				$this->db->exec("INSERT INTO org_members_<myname> (`name`, `mode`) VALUES (?, 'add')", $name);
			} else {
				$this->db->exec("UPDATE org_members_<myname> SET `mode` = 'add' WHERE `name` = ?", $name);
			}
			$this->db->exec("INSERT INTO online (`name`, `channel`, `channel_type`, `added_by`, `dt`) VALUES (?, '<myguild>', 'guild', '<myname>', ?)", $name, time());
			$this->buddylistManager->add($name, 'org');
			$this->chatBot->guildmembers[$name] = 6;
			$msg = "<highlight>{$name}<end> has been added to the Notify list.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("notify")
	 * @Matches("/^notify (off|rem) (.+)$/i")
	 */
	public function notifyRemoveCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[2]));
		$uid = $this->chatBot->get_uid($name);

		if (!$uid) {
			$msg = "<highlight>{$name}<end> does not exist.";
			$sendto->reply($msg);
			return;
		}

		$row = $this->db->queryRow("SELECT mode FROM org_members_<myname> WHERE `name` = ?", $name);

		if ($row === null) {
			$msg = "<highlight>{$name}<end> is not on the guild roster.";
		} else if ($row->mode == "del") {
			$msg = "<highlight>{$name}<end> has already been removed from the Notify list.";
		} else {
			$this->db->exec("UPDATE org_members_<myname> SET `mode` = 'del' WHERE `name` = ?", $name);
			$this->db->exec("DELETE FROM online WHERE `name` = ? AND `channel_type` = 'guild' AND added_by = '<myname>'", $name);
			$this->buddylistManager->remove($name, 'org');
			unset($this->chatBot->guildmembers[$name]);
			$msg = "Removed <highlight>{$name}<end> from the Notify list.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("inactivemem")
	 * @Matches("/^inactivemem ([a-z0-9]+)/i")
	 */
	public function inactivememCommand($message, $channel, $sender, $sendto, $args) {
		if (!$this->isGuildBot()) {
			$sendto->reply("The bot must be in an org.");
			return;
		}

		$time = $this->util->parseTime($args[1]);
		if ($time < 1) {
			$msg = "You must enter a valid time parameter.";
			$sendto->reply($msg);
			return;
		}

		$timeString = $this->util->unixtime_to_readable($time, false);
		$time = time() - $time;

		$data = $this->db->query("SELECT * FROM org_members_<myname> o LEFT JOIN alts a ON o.name = a.alt WHERE `mode` != 'del' AND `logged_off` < ?  ORDER BY o.name", $time);

		if (count($data) == 0) {
			$sendto->reply("No members recorded.");
			return;
		}

		$numinactive = 0;
		$highlight = 0;

		$blob = "Org members who have not logged off since <highlight>{$timeString}<end> ago.\n\n";

		forEach ($data as $row) {
			$logged = 0;
			$main = $row->main;
			if ($row->main != "") {
				$data1 = $this->db->query("SELECT * FROM alts a JOIN org_members_<myname> o ON a.alt = o.name WHERE `main` = ?", $row->main);
				forEach ($data1 as $row1) {
					if ($row1->logged_off > $time) {
						continue 2;
					}

					if ($row1->logged_off > $logged) {
						$logged = $row1->logged_off;
						$lasttoon = $row1->name;
					}
				}
			}

			$numinactive++;
			$alts = $this->text->make_chatcmd("Alts", "/tell <myname> alts {$row->name}");
			$logged = $row->logged_off;
			$lasttoon = $row->name;

			$player = $row->name."; Main: $main; [{$alts}]\nLast seen on [$lasttoon] on " . $this->util->date($logged) . "\n\n";
			if ($highlight == 1) {
				$blob .= "<highlight>$player<end>";
				$highlight = 0;
			} else {
				$blob .= $player;
				$highlight = 1;
			}
		}
		$msg = $this->text->make_blob("$numinactive Inactive Org Members", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("updateorg")
	 * @Matches("/^updateorg$/i")
	 */
	public function updateorgCommand($message, $channel, $sender, $sendto, $args) {
		$force_update = true;
		$sendto->reply("Starting Roster update");
		$this->updateOrgRoster();
		$sendto->reply("Finished Roster update");
	}
	
	public function updateOrgRoster() {
		if ($this->isGuildBot()) {
			$this->logger->log('INFO', "Starting Roster update");

			// Get the guild info
			$org = $this->guildManager->get_by_id($this->chatBot->vars["my_guild_id"], $this->chatBot->vars["dimension"], true);

			// Check if guild xml file is correct if not abort
			if ($org === null) {
				$this->logger->log('ERROR', "Error downloading the guild roster xml file");
				return;
			}

			if (count($org->members) == 0) {
				$this->logger->log('ERROR', "Guild xml file has no members! Aborting roster update.");
				return;
			}

			// Save the current org_members table in a var
			$data = $this->db->query("SELECT * FROM org_members_<myname>");
			if (count($data) == 0 && (count($org->members) > 0)) {
				$restart = true;
			} else {
				$restart = false;
				forEach ($data as $row) {
					$dbentrys[$row->name]["name"] = $row->name;
					$dbentrys[$row->name]["mode"] = $row->mode;
				}
			}

			$this->chatBot->ready = false;

			$this->db->begin_transaction();

			// Going through each member of the org and add or update his/her
			forEach ($org->members as $member) {
				// don't do anything if $member is the bot itself
				if (strtolower($member->name) == strtolower($this->chatBot->vars["name"])) {
					continue;
				}

				//If there exists already data about the player just update him/her
				if (isset($dbentrys[$member->name])) {
					if ($dbentrys[$member->name]["mode"] == "del") {
						// members who are not on notify should not be on the buddy list but should remain in the database
						$this->buddylistManager->remove($member->name, 'org');
						unset($this->chatBot->guildmembers[$name]);
					} else {
						// add org members who are on notify to buddy list
						$this->buddylistManager->add($member->name, 'org');
						$this->chatBot->guildmembers[$member->name] = $member->guild_rank_id;

						// if member was added to notify list manually, switch mode to org and let guild roster update from now on
						if ($dbentrys[$member->name]["mode"] == "add") {
							$this->db->exec("UPDATE org_members_<myname> SET `mode` = 'org' WHERE `name` = ?", $member->name);
						}
					}
				//Else insert his/her data
				} else {
					// add new org members to buddy list
					$this->buddylistManager->add($member->name, 'org');
					$this->chatBot->guildmembers[$member->name] = $member->guild_rank_id;

					$this->db->exec("INSERT INTO org_members_<myname> (`name`, `mode`) VALUES (?, 'org')", $member->name);
				}
				unset($dbentrys[$member->name]);
			}

			$this->db->commit();

			// remove buddies who are no longer org members
			forEach ($dbentrys as $buddy) {
				if ($buddy['mode'] != 'add') {
					$this->db->exec("DELETE FROM online WHERE `name` = ? AND `channel_type` = 'guild' AND added_by = '<myname>'", $buddy['name']);
					$this->db->exec("DELETE FROM org_members_<myname> WHERE `name` = ?", $buddy['name']);
					$this->buddylistManager->remove($buddy['name'], 'org');
					unset($this->chatBot->guildmembers[$buddy['name']]);
				}
			}

			$this->logger->log('INFO', "Finished Roster update");

			if ($restart == true) {
				$this->chatBot->sendGuild("Guild roster has been loaded for the first time. Restarting...");

				$this->logger->log('INFO', "The bot is restarting");

				sleep(5);

				// in case some of the org members were already on the friendlist, we need to restart the bot
				// in order to get them to appear on the online list
				die();
			}
		}
	}

	/**
	 * @Event("24hrs")
	 * @Description("Download guild roster xml and update guild members")
	 */
	public function downloadOrgRosterEvent($eventObj) {
		$this->updateOrgRoster();
	}
	
	/**
	 * @Event("orgmsg")
	 * @Description("Automatically update guild roster as players join and leave the guild")
	 */
	public function autoNotifyOrgMembersEvent($eventObj) {
		$message = $eventObj->message;
		if (preg_match("/^(.+) invited (.+) to your organization.$/", $message, $arr)) {
			$name = ucfirst(strtolower($arr[2]));

			$row = $this->db->queryRow("SELECT * FROM org_members_<myname> WHERE `name` = ?", $name);
			if ($row != null) {
				$this->db->exec("UPDATE org_members_<myname> SET `mode` = 'add' WHERE `name` = ?", $name);
				$this->buddylistManager->add($name, 'org');
				$this->chatBot->guildmembers[$name] = 6;
				$msg = "<highlight>{$name}<end> has been added to the Notify list.";
			} else {
				$this->db->exec("INSERT INTO org_members_<myname> (`mode`, `name`) VALUES ('add', ?)", $name);
				$this->buddylistManager->add($name, 'org');
				$this->chatBot->guildmembers[$name] = 6;
				$msg = "<highlight>{$name}<end> has been added to the Notify list.";
			}
			$this->db->exec("INSERT INTO online (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES (?, '<myguild>', 'guild', '<myname>', ?)", $name, time());
			$this->chatBot->sendGuild($msg);

			// update character info
			$this->playerManager->get_by_name($name);
		} else if (preg_match("/^(.+) kicked (.+) from your organization.$/", $message, $arr) || preg_match("/^(.+) removed inactive character (.+) from your organization.$/", $message, $arr)) {
			$name = ucfirst(strtolower($arr[2]));

			$this->db->exec("UPDATE org_members_<myname> SET `mode` = 'del' WHERE `name` = ?", $name);
			$this->db->exec("DELETE FROM online WHERE `name` = ? AND `channel_type` = 'guild' AND added_by = '<myname>'", $name);

			unset($this->chatBot->guildmembers[$name]);
			$this->buddylistManager->remove($name, 'org');

			$msg = "Removed <highlight>{$name}<end> from the Notify list.";
			$this->chatBot->sendGuild($msg);
		} else if (preg_match("/^(.+) just left your organization.$/", $message, $arr) || preg_match("/^(.+) kicked from organization \\(alignment changed\\).$/", $message, $arr)) {
			$name = ucfirst(strtolower($arr[1]));

			$this->db->exec("UPDATE org_members_<myname> SET `mode` = 'del' WHERE `name` = ?", $name);
			$this->db->exec("DELETE FROM online WHERE `name` = ? AND `channel_type` = 'guild' AND added_by = '<myname>'", $name);

			unset($this->chatBot->guildmembers[$name]);
			$this->buddylistManager->remove($name, 'org');

			$msg = "Removed <highlight>{$name}<end> from the Notify list.";
			$this->chatBot->sendGuild($msg);
		}
	}
	
	/**
	 * @Event("logOn")
	 * @Description("Shows an org member logon in chat")
	 */
	public function orgMemberLogonMessageEvent($eventObj) {
		$sender = $eventObj->sender;
		if (isset($this->chatBot->guildmembers[$sender]) && $this->chatBot->is_ready()) {
			if ($this->settingManager->get('first_and_last_alt_only') == 1) {
				// if at least one alt/main is still online, don't show logoff message
				$altInfo = $this->alts->get_alt_info($sender);
				if (count($altInfo->get_online_alts()) > 1) {
					return;
				}
			}

			$whois = $this->playerManager->get_by_name($sender);

			$msg = '';
			if ($whois === null) {
				$msg = "$sender logged on.";
			} else {
				$msg = $this->playerManager->get_info($whois);

				$msg .= " logged on.";

				$altInfo = $this->alts->get_alt_info($sender);
				if (count($altInfo->alts) > 0) {
					$msg .= " " . $altInfo->get_alts_blob(false, true);
				}
			}

			$logon_msg = $this->preferences->get($sender, 'logon_msg');
			if ($logon_msg !== false && $logon_msg != '') {
				$msg .= " - " . $logon_msg;
			}

			$this->chatBot->sendGuild($msg, true);

			//private channel part
			if ($this->settingManager->get("guest_relay") == 1) {
				$this->chatBot->sendPrivate($msg, true);
			}
		}
	}
	
	/**
	 * @Event("logOff")
	 * @Description("Shows an org member logoff in chat")
	 */
	public function orgMemberLogoffMessageEvent($eventObj) {
		$sender = $eventObj->sender;
		if (isset($this->chatBot->guildmembers[$sender]) && $this->chatBot->is_ready()) {
			if ($this->settingManager->get('first_and_last_alt_only') == 1) {
				// if at least one alt/main is already online, don't show logoff message
				$altInfo = $alts->get_alt_info($sender);
				if (count($altInfo->get_online_alts()) > 0) {
					return;
				}
			}

			$msg = "$sender logged off.";
			$logoff_msg = $this->preferences->get($sender, 'logoff_msg');
			if ($logoff_msg !== false && $logoff_msg != '') {
				$msg .= " - " . $logoff_msg;
			}

			$this->chatBot->sendGuild($msg, true);

			//private channel part
			if ($this->settingManager->get("guest_relay") == 1) {
				$this->chatBot->sendPrivate($msg, true);
			}
		}
	}
	
	/**
	 * @Event("logOff")
	 * @Description("Record org member logoff for lastseen command")
	 */
	public function orgMemberLogoffRecordEvent($eventObj) {
		$sender = $eventObj->sender;
		if (isset($this->chatBot->guildmembers[$sender]) && $this->chatBot->is_ready()) {
			$this->db->exec("UPDATE org_members_<myname> SET `logged_off` = ? WHERE `name` = ?", time(), $sender);
		}
	}
	
	public function isGuildBot() {
		return !empty($this->chatBot->vars["my_guild"]) && !empty($this->chatBot->vars["my_guild_id"]);
	}
}

