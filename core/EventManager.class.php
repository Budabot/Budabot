<?php

/*
`module` VARCHAR(50) NOT NULL
`type` VARCHAR(18)
`file` VARCHAR(255)
`is_core` TINYINT NOT NULL
`description` VARCHAR(50) NOT NULL DEFAULT ''
`verify` INT DEFAULT 0
`status` INT DEFAULT 1
*/

/**
 * @Instance
 */
class EventManager {

	/** @Inject */
	public $db;

	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $settingManager;

	/** @Inject */
	public $util;

	/** @Logger */
	public $logger;

	public $events = array();

	public static $EVENT_TYPES = array(
		'msg','priv','extpriv','guild','joinpriv','extjoinpriv','leavepriv','extleavepriv',
		'orgmsg','extjoinprivrequest','extkickpriv','logon','logoff','towers','connect',
		'sendguild','sendpriv','setup','allpackets'
	);

	private $lastCronTime = 0;
	private $areConnectEventsFired = false;

	/**
	 * @name: register
	 * @description: Registers an event on the bot so it can be configured
	 */
	public function register($module, $type, $filename, $description = 'none', $help = '', $defaultStatus = null) {
		$type = strtolower($type);

		$this->logger->log('DEBUG', "Registering event Type:($type) File:($filename) Module:($module)");

		$time = $this->util->parseTime($type);
		if ($time <= 0 && !in_array($type, self::$EVENT_TYPES)) {
			$this->logger->log('ERROR', "Error registering event Type:($type) File:($filename) Module:($module). The type is not a recognized event type!");
			return;
		}

		list($name, $method) = explode(".", $filename);
		if (!Registry::instanceExists($name)) {
			$this->logger->log('ERROR', "Error registering method $filename for event type $type.  Could not find instance '$name'.");
			return;
		}

		try {
			if (isset($this->chatBot->existing_events[$type][$filename])) {
				$sql = "UPDATE eventcfg_<myname> SET `verify` = 1, `description` = ?, `help` = ? WHERE `type` = ? AND `file` = ? AND `module` = ?";
				$this->db->exec($sql, $description, $help, $type, $filename, $module);
			} else {
				if ($defaultStatus === null) {
					if ($this->chatBot->vars['default_module_status'] == 1) {
						$status = 1;
					} else {
						$status = 0;
					}
				} else {
					$status = $defaultStatus;
				}
				$sql = "INSERT INTO eventcfg_<myname> (`module`, `type`, `file`, `verify`, `description`, `status`, `help`) VALUES (?, ?, ?, ?, ?, ?, ?)";
				$this->db->exec($sql, $module, $type, $filename, '1', $description, $status, $help);
			}
		} catch (SQLException $e) {
			$this->logger->log('ERROR', "Error registering method $filename for event type $type: " . $e->getMessage());
		}
	}

	/**
	 * @name: activate
	 * @description: Activates an event
	 */
	public function activate($type, $filename) {
		$type = strtolower($type);

		$this->logger->log('DEBUG', "Activating event Type:($type) File:($filename)");

		list($name, $method) = explode(".", $filename);
		if (!Registry::instanceExists($name)) {
			$this->logger->log('ERROR', "Error activating method $filename for event type $type.  Could not find instance '$name'.");
			return;
		}

		if ($type == "setup") {
			$eventObj = new stdClass;
			$eventObj->type = 'setup';

			$this->callEventHandler($eventObj, $filename);
		} else if (in_array($type, self::$EVENT_TYPES)) {
			if (!isset($this->events[$type]) || !in_array($filename, $this->events[$type])) {
				$this->events[$type] []= $filename;
			} else {
				$this->logger->log('ERROR', "Error activating event Type:($type) File:($filename). Event already activated!");
			}
		} else {
			$time = $this->util->parseTime($type);
			if ($time > 0) {
				$key = $this->getKeyForCronEvent($time, $filename);
				if ($key === null) {
					$this->cronevents[] = array('nextevent' => 0, 'filename' => $filename, 'time' => $time);
				} else {
					$this->logger->log('ERROR', "Error activating event Type:($type) File:($filename). Event already activated!");
				}
			} else {
				$this->logger->log('ERROR', "Error activating event Type:($type) File:($filename). The type is not a recognized event type!");
			}
		}
	}

	/**
	 * @name: deactivate
	 * @description: Deactivates an event
	 */
	public function deactivate($type, $filename) {
		$type = strtolower($type);

		$this->logger->log('debug', "Deactivating event Type:($type) File:($filename)");

		if (in_array($type, self::$EVENT_TYPES)) {
			if (in_array($filename, $this->events[$type])) {
				$found = true;
				$temp = array_flip($this->events[$type]);
				unset($this->events[$type][$temp[$filename]]);
			}
		} else {
			$time = $this->util->parseTime($type);
			if ($time > 0) {
				$key = $this->getKeyForCronEvent($time, $filename);
				if ($key != null) {
					$found = true;
					unset($this->cronevents[$key]);
				}
			} else {
				$this->logger->log('ERROR', "Error deactivating event Type:($type) File:($filename). The type is not a recognized event type!");
				return;
			}
		}

		if (!$found) {
			$this->logger->log('ERROR', "Error deactivating event Type:($type) File:($filename). The event is not active or doesn't exist!");
		}
	}
	
