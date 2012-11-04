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
			$this->timers[strtolower($row->name)] = $row;
		}
	}

	/**
	 * @Event("2sec")
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

			if ($tleft >= 3599 && $tleft < 3601 && ((time() - $set_time) >= 30)) {
				if ($name == $owner) {
					$msg = "Reminder: Timer has <highlight>1 hour<end> left. [set by <highlight>$owner<end>]";
				} else {
					$msg = "Reminder: Timer <highlight>$name<end> has <highlight>1 hour<end> left. [set by <highlight>$owner<end>]";
				}
			} else if ($tleft >= 899 && $tleft < 901 && ((time() - $set_time) >= 30)) {
				if ($name == $owner) {
					$msg = "Reminder: Timer has <highlight>15 minutes<end> left [set by <highlight>$owner<end>]";
				} else {
					$msg = "Reminder: Timer <highlight>$name<end> has <highlight>15 minutes<end> left. [set by <highlight>$owner<end>]";
				}
			} else if ($tleft >= 59 && $tleft < 61 && ((time() - $set_time) >= 30)) {
				if ($name == $owner) {
					$msg = "Reminder: Timer has <highlight>1 minute<end> left [set by <highlight>$owner<end>]";
				} else {
					$msg = "Reminder: Timer <highlight>$name<end> has <highlight>1 minute<end> left. [set by <highlight>$owner<end>]";
				}
			} else if ($tleft <= 0) {
				if ($tleft >= -600) {
					if ($name == $owner) {
						$msg = "<highlight>$owner<end> your timer has gone off.";
					} else {
						$msg = "<highlight>$owner<end> your timer named <highlight>$name<end> has gone off.";
					}
				}

				$this->remove($name);
				if ($timer->callback == 'repeating') {
					$this->add($name, $owner, $mode, $timer->callback_param + $timer->timer, $timer->callback, $timer->callback_param);
				}
			}

			if ('' != $msg) {
				if ('priv' == $mode) {
					$this->chatBot->sendPrivate($msg);
				} else if ('guild' == $mode) {
					$this->chatBot->sendGuild($msg);
				} else {
					$this->chatBot->sendTell($msg, $owner);
				}
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

		$this->add($timerName, $sender, $channel, $time, "repeating", $runTime);

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
	 * @Matches("/^(timers add|timers) ([a-z0-9]+) (.+)$/i")
	 * @Matches("/^(timers add|timers) ([a-z0-9]+)$/i")
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

	public function addTimer($sender, $name, $runTime, $channel) {
		if ($name == '') {
			return;
		}

		if ($this->get($name) != null) {
			return "A timer named <highlight>$name<end> is already running.";
		}

		if ($runTime < 1) {
			return "You must enter a valid time parameter.";
		}

		$timer = time() + $runTime;

		$this->add($name, $sender, $channel, $timer);

		$timerset = $this->util->unixtime_to_readable($runTime);
		return "Timer <highlight>$name<end> has been set for $timerset.";
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

	public function add($name, $owner, $mode, $time, $callback = null, $callback_param = null) {
		$this->timers[strtolower($name)] = (object)array("name" => $name, "owner" => $owner, "mode" => $mode, "timer" => $time, "settime" => time(), 'callback' => $callback, 'callback_param' => $callback_param);
		$sql = "INSERT INTO timers_<myname> (`name`, `owner`, `mode`, `timer`, `settime`, `callback`, `callback_param`) VALUES (?, ?, ?, ?, ?, ?, ?)";
		$this->db->exec($sql, $name, $owner, $mode, $time, time(), $callback, $callback_param);
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
