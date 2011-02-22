<?php

/*
`name` VARCHAR(30) NOT NULL
`module` VARCHAR(50)
`mode` VARCHAR(10)
`is_core` TINYINT NOT NULL
`setting` VARCHAR(50) DEFAULT '0'
`options` VARCHAR(50) Default '0'
`intoptions` VARCHAR(50) DEFAULT '0'
`description` VARCHAR(50) NOT NULL DEFAULT ''
`source` VARCHAR(5)
`access_level` INT DEFAULT 0
`help` VARCHAR(60)
`verify` INT DEFAULT 1
*/

class Setting {

	/**
	 * @name: add
	 * @description: Adds a new setting
	 */	
	public static function add($module, $name, $description, $mode, $type, $value, $options = '', $intoptions = '', $admin = 'mod', $help = '') {
		$db = DB::get_instance();
		global $chatBot;
		
		$name = strtolower($name);
		$type = strtolower($type);
		
		if (!in_array($type, array('color', 'number', 'text', 'options'))) {
			Logger::log('ERROR', 'Core', "Error in registering Setting $module:setting($name). Type should be one of: 'color', 'number', 'text', 'options'. Actual: '$type'.");
		}
		
		$options = str_replace("'", "''", $options);
		$description = str_replace("'", "''", $description);

		if ($chatBot->existing_settings[$name] != true) {
			$db->exec("INSERT INTO settings_<myname> (`name`, `module`, `type`, `mode`, `value`, `options`, `intoptions`, `description`, `source`, `admin`, `help`) VALUES ('$name', '$module', '$type', '$mode', '" . str_replace("'", "''", $value) . "', '$options', '$intoptions', '$description', 'db', '$admin', '$help')");
		  	$chatBot->settings[$name] = $value;
	  	} else {
			$db->exec("UPDATE settings_<myname> SET `module` = '$module', `type` = '$type', `mode` = '$mode', `options` = '$options', `intoptions` = '$intoptions', `description` = '$description', `admin` = '$admin', `help` = '$help' WHERE `name` = '$name'");
		}
	}

	/**
	 * @name: get
	 * @description: Gets the value of a setting
	 */	
	public static function get($name) {
		global $chatBot;
	
		$name = strtolower($name);
		if (isset($chatBot->settings[$name])) {
	  		return $chatBot->settings[$name];
	  	} else {
	  		return false;
		}
	}

	/**
	 * @name: save
	 * @description: Saves a new value for a setting
	 */	
	public static function save($name, $newsetting = null) {
		$db = DB::get_instance();
		global $chatBot;

		$name = strtolower($name);
		if ($newsetting === null) {
			return false;
		}

		if (isset($chatBot->settings[$name])) {
			$db->exec("UPDATE settings_<myname> SET `value` = '" . str_replace("'", "''", $newsetting) . "' WHERE `name` = '$name'");
			$chatBot->settings[$name] = $newsetting;
		} else {
			return false;
		}
	}
}

?>