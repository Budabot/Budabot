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

class Event extends Annotation {

	/** @Inject */
	public $db;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $setting;
	
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

	/**
	 * @name: register
	 * @description: Registers an event on the bot so it can be configured
	 */
	public function register($module, $type, $filename, $description = 'none', $help = '', $defaultStatus = null) {
		$type = strtolower($type);
		
		$this->logger->log('DEBUG', "Registering event Type:($type) File:($filename) Module:($module)");
		
		$time = $this->util->parseTime($type);
		if ($time <= 0 && !in_array($type, Event::$EVENT_TYPES)) {
			$this->logger->log('ERROR', "Error registering event Type:($type) File:($filename) Module:($module). The type is not a recognized event type!");
			return;
		}
		
		if (preg_match("/\\.php$/i", $filename)) {
			$actual_filename = $this->util->verify_filename($module . '/' . $filename);
			if ($actual_filename == '') {
				$this->logger->log('ERROR', "Error registering event Type:($type) File:($filename) Module:($module). The file doesn't exist!");
				return;
			}
		} else {
			list($name, $method) = explode(".", $filename);
			if (!Registry::instanceExists($name)) {
				$this->logger->log('ERROR', "Error registering method $filename for event type $type.  Could not find instance '$name'.");
				return;
			}
			$actual_filename = $filename;
		}
		
		if (isset($this->chatBot->existing_events[$type][$actual_filename])) {
			$sql = "UPDATE eventcfg_<myname> SET `verify` = 1, `description` = ?, `help` = ? WHERE `type` = ? AND `file` = ? AND `module` = ?";
		  	$this->db->exec($sql, $description, $help, $type, $actual_filename, $module);
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
			$this->db->exec($sql, $module, $type, $actual_filename, '1', $description, $status, $help);
		}
	}

