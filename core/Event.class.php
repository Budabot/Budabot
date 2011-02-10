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

	public static $EVENT_TYPES = array(
		'towers','orgmsg','msg','priv','extPriv','guild','joinPriv','extJoinPriv','leavePriv','extLeavePriv',
		'extJoinPrivRequest','extKickPriv','logOn','logOff','2sec','1min','10mins','15mins','30mins','1hour',
		'24hrs','connect','shopping','sendGuild','sendPriv','setup');

	/**
	 * @name: register
	 * @description: Registers an event on the bot so it can be configured
	 */
	public static function register($module, $type, $filename, $dependson = 'none', $description = 'none') {
		$db = DB::get_instance();
		global $chatBot;
		
		$description = str_replace("'", "''", $description);
		
		Logger::log('DEBUG', 'Core', "Registering event Type:($type) File:($filename) Module:($module)");
		
		if (!in_array($type, Event::$EVENT_TYPES)) {
			Logger::log('ERROR', 'Core', "Error registering event Type:($type) File:($filename) Module:($module). The type is not a recognized event type!");
			return;
		}
		
		//Check if the file exists
		$actual_filename = $chatBot->verifyFilename($module . '/' . $filename);
		if ($actual_filename == '') {
			Logger::log('error', 'Core', "Error registering event Type:($type) File:($filename) Module:($module). The file doesn't exist!");
			return;
		}
		
		if ($chatBot->existing_events[$type][$actual_filename] == true) {
		  	$db->exec("UPDATE cmdcfg_<myname> SET `verify` = 1, `description` = '$description' WHERE `type` = '$type' AND `cmdevent` = 'event' AND `file` = '$actual_filename' AND `module` = '$module'");
		} else {
			if ($chatBot->settings["default_module_status"] == 1) {
				$status = 1;
			} else {
				$status = 0;
			}
			$db->exec("INSERT INTO cmdcfg_<myname> (`module`, `cmdevent`, `type`, `file`, `verify`, `description`, `status`) VALUES ('$module', 'event', '$type', '$actual_filename', '1', '$description', '$status')");
		}
	}

	/**
	 * @name: activate
	 * @description: Activates an event
	 */
	public static function activate($type, $filename) {
		global $chatBot;
		$db = DB::get_instance();
		
		Logger::log('DEBUG', 'Core', "Activating event Type:($type) File:($filename)");
		
		if (!in_array($type, Event::$EVENT_TYPES)) {
			Logger::log('ERROR', 'Core', "Error activating event Type:($type) File:($filename). The type is not a recognized event type!");
			return;
		}

		//Check if the file exists
		$actual_filename = $chatBot->verifyFilename($filename);
		if ($actual_filename == '') {
			Logger::log('error', 'Core', "Error activating event Type:($type) File:($filename). The file doesn't exist!");
			return;
		}
		
		if ($type == "setup") {
			include $actual_filename;
		} else {
			if (!in_array($actual_filename, $chatBot->events[$type])) {
				$chatBot->events[$type] []= $actual_filename;
			} else {
				Logger::log('ERROR', 'Core', "Error activating event Type:($type) File:($filename). Event already registered!");
			}
		}
	}

	/**
	 * @name: deactivate
	 * @description: Deactivates an event
	 */
	public static function deactivate($type, $filename) {
		global $chatBot;

		Logger::log('debug', 'Core', "Deactivating event Type:($type) File:($filename)");
		
		if (!in_array($type, Event::$EVENT_TYPES)) {
			Logger::log('ERROR', 'Core', "Error deactivating event Type:($type) File:($filename). The type is not a recognized event type!");
			return;
		}
		
		/* does we need this check for deactivating? --tyrence
		//Check if the file exists
		$actual_filename = $chatBot->verifyFilename($filename);
		if ($actual_filename == '') {
			Logger::log('error', 'Core', "Error deactivating event Type:($type) File:($filename). The file doesn't exist!");
			return;
		}
		*/

		if (in_array($actual_filename, $chatBot->events[$type])) {
			$temp = array_flip($chatBot->events[$type]);
			unset($chatBot->events[$type][$temp[$actual_filename]]);
		} else {
			Logger::log('ERROR', 'Core', "Error deactivating event Type:($type) File:($filename). The event is not active or doesn't exist!");
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
			$cmd_sql = "AND `filename` = '$filename'";
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
	function loadEvents() {
	  	$db = DB::get_instance();

		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `status` = '1' AND `cmdevent` = 'event'");
		$data = $db->fObject("all");
		forEach ($data as $row) {
			Event::activate($row->type, $row->file);
		}
	}
}

?>