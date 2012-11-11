<?php

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'rtimer',
 *		accessLevel = 'guild',
 *		description = 'Adds a repeating timer',
 *		help        = 'timers.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'timers',
 *		accessLevel = 'guild',
 *		description = 'Sets and shows timers',
 *		help        = 'timers.txt'
 *	)
 */
class TimerController {

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
	public $accessLevel;

	/** @Inject */
	public $text;

	/** @Inject */
	public $util;

	private $timers = array();

	/**
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'timers');
	
		$this->timers = array();
		$data = $this->db->query("SELECT * FROM timers_<myname>");
		forEach ($data as $row) {
			$row->alerts = json_decode($row->alerts);

			// remove alerts that have already passed
			while (count($row->alerts) > 0 && $row->alerts[0]->time <= time()) {
				array_shift($timer->alerts);
			}

			$this->timers[strtolower($row->name)] = $row;
		}
	}

	/**
	 * @Event("1sec")
	 * @Description("Checks timers and periodically updates chat with time left")
	 */
	public function checkTimers() {
		//Check if at least one timer is running
		if (count($this->timers) == 0) {
			return;
		}

		forEach ($this->timers as $timer) {
			$msg = "";

			$tleft = $timer->timer - time();
			$set_time = $timer->settime;
			$name = $timer->name;
			$owner = $timer->owner;
			$mode = $timer->mode;

			while (count($timer->alerts) > 0 && $timer->alerts[0]->time <= time()) {
				$alert = array_shift($timer->alerts);
				$msg = $alert->message;

				if ('priv' == $mode) {
					$this->chatBot->sendPrivate($msg);
				} else if ('guild' == $mode) {
					$this->chatBot->sendGuild($msg);
				} else {
					$this->chatBot->sendTell($msg, $owner);
				}
			}

			if (count($timer->alerts) == 0) {
				$this->remove($name);
			}

			if ($timer->callback == 'repeating') {
				$this->add($name, $owner, $mode, $timer->callback_param + $timer->timer, null, $timer->callback, $timer->callback_param);
			}
		}

	}