	/**
	 * @name: activate
	 * @description: Activates an event
	 */
	public function activate($type, $filename) {
		// for file includes
		$chatBot = $this->chatBot;
		$db = $this->db;
		$setting = $this->setting;
		
		$type = strtolower($type);
		
		$this->logger->log('DEBUG', "Activating event Type:($type) File:($filename)");

		if (preg_match("/\\.php$/i", $filename)) {
			$actual_filename = $this->util->verify_filename($filename);
			if ($actual_filename == '') {
				$this->logger->log('ERROR', "Error activating event Type:($type) File:($filename). The file doesn't exist!");
				return;
			}
		} else {
			list($name, $method) = explode(".", $filename);
			if (!Registry::instanceExists($name)) {
				$this->logger->log('ERROR', "Error activating method $filename for event type $type.  Could not find instance '$name'.");
				return;
			}
			$actual_filename = $filename;
		}
		
		if ($type == "setup") {
			$eventObj = new stdClass;
			$eventObj->type = 'setup';

			$this->callEventHandler($eventObj, $actual_filename);
		} else if (in_array($type, Event::$EVENT_TYPES)) {
			if (!isset($this->events[$type]) || !in_array($actual_filename, $this->events[$type])) {
				$this->events[$type] []= $actual_filename;
			} else {
				$this->logger->log('ERROR', "Error activating event Type:($type) File:($filename). Event already activated!");
			}
		} else {
			$time = $this->util->parseTime($type);
			if ($time > 0) {
				$key = $this->getKeyForCronEvent($time, $actual_filename);
				if ($key === null) {
					$this->cronevents[] = array('nextevent' => 0, 'filename' => $actual_filename, 'time' => $time);
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
		
		// to remove this check we need to make sure to use $filename instead of $actual_filename
		//Check if the file exists
		if (preg_match("/\\.php$/i", $filename)) {
			$actual_filename = $this->util->verify_filename($filename);
			if ($actual_filename == '') {
				$this->logger->log('ERROR', "Error deactivating event Type:($type) File:($filename). The file doesn't exist!");
				return;
			}
		} else {
			$actual_filename = $filename;
		}
		
		if (in_array($type, Event::$EVENT_TYPES)) {
			if (in_array($actual_filename, $this->events[$type])) {
				$found = true;
				$temp = array_flip($this->events[$type]);
				unset($this->events[$type][$temp[$actual_filename]]);
			}
		} else {
			$time = $this->util->parseTime($type);
			if ($time > 0) {
				$key = $this->getKeyForCronEvent($time, $actual_filename);
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
	
	public function getKeyForCronEvent($time, $filename) {
		forEach ($this->cronevents as $key => $event) {
			if ($time == $event['time'] && $event['filename'] == $filename) {
				return $key;
			}
		}
		return null;
	}
	
	public function update_status($type, $module, $filename, $status) {
		$type = strtolower($type);

		if ($type == 'all' || $type == '' || $type == null) {
			$type_sql = '';
		} else {
			$type_sql = "AND `type` = '$type'";
		}

		if ($filename == '' || $filename == null) {
			$filename_sql = '';
		} else {
			$cmd_sql = "AND `file` = '$filename'";
		}

		if ($module == '' || $module == null) {
			$module_sql = '';
		} else {
			$module_sql = "AND `module` = '$module'";
		}

		if ($status == 0) {
			$status_sql = "`status` = 1";
		} else {
			$status_sql = "`status` = 0";
		}

		$data = $this->db->query("SELECT * FROM eventcfg_<myname> WHERE $status_Sql $module_sql $filename_sql $type_sql");
		if (count($data) == 0) {
			return 0;
		}

		forEach ($data as $row) {
			if ($status == 1) {
				$this->activate($row->type, $row->filename);
			} else if ($status == 0) {
				$this->deactivate($row->type, $row->filename);
			}
		}

		return $this->db->exec("UPDATE eventcfg_<myname> SET status = '$status' WHERE $status_Sql $module_sql $filename_sql $type_sql");
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
	 * @description: Call php-Scripts at certin time intervals. 2 sec, 1 min, 10min, 15 min, 30min, 1 hour, 24 hours
	 */
	public function crons() {
		$chatBot = Registry::getInstance('chatBot');
		
		if ($this->chatBot->is_ready()) {
			$time = time();
			$this->logger->log('DEBUG', "Executing cron events at '$time'");
			forEach ($this->cronevents as $key => $event) {
				if ($event['nextevent'] <= $time) {
					$this->logger->log('DEBUG', "Executing cron event '${event['filename']}'");
					
					$eventObj = new stdClass;
					$eventObj->type = strtolower($event['time']);

					$this->callEventHandler($eventObj, $event['filename']);
					$this->cronevents[$key]['nextevent'] = $time + $event['time'];
				}
			}
		}
	}
	
	/*===============================
	** Name: executeConnectEvents
	** Execute Events that needs to be executed right after login
	*/	
	public function executeConnectEvents(){
		$this->logger->log('DEBUG', "Executing connected events");

		$eventObj = new stdClass;
		$eventObj->type = 'connect';

		$this->fireEvent($eventObj);
	}
	
	public function fireEvent($eventObj) {
		if (isset($this->events[$eventObj->type])) {
			forEach ($this->events[$eventObj->type] as $filename) {
				if ($this->callEventHandler($eventObj, $filename)) {
					return;
				}
			}
		}
	}
	
	public function callEventHandler($eventObj, $handler) {
		$this->logger->log('DEBUG', "Executing handler '$handler' for event type '$eventObj->type'");
	
		$stop_execution = false;
		$msg = "";

		if (preg_match("/\\.php$/i", $handler)) {
			$chatBot = $this->chatBot;
			$db = $this->db;
			$setting = $this->setting;
			
			$type = $eventObj->type;
			@$channel = $eventObj->channel;
			@$sender = $eventObj->sender;
			@$message = $eventObj->message;
			@$packet_type = $eventObj->packet->type;
			@$args = $eventObj->packet->args;

			require $handler;
		} else {
			list($name, $method) = explode(".", $handler);
			$instance = Registry::getInstance($name);
			if ($instance === null) {
				$this->logger->log('ERROR', "Could not find instance for name '$name' in '$handler' for event type '$eventObj->type'");
			} else {
				$stop_execution = ($instance->$method($eventObj) === true ? true : false);
			}
		}
		return $stop_execution;
	}
}

?>