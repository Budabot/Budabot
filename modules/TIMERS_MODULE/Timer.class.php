<?php

class Timer {
	
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
				if ('msg' == $mode) {
					$this->chatBot->sendTell($msg, $owner);
				} else {
					$this->chatBot->send($msg, $mode);
				}
			}
		}

	}
	
	/**
	 * @Command("rtimer")
	 * @AccessLevel("guild")
	 * @Description("Add a repeating timer")
	 * @Help("timers.txt")
	 */
	public function rtimerCommand($message, $channel, $sender, $sendto) {
		if (preg_match("/^(rtimer add|rtimer) ([a-z0-9]+) ([a-z0-9]+) (.+)$/i", $message, $arr)) {
			$initialTimeString = $arr[2];
			$timeString = $arr[3];
			$timerName = $arr[4];
			
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
		} else {
			return false;
		}
	}
	
	/**
	 * @Command("timers")
	 * @AccessLevel("guild")
	 * @Description("Set and show timers")
	 * @Help("timers.txt")
	 */
	public function timerCommand($message, $channel, $sender, $sendto) {
		if (preg_match("/^timers view (.+)$/i", $message, $arr)) {
			$msg = $this->viewTimer($arr[1]);
			$sendto->reply($msg);
		} else if (preg_match("/^(timers|timers add) ([0-9]+)$/i", $message, $arr) || preg_match("/^(timers|timers add) ([0-9]+) (.+)$/i", $message, $arr)) {
			if (isset($arr[3])) {
				$timerName = $arr[3];
			} else {
				$timerName = $sender;
			}
			$runTime = $arr[2] * 60;
			
			$msg = $this->addTimer($sender, $timerName, $runTime, $channel);
			$sendto->reply($msg);
		} else if (preg_match("/^timers (rem|del) (.+)$/i", $message, $arr)) {
			$msg = $this->removeTimer($sender, $arr[2]);
			$sendto->reply($msg);
		} else if (preg_match("/^(timers add|timers) ([a-z0-9]+) (.+)$/i", $message, $arr) ||
				preg_match("/^(timers add|timers) ([a-z0-9]+)$/i", $message, $arr2)) {

			if (isset($arr2)) {
				$timeString = $arr2[2];
				$name = $sender;
			} else {
				$timeString = $arr[2];
				$name = $arr[3];
			}
			
			$runTime = $this->util->parseTime($timeString);

			$msg = $this->addTimer($sender, $name, $runTime, $channel);
			$sendto->reply($msg);
		} else if (preg_match("/^timers$/i", $message, $arr)) {
			$msg = $this->showTimers();
			$sendto->reply($msg);
		} else {
			return false;
		}
	}
	
	public function viewTimer($name) {
		$name = strtolower($name);
		$timer = $this->get($name);
		if ($timer == null) {
			return "Could not find timer named <highlight>$name<end>.";
		}
		
		$time_left = $this->util->unixtime_to_readable($timer->timer - time());
		$name = $timer->name;

		return "Timer <highlight>$name<end> has <highlight>$time_left<end> left.";
	}
	
	public function removeTimer($sender, $name) {
		$name = strtolower($name);
		$timer = $this->get($name);
		if ($timer == null) {
			return "Could not find a timer named <highlight>$name<end>.";
		} else if ($timer->owner != $sender && !$this->accessLevel->checkAccess($sender, "rl")) {
			return "You don't have the required access level (raidleader) to remove this timer.";
		} else {
			$this->remove($name);
			return "Removed timer <highlight>$name<end>.";
		}
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
	
	public function showTimers() {
		$timers = $this->getAllTimers();
		if (count($timers) == 0) {
			return "No timers currently running.";
		}

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
		return $this->text->make_blob("Timers currently running", $blob);
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
