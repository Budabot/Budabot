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

class Event {

	public static $CRON_EVENT_TYPES = array(
		'2sec','1min','5mins','10mins','15mins','30mins','1hour','24hrs'
	);
	
	public static $CRON_EVENT_TYPE_DURATIONS = array(
		2, 60, 300, 600, 900, 1800, 3600, 86400
	);

	public static $EVENT_TYPES = array(
		'msg','priv','extPriv','guild','joinPriv','extJoinPriv','leavePriv','extLeavePriv',
		'orgmsg','extJoinPrivRequest','extKickPriv','logOn','logOff','towers','connect',
		'sendGuild','sendPriv','setup','allpackets'
	);

	/**
	 * @name: register
	 * @description: Registers an event on the bot so it can be configured
	 */
	public static function register($module, $type, $filename, $dependson = 'none', $description = 'none', $help = '') {
		$db = DB::get_instance();
		global $chatBot;
		
		$description = str_replace("'", "''", $description);
		
		Logger::log('DEBUG', 'Event', "Registering event Type:($type) File:($filename) Module:($module)");
		
		if (!in_array($type, Event::$EVENT_TYPES) && !in_array($type, Event::$CRON_EVENT_TYPES)) {
			Logger::log('ERROR', 'Event', "Error registering event Type:($type) File:($filename) Module:($module). The type is not a recognized event type!");
			return;
		}
		
		//Check if the file exists
		$actual_filename = Util::verify_filename($module . '/' . $filename);
		if ($actual_filename == '') {
			Logger::log('ERROR', 'Event', "Error registering event Type:($type) File:($filename) Module:($module). The file doesn't exist!");
			return;
		}
		
		if ($chatBot->existing_events[$type][$actual_filename] == true) {
		  	$db->exec("UPDATE eventcfg_<myname> SET `verify` = 1, `description` = '$description', `help` = '{$help}' WHERE `type` = '$type' AND `file` = '$actual_filename' AND `module` = '$module'");
		} else {
			if ($chatBot->vars['default_module_status'] == 1) {
				$status = 1;
			} else {
				$status = 0;
			}
			$db->exec("INSERT INTO eventcfg_<myname> (`module`, `type`, `file`, `verify`, `description`, `status`, `help`) VALUES ('$module', '$type', '$actual_filename', '1', '$description', '$status', '{$help}')");
		}
	}

	/**
	 * @name: activate
	 * @description: Activates an event
	 */
	public static function activate($type, $filename) {
		global $chatBot;
		$db = DB::get_instance();
		
		Logger::log('DEBUG', 'Event', "Activating event Type:($type) File:($filename)");
		
		if (!in_array($type, Event::$EVENT_TYPES) && !in_array($type, Event::$CRON_EVENT_TYPES)) {
			Logger::log('ERROR', 'Event', "Error activating event Type:($type) File:($filename). The type is not a recognized event type!");
			return;
		}

		//Check if the file exists
		$actual_filename = Util::verify_filename($filename);
		if ($actual_filename == '') {
			Logger::log('ERROR', 'Event', "Error activating event Type:($type) File:($filename). The file doesn't exist!");
			return;
		}
		
		if ($type == "setup") {
			include $actual_filename;
		} else {
			if (!isset($chatBot->events[$type]) || !in_array($actual_filename, $chatBot->events[$type])) {
				$chatBot->events[$type] []= $actual_filename;
			} else {
				Logger::log('ERROR', 'Event', "Error activating event Type:($type) File:($filename). Event already activated!");
			}
		}
	}

	/**
	 * @name: deactivate
	 * @description: Deactivates an event
	 */
	public static function deactivate($type, $filename) {
		global $chatBot;

		Logger::log('debug', 'Event', "Deactivating event Type:($type) File:($filename)");
		
		if (!in_array($type, Event::$EVENT_TYPES) && !in_array($type, Event::$CRON_EVENT_TYPES)) {
			Logger::log('ERROR', 'Event', "Error deactivating event Type:($type) File:($filename). The type is not a recognized event type!");
			return;
		}
		
		// to remove this check we need to make sure to use $filename instead of $actual_filename
		//Check if the file exists
		$actual_filename = Util::verify_filename($filename);
		if ($actual_filename == '') {
			Logger::log('ERROR', 'Event', "Error deactivating event Type:($type) File:($filename). The file doesn't exist!");
			return;
		}

		if (isset($chatBot->events[$type]) && in_array($actual_filename, $chatBot->events[$type])) {
			$temp = array_flip($chatBot->events[$type]);
			unset($chatBot->events[$type][$temp[$actual_filename]]);
		} else {
			Logger::log('ERROR', 'Event', "Error deactivating event Type:($type) File:($filename). The event is not active or doesn't exist!");
		}
	}
	
	public static function update_status($type, $module, $filename, $status) {
		$db = DB::get_instance();
		
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
	
		$db->query("SELECT * FROM eventcfg_<myname> WHERE $status_Sql $module_sql $filename_sql $type_sql");
		if ($db->numrows == 0) {
			return 0;
		}
		
		$data = $db->fObject('all');
		forEach ($data as $row) {
			if ($status == 1) {
				Event::activate($row->type, $row->filename);
			} else if ($status == 0) {
				Event::deactivate($row->type, $row->filename);
			}
		}
		
		return $db->exec("UPDATE eventcfg_<myname> SET status = '$status' WHERE $status_Sql $module_sql $filename_sql $type_sql");
	}

	/**
	 * @name: loadEvents
	 * @description: Loads the active events into memory and activates them
	 */
	public static function loadEvents() {
		Logger::log('DEBUG', 'Event', "Loading enabled events");

	  	$db = DB::get_instance();

		$db->query("SELECT * FROM eventcfg_<myname> WHERE `status` = '1'");
		$data = $db->fObject("all");
		forEach ($data as $row) {
			Event::activate($row->type, $row->file);
		}
	}
	
	/**
	 * @name: crons
	 * @description: Call php-Scripts at certin time intervals. 2 sec, 1 min, 10min, 15 min, 30min, 1 hour, 24 hours
	 */
	public static function crons() {
		global $chatBot;
		
		if ($chatBot->is_ready()) {
			$numEvents = count(Event::$CRON_EVENT_TYPES);
			for ($i = 0; $i < $numEvents; $i++) {
				Event::executeCronEvent(Event::$CRON_EVENT_TYPES[$i], Event::$CRON_EVENT_TYPE_DURATIONS[$i]);
			}
		}
	}
	
	private static function executeCronEvent($eventType, $time) {
		$db = DB::get_instance();
		global $chatBot;

		if ($chatBot->vars[$eventType] < time()) {
			Logger::log('DEBUG', 'Cron', "Executing cron event '$eventType'");
			$chatBot->vars[$eventType] = time() + $time;
			forEach ($chatBot->events[$eventType] as $filename) {
				require $filename;
			}
		}
	}
	
	public static function initCronTimers() {
		Logger::log('DEBUG', 'Cron', "Initializing cron timers");

		global $chatBot;

		forEach (Event::$CRON_EVENT_TYPES as $event_type) {
			$chatBot->cron_timers[$event_type] = time();
		}
	}
	
	/*===============================
	** Name: executeConnectEvents
	** Execute Events that needs to be executed right after login
	*/	
	public static function executeConnectEvents(){
		Logger::log('DEBUG', 'Event', "Executing connected events");

		$db = DB::get_instance();
		global $chatBot;

		// Check files, for all 'connect' events.
		forEach ($chatBot->events['connect'] as $filename) {
			require $filename;
		}
	}
}

?>