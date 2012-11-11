<?php

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'cloak',
 *		accessLevel = 'guild',
 *		description = 'Show the status of the city cloak',
 *		help        = 'cloak.txt',
 *		alias		= 'city'
 *	)
 */
class CloakController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $settingManager;
	
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $alts;
	
	/** @Inject */
	public $timerController;
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'org_city');
	
		$this->settingManager->add($this->moduleName, "showcloakstatus", "Show cloak status to players at logon", "edit", "options", "1", "Never;When cloak is down;Always", "0;1;2");
		$this->settingManager->add($this->moduleName, "cloak_reminder_interval", "How often to spam guild channel when cloak is down", "edit", "time", "5m", "2m;5m;10m;15m;20m");
	}
	
	/**
	 * @HandlesCommand("cloak")
	 * @Matches("/^cloak$/i")
	 */
	public function cloakCommand($message, $channel, $sender, $sendto, $args) {
		$data = $this->db->query("SELECT * FROM org_city_<myname> WHERE `action` = 'on' OR `action` = 'off' ORDER BY `time` DESC LIMIT 20");
		if (count($data) == 0) {
			$msg = "Unknown status on cloak!";
		} else {
			$row = array_shift($data);
			$timeSinceChange = time() - $row->time;
			$timeString = $this->util->unixtime_to_readable(3600 - $timeSinceChange, false);

			if ($timeSinceChange >= 3600 && $row->action == "off") {
				$msg = "The cloaking device is <orange>disabled<end>. It is possible to enable it.";
			} else if ($timeSinceChange < 3600 && $row->action == "off") {
				$msg = "The cloaking device is <orange>disabled<end>. It is possible in $timeString to enable it.";
			} else if ($timeSinceChange >= 3600 && $row->action == "on") {
				$msg = "The cloaking device is <green>enabled<end>. It is possible to disable it.";
			} else if ($timeSinceChange < 3600 && $row->action == "on") {
				$msg = "The cloaking device is <green>enabled<end>. It is possible in $timeString to disable it.";
			}

			$list = "Time: <highlight>" . $this->util->date($row->time) . "<end>\n";
			$list .= "Action: <highlight>Cloaking device turned " . $row->action . "<end>\n";
			$list .= "Player: <highlight>" . $row->player . "<end>\n\n";

			forEach ($data as $row) {
				$list .= "Time: <highlight>" . $this->util->date($row->time) . "<end>\n";
				$list .= "Action: <highlight>Cloaking device turned " . $row->action . "<end>\n";
				$list .= "Player: <highlight>" . $row->player . "<end>\n\n";
			}
			$msg .= " " . $this->text->make_blob("Cloak History", $list);
		}
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("cloak")
	 * @Matches("/^cloak (raise|on)$/i")
	 */
	public function cloakRaiseCommand($message, $channel, $sender, $sendto, $args) {
		$row = $this->db->queryRow("SELECT * FROM org_city_<myname> WHERE `action` = 'on' OR `action` = 'off' ORDER BY `time` DESC LIMIT 1");

		if ($row->action == "on") {
			$msg = "The cloaking device is already <green>enabled<end>.";
		} else {
			$this->db->exec("INSERT INTO org_city_<myname> (`time`, `action`, `player`) VALUES (?, ?, ?)", time(), 'on', $sender . '*');
			$msg = "The cloaking device has been manually enabled in the bot (you must still enable the cloak if it's disabled).";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @Event("guild")
	 * @Description("Records when the cloak is raised or lowered")
	 */
	public function recordCloakChangesEvent($eventObj) {
		if (!$this->util->isValidSender($eventObj->sender) && preg_match("/^(.+) turned the cloaking device in your city (on|off).$/i", $eventObj->message, $arr)) {
			$this->db->exec("INSERT INTO org_city_<myname> (`time`, `action`, `player`) VALUES (?, ?, ?)", time(), $arr[2], $arr[1]);
		}
	}
	
	/**
	 * @Event("1min")
	 * @Description("Checks timer to see if cloak can be raised or lowered")
	 */
	public function checkTimerEvent($eventObj) {
		$row = $this->db->queryRow("SELECT * FROM org_city_<myname> ORDER BY `time` DESC LIMIT 1");
		if ($row !== null) {
			$timeSinceChange = time() - $row->time;
			if ($row->action == "off") {
				// send message to org chat every 5 minutes that the cloaking device is
				// disabled past the the time that the cloaking device could be enabled.
				$interval = $this->settingManager->get('cloak_reminder_interval');
				if ($timeSinceChange >= 60*60 && ($timeSinceChange % $interval >= 0 && $timeSinceChange % $interval <= 60 )) {
					$timeString = $this->util->unixtime_to_readable(time() - $row->time, false);
					$this->chatBot->sendGuild("The cloaking device was disabled by <highlight>{$row->player}<end> $timeString ago. It is possible to enable it.");
				}
			} else if ($row->action == "on") {
				if ($timeSinceChange >= 60*60 && $timeSinceChange < 61*60) {
					$this->chatBot->sendGuild("The cloaking device was enabled one hour ago. Alien attacks can now be initiated.");
				}
			}
		}
	}
	
	/**
	 * @Event("1min")
	 * @Description("Reminds the player who lowered cloak to raise it")
	 */
	public function cloakReminderEvent($eventObj) {
		// valid states for action are: 'on', 'off'
		$row = $this->db->queryRow("SELECT * FROM org_city_<myname> WHERE `action` = 'on' OR `action` = 'off' ORDER BY `time` DESC LIMIT 1 ");
		if ($row !== null) {
			$msg = "";

			if ($row->action == "off") {
				$timeSinceChange = time() - $row->time;
				$timeString = $this->util->unixtime_to_readable(3600 - $timeSinceChange, false);

				// 10 minutes before, send tell to player
				if ($timeSinceChange >= 49*60 && $timeSinceChange <= 50*60) {
					$msg = "The cloaking device is <orange>disabled<end>. It is possible in $timeString to enable it.";
				} else if ($timeSinceChange >= 58*60 && $timeSinceChange <= 59*60) {
					// 1 minute before send tell to player
					$msg = "The cloaking device is <orange>disabled<end>. It is possible in $timeString to enable it.";
				} else if ($timeSinceChange >= 59*60 && ($timeSinceChange % (60*5) >= 0 && $timeSinceChange % (60*5) <= 60 )) {
					// when cloak can be raised, send tell to player and
					// every 5 minutes after, send tell to player
					$msg = "The cloaking device is <orange>disabled<end>. Please enable it now.";
				}

				if ($msg) {
					// send message to all online alts
					$altInfo = $this->alts->get_alt_info($row->player);
					forEach ($altInfo->get_online_alts() as $name) {
						$this->chatBot->sendTell($msg, $name);
					}
				}
			}
		}
	}
	
	/**
	 * @Event("logOn")
	 * @Description("Show cloak status to guild members logging in")
	 */
	public function cityGuildLogonEvent($eventObj) {
		if ($this->chatBot->is_ready() && isset($this->chatBot->guildmembers[$eventObj->sender])) {
			$row = $this->db->queryRow("SELECT * FROM org_city_<myname> WHERE `action` = 'on' OR `action` = 'off' ORDER BY `time` DESC LIMIT 0, 20 ");

			$case = 0;
			if ($row !== null) {
				$timeSinceChange = time() - $row->time;
				$timeString = $this->util->unixtime_to_readable(3600 - $timeSinceChange, false);

				if ($timeSinceChange >= 60*60 && $row->action == "off") {
					$case = 1;
					$msg = "The cloaking device is <orange>disabled<end>. It is possible to enable it.";
				} else if ($timeSinceChange < 60*30 && $row->action == "off") {
					$case = 1;
					$msg = "<red>RAID IN PROGRESS!  DO NOT ENTER CITY!</red>";
				} else if ($timeSinceChange < 60*60 && $row->action == "off") {
					$msg = "Cloaking device is <orange>disabled<end>. It is possible in $timeString to enable it.";
					$case = 1;
				} else if ($timeSinceChange >= 60*60 && $row->action == "on") {
					$msg = "The cloaking device is <green>enabled<end>. It is possible to disable it.";
					$case = 2;
				} else if ($timeSinceChange < 60*60 && $row->action == "on") {
					$msg = "The cloaking device is <green>enabled<end>. It is possible in $timeString to disable it.";
					$case = 2;
				} else {
					$msg = "<highlight>Unknown status on city cloak!<end>";
					$case = 1;
				}

				if ($case <= $this->settingManager->get("showcloakstatus")) {
					$this->chatBot->sendTell($msg, $eventObj->sender);
				}
			}
		}
	}
	
	/**
	 * @Event("orgmsg")
	 * @Description("Sets a timer when an OS/AS is launched")
	 */
	public function osTimerEvent($eventObj) {
		// create a timer for 15m when an OS/AS is launched (so org knows when they can launch again)
		// [Org Msg] Blammo! Player has launched an orbital attack!

		if (preg_match("/^Blammo! (.+) has launched an orbital attack!$/i", $eventObj->message, $arr)) {
			$this->chatBot->sendGuild("OS !timer was set for 15 minutes");
			$orgName = $this->chatBot->vars["my_guild"];

			$launcher = $arr[1];

			for ($i = 1; $i <= 10; $i++) {
				$name = "$orgName OS/AS $i";
				if ($this->timerController->get($name) == null) {
					$timer = time() + (15*60); // set timer for 15 minutes
					$this->timerController->add($name, $launcher, 'guild', $timer, null);
					break;
				}
			}
		}
	}
}

?>