	/**
	 * Activates events that are annotated on one or more method names
	 * if the events are not already activated
	 *
	 * @param Object obj
	 * @param String methodName1
	 * @param String methodName2 ...
	 */
	public function activateIfDeactivated($obj) {
		$eventMethods = func_get_args();
		array_shift($eventMethods);
		forEach ($eventMethods as $eventMethod) {
			$call = get_class($obj) . "." . $eventMethod;
			$type = $this->getEventTypeByMethod($obj, $eventMethod);
			if ($type !== null) {
				if (isset($this->events[$type]) && in_array($call, $this->events[$type])) {
					// event already activated
					continue;
				}
				$this->activate($type, $call);
			} else {
				$this->logger->log('ERROR', "Could not find event for '$call'");
			}
		}
	}
	
	/**
	 * Deactivates events that are annotated on one or more method names
	 * if the events are not already deactivated
	 *
	 * @param Object obj
	 * @param String methodName1
	 * @param String methodName2 ...
	 */
	public function deactivateIfActivated($obj) {
		$eventMethods = func_get_args();
		array_shift($eventMethods);
		forEach ($eventMethods as $eventMethod) {
			$call = get_class($obj) . "." . $eventMethod;
			$type = $this->getEventTypeByMethod($obj, $eventMethod);
			if ($type !== null) {
				if (!isset($this->events[$type]) || !in_array($call, $this->events[$type])) {
					// event already deactivated
					continue;
				}
				$this->deactivate($type, $call);
			} else {
				$this->logger->log('ERROR', "Could not find event for '$call'");
			}
		}
	}
	
	public function getEventTypeByMethod($obj, $methodName) {
		$method = new ReflectionAnnotatedMethod($obj, $methodName);
		if ($method->hasAnnotation('Event')) {
			return strtolower($method->getAnnotation('Event')->value);
		} else {
			return null;
		}
	}

	public function getKeyForCronEvent($time, $filename) {
		forEach ($this->cronevents as $key => $event) {
			if ($time == $event['time'] && $event['filename'] == $filename) {
				return $key;
			}
		}
		return null;
	}

	/**
	 * @name: loadEvents
	 * @description: Loads the active events into memory and activates them
	 */
	public function loadEvents() {
		$this->logger->log('DEBUG', "Loading enabled events");

		$data = $this->db->query("SELECT * FROM eventcfg_<myname> WHERE `status` = '1'");
		forEach ($data as $row) {
			$this->activate($row->type, $row->file);
		}
	}

	/**
	 * @name: crons
	 * @description: Call timer events
	 */
	public function crons() {
		$time = time();

		if ($this->lastCronTime == $time) {
			return;
		}
		$this->lastCronTime = $time;

		$this->logger->log('DEBUG', "Executing cron events at '$time'");
		forEach ($this->cronevents as $key => $event) {
			if ($event['nextevent'] <= $time) {
				$this->logger->log('DEBUG', "Executing cron event '${event['filename']}'");

				$eventObj = new stdClass;
				$eventObj->type = strtolower($event['time']);

				$this->cronevents[$key]['nextevent'] = $time + $event['time'];
				$this->callEventHandler($eventObj, $event['filename']);
			}
		}
	}

	/*===============================
	** Name: executeConnectEvents
	** Execute Events that needs to be executed right after login
	*/
	public function executeConnectEvents() {

		if ($this->areConnectEventsFired) {
			return;
		}
		$this->areConnectEventsFired = true;

		$this->logger->log('DEBUG', "Executing connected events");

		$eventObj = new stdClass;
		$eventObj->type = 'connect';

		$this->fireEvent($eventObj);
	}

	public function fireEvent($eventObj) {
		if (isset($this->events[$eventObj->type])) {
			forEach ($this->events[$eventObj->type] as $filename) {
				$this->callEventHandler($eventObj, $filename);
			}
		}
	}

	public function callEventHandler($eventObj, $handler) {
		$this->logger->log('DEBUG', "Executing handler '$handler' for event type '$eventObj->type'");

		try {
			list($name, $method) = explode(".", $handler);
			$instance = Registry::getInstance($name);
			if ($instance === null) {
				$this->logger->log('ERROR', "Could not find instance for name '$name' in '$handler' for event type '$eventObj->type'");
			} else {
				$instance->$method($eventObj);
			}
		} catch (StopExecutionException $e) {
			throw $e;
		} catch (Exception $e) {
			$this->logger->log('ERROR', "Error calling event handler '$handler': " . $e->getMessage(), $e);
		}
	}
}

?>
