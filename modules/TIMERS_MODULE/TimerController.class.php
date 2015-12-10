<?php

namespace Budabot\User\Modules;

use stdClass;
use Exception;
use Budabot\Core\Registry;

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
 *		help        = 'timers.txt',
 *		alias       = 'timer'
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
	public $accessManager;

	/** @Inject */
	public $text;

	/** @Inject */
	public $util;
	
	/** @Inject */
	public $settingManager;
	
	/** @Inject */
	public $setting;
	
	/** @Logger */
	public $logger;

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
			// leave 1 alert so that owner can be notified of timer finishing
			while (count($row->alerts) > 1 && $row->alerts[0]->time <= time()) {
				array_shift($row->alerts);
			}

			$this->timers[strtolower($row->name)] = $row;
		}

		$this->settingManager->add($this->moduleName, 'timer_alert_times', 'Times to display timer alerts', 'edit', 'text', '1h 15m 1m', '1h 15m 1m', '', 'mod', 'timer_alert_times.txt');
		$this->settingManager->registerChangeListener('timer_alert_times', array($this, 'changeTimerAlertTimes'));
	}

	public function changeTimerAlertTimes($settingName, $oldValue, $newValue, $data)  {
		$alertTimes = array_reverse(explode(' ', $newValue));
		$oldTime = 0;
		forEach ($alertTimes as $alertTime) {
			$time = $this->util->parseTime($alertTime);
			if ($time == 0) {
				// invalid time
				throw new Exception("Error saving setting: invalid alert time('$alertTime'). For more info type !help timer_alert_times.");
			} else if ($time <= $oldTime) {
				// invalid alert order
				throw new Exception("Error saving setting: invalid alert order('$alertTime'). For more info type !help timer_alert_times.");
			}
			$oldTime = $time;
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

		$time = time();

		forEach ($this->timers as $timer) {
			if (count($timer->alerts) == 0) {
				$this->remove($timer->name);
				continue;
			}

			forEach($timer->alerts as $alert) {
				if ($alert->time > $time) {
					break;
				}

				array_shift($timer->alerts);

				list($name, $method) = explode(".", $timer->callback);
				$instance = Registry::getInstance($name);
				if ($instance === null) {
					$this->logger->log('ERROR', "Error calling callback method '$timer->callback' for timer '$timer->name': Could not find instance '$name'.");
				} else {
					try {
						$instance->$method($timer, $alert);
					} catch (Exception $e) {
						$this->logger->log("ERROR", "Error calling callback method '$timer->callback' for timer '$timer->name': " . $e->getMessage(), $e);
					}
				}
			}
		}
	}

	public function timerCallback($timer, $alert) {
		$this->sendAlertMessage($timer, $alert);
	}

	public function repeatingTimerCallback($timer, $alert) {
		$this->sendAlertMessage($timer, $alert);

		if (count($timer->alerts) == 0) {
			$endTime = $timer->data + $alert->time;
			$alerts = $this->generateAlerts($timer->owner, $timer->name, $endTime, explode(' ', $this->setting->timer_alert_times));
			$this->add($timer->name, $timer->owner, $timer->mode, $alerts, $timer->callback, $timer->data);
		}
	}

	public function sendAlertMessage($timer, $alert) {
		$msg = $alert->message;
		$mode = $timer->mode;
		if ('priv' == $mode) {
			$this->chatBot->sendPrivate($msg);
		} else if ('guild' == $mode) {
			$this->chatBot->sendGuild($msg);
		} else {
			$this->chatBot->sendTell($msg, $timer->owner);
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
			$msg = "A timer with the name <highlight>$timerName<end> is already running.";
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

		$endTime = time() + $initialRunTime;
		
		$alerts = $this->generateAlerts($sender, $timerName, $endTime, explode(' ', $this->setting->timer_alert_times));

		$this->add($timerName, $sender, $channel, $alerts, "timercontroller.repeatingTimerCallback", $runTime);

		$initialTimerSet = $this->util->unixtimeToReadable($initialRunTime);
		$timerSet = $this->util->unixtimeToReadable($runTime);
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
			$time_left = $this->util->unixtimeToReadable($timer->endtime - time());
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
		} else if ($timer->owner != $sender && !$this->accessManager->checkAccess($sender, "mod")) {
			$msg = "You must own this timer or have moderator access in order to remove it.";
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
		$count = count($timers);
		if ($count == 0) {
			$msg = "No timers currently running.";
		} else {
			$blob = '';
			forEach ($timers as $timer) {
				$time_left = $this->util->unixtimeToReadable($timer->endtime - time());
				$name = $timer->name;
				$owner = $timer->owner;

				$remove_link = $this->text->make_chatcmd("Remove", "/tell <myname> timers rem $name");

				$repeatingInfo = '';
				if ($timer->callback == 'timercontroller.repeatingTimerCallback') {
					$repeatingTimeString = $this->util->unixtimeToReadable($timer->data);
					$repeatingInfo = " (Repeats every $repeatingTimeString)";
				}

				$blob .= "Name: <highlight>$name<end> {$remove_link}\n";
				$blob .= "Time left: <highlight>$time_left<end> $repeatingInfo\n";
				$blob .= "Set by: <highlight>$owner<end>\n\n";
			}
			$msg = $this->text->make_blob("Timers ($count)", $blob);
		}
		$sendto->reply($msg);
	}
	
	public function generateAlerts($sender, $name, $endTime, $alertTimes) {
		$alerts = array();
		
		forEach ($alertTimes as $alertTime) {
			$time = $this->util->parseTime($alertTime);
			$timeString = $this->util->unixtimeToReadable($time);
			if ($endTime - $time > time()) {
				$alert = new stdClass;
				$alert->message = "Reminder: Timer <highlight>$name<end> has <highlight>$timeString<end> left. [set by <highlight>$sender<end>]";
				$alert->time = $endTime - $time;
				$alerts []= $alert;
			}
		}
		
		if ($endTime > time()) {
			$alert = new stdClass;
			if ($name == $sender) {
				$alert->message = "<highlight>$sender<end> your timer has gone off.";
			} else {
				$alert->message = "<highlight>$sender<end> your timer named <highlight>$name<end> has gone off.";
			}
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
		
		if (strlen($name) > 255) {
			return "You cannot use timer names longer than 255 characters.";
		}

		$endTime = time() + $runTime;
		
		if ($alerts === null) {
			$alerts = $this->generateAlerts($sender, $name, $endTime, explode(' ', $this->setting->timer_alert_times));
		}

		$this->add($name, $sender, $channel, $alerts, 'timercontroller.timerCallback');

		$timerset = $this->util->unixtimeToReadable($runTime);
		return "Timer <highlight>$name<end> has been set for $timerset.";
	}

	public function add($name, $owner, $mode, $alerts, $callback, $data = null) {
		usort($alerts, function($a, $b) {
			return $a->time - $b->time;
		});

		$timer = new stdClass;
		$timer->name = $name;
		$timer->owner = $owner;
		$timer->mode = $mode;
		$timer->endtime = end($alerts)->time;
		$timer->settime = time();
		$timer->callback = $callback;
		$timer->data = $data;
		$timer->alerts = $alerts;

		$this->timers[strtolower($name)] = $timer;

		$sql = "INSERT INTO timers_<myname> (`name`, `owner`, `mode`, `timer`, `settime`, `callback`, `data`, alerts) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
		$this->db->exec($sql, $name, $owner, $mode, $timer->endtime, $timer->settime, $callback, $data, json_encode($alerts));
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