	/**
	 * This command handler adds a repeating timer.
	 *
	 * @HandlesCommand("rtimer")
	 * @Matches("/^(rtimer add|rtimer) ([a-z0-9]+) ([a-z0-9]+) (.+)$/i")
	 */
	public function rtimerCommand($message, $channel, $sender, $sendto, $args) {
		$initialTimeString = $args[2];
		$timeString = $args[3];
		$timerName = $args[4];

		$timer = $this->get($timerName);
		if ($timer != null) {
			$msg = "A Timer with the name <highlight>$timerName<end> is already running.";
			$sendto->reply($msg);
			return;
		}

		$initialRunTime = $this->util->parseTime($initialTimeString);
		$runTime = $this->util->parseTime($timeString);

		if ($runTime < 1) {
			$msg = "You must enter a valid time parameter for the run time.";
			$sendto->reply($msg);
			return;
		}

		if ($initialRunTime < 1) {
			$msg = "You must enter a valid time parameter for the initial run time.";
			$sendto->reply($msg);
			return;
		}

		$time = time() + $initialRunTime;

		$this->add($timerName, $sender, $channel, $time, null, "repeating", $runTime);

		$initialTimerSet = $this->util->unixtime_to_readable($initialRunTime);
		$timerSet = $this->util->unixtime_to_readable($runTime);
		$msg = "Repeating timer <highlight>$timerName<end> will go off in $initialTimerSet and repeat every $timerSet.";

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("timers")
	 * @Matches("/^timers view (.+)$/i")
	 */
	public function timersViewCommand($message, $channel, $sender, $sendto, $args) {
		$name = strtolower($args[1]);
		$timer = $this->get($name);
		if ($timer == null) {
			$msg = "Could not find timer named <highlight>$name<end>.";
		} else {
			$time_left = $this->util->unixtime_to_readable($timer->timer - time());
			$name = $timer->name;

			$msg = "Timer <highlight>$name<end> has <highlight>$time_left<end> left.";
		}
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("timers")
	 * @Matches("/^timers (rem|del) (.+)$/i")
	 */
	public function timersRemoveCommand($message, $channel, $sender, $sendto, $args) {
		$name = strtolower($args[2]);
		$timer = $this->get($name);
		if ($timer == null) {
			$msg = "Could not find a timer named <highlight>$name<end>.";
		} else if ($timer->owner != $sender && !$this->accessLevel->checkAccess($sender, "rl")) {
			$msg = "You don't have the required access level (raidleader) to remove this timer.";
		} else {
			$this->remove($name);
			$msg = "Removed timer <highlight>$timer->name<end>.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("timers")
	 * @Matches("/^(timers add|timers) ([a-z0-9]+)$/i")
	 * @Matches("/^(timers add|timers) ([a-z0-9]+) (.+)$/i")
	 */
	public function timersAddCommand($message, $channel, $sender, $sendto, $args) {
		if (count($args) == 3) {
			$timeString = $args[2];
			$name = $sender;
		} else {
			$timeString = $args[2];
			$name = $args[3];
		}
		
		if (preg_match("/^\\d+$/", $timeString)) {
			$runTime = $args[2] * 60;
		} else {
			$runTime = $this->util->parseTime($timeString);
		}

		$msg = $this->addTimer($sender, $name, $runTime, $channel);
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("timers")
	 * @Matches("/^timers$/i")
	 */
	public function timersListCommand($message, $channel, $sender, $sendto, $args) {
		$timers = $this->getAllTimers();
		if (count($timers) == 0) {
			$msg = "No timers currently running.";
		} else {
			$blob = '';
			forEach ($timers as $timer) {
				$time_left = $this->util->unixtime_to_readable($timer->timer - time());
				$name = $timer->name;
				$owner = $timer->owner;

				$remove_link = $this->text->make_chatcmd("Remove", "/tell <myname> timers rem $name");

				$repeatingInfo = '';
				if ($timer->callback == 'repeating') {
					$repeatingTimeString = $this->util->unixtime_to_readable($timer->callback_param);
					$repeatingInfo = " (Repeats every $repeatingTimeString)";
				}

				$blob .= "Name: <highlight>$name<end> {$remove_link}\n";
				$blob .= "Time left: <highlight>$time_left<end> $repeatingInfo\n";
				$blob .= "Set by: <highlight>$owner<end>\n\n";
			}
			$msg = $this->text->make_blob("Timers currently running", $blob);
		}
		$sendto->reply($msg);
	}
	
	public function generateAlerts($sender, $name, $endTime) {
		$alerts = array();
		
		if ($endTime - 60*60 > time()) {
			$alert = new stdClass;
			$alert->message = "Reminder: Timer <highlight>$name<end> has <highlight>1 hour<end> left. [set by <highlight>$sender<end>]";
			$alert->time = $endTime - 60*60;
			$alerts []= $alert;
		}
		
		if ($endTime - 60*15 > time()) {
			$alert = new stdClass;
			$alert->message = "Reminder: Timer <highlight>$name<end> has <highlight>15 minutes<end> left. [set by <highlight>$sender<end>]";
			$alert->time = $endTime - 60*15;
			$alerts []= $alert;
		}
		
		if ($endTime - 60 > time()) {
			$alert = new stdClass;
			$alert->message = "Reminder: Timer <highlight>$name<end> has <highlight>1 minute<end> left. [set by <highlight>$sender<end>]";
			$alert->time = $endTime - 60;
			$alerts []= $alert;
		}
		
		if ($endTime > time()) {
			$alert = new stdClass;
			$alert->message = "<highlight>$sender<end> your timer named <highlight>$name<end> has gone off.";
			$alert->time = $endTime;
			$alerts []= $alert;
		}
		
		return $alerts;
	}

	public function addTimer($sender, $name, $runTime, $channel, $alerts = null) {
		if ($name == '') {
			return;
		}

		if ($this->get($name) != null) {
			return "A timer named <highlight>$name<end> is already running.";
		}

		if ($runTime < 1) {
			return "You must enter a valid time parameter.";
		}

		$endTime = time() + $runTime;
		
		if ($alerts === null) {
			$alerts = $this->generateAlerts($sender, $name, $endTime);
		}

		$this->add($name, $sender, $channel, $endTime, $alerts);

		$timerset = $this->util->unixtime_to_readable($runTime);
		return "Timer <highlight>$name<end> has been set for $timerset.";
	}

	public function add($name, $owner, $mode, $time, $alerts, $callback = null, $callback_param = null) {
		$timer = new stdClass;
		$timer->name = $name;
		$timer->owner = $owner;
		$timer->mode = $mode;
		$timer->timer = $time;
		$timer->settime = time();
		$timer->callback = $callback;
		$timer->callback_param = $callback_param;
		$timer->alerts = $alerts;
		
		$this->timers[strtolower($name)] = $timer;
		
		$sql = "INSERT INTO timers_<myname> (`name`, `owner`, `mode`, `timer`, `settime`, `callback`, `callback_param`, alerts) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
		$this->db->exec($sql, $name, $owner, $mode, $time, time(), $callback, $callback_param, json_encode($alerts));
	}

	public function remove($name) {
		$this->db->exec("DELETE FROM timers_<myname> WHERE `name` LIKE ?", $name);
		unset($this->timers[strtolower($name)]);
	}

	public function get($name) {
		return $this->timers[strtolower($name)];
	}

	public function getAllTimers() {
		return $this->timers;
	}
}

?>
