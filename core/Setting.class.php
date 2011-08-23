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

/*
Table Description
mode = if this setting is editable or not
		edit = This setting is editable
		hide = This setting is not shown on !settings list
		noedit = Not changable
options = Allowed Options for this setting
		text = any text(up to 50 chars)
		text;maxLength = any text(up to max length)
		number = any number
		number;start-end = any number between start and end
		color = any HMTL Color code
		option1;option2 = List of Options seperated by a ;
intoptions = Internal Version of options
		ONLY usable for a list of options
descriptions = Description of this setting, this is shown on !settings
source = From where is this setting coming
		db = Added by a module
		cfg = added by thy config.php
admin = Rank that is needed for this setting (admin or mod)
help = Helpfile for this setting
*/

class Setting {

	/**
	 * @name: add
	 * @param: $module - the module name
	 * @param: $name - the name of the setting
	 * @param: $description - a description for the setting (will appear in the config)
	 * @param: $mode - 'edit', 'noedit', or 'hide'
	 * @param: $type - 'color', 'number', 'text', or 'options'
	 * @param: $options - a list of values that the setting can be, semi-colon delimited (optional)
	 * @param: $intoptions - int values corresponding to $options; if empty, the values from $options will be what is stored in the database (optional)
	 * @param: $admin - the permission level needed to change this setting (default: mod) (optional)
	 * @param: $help - a help file for this setting; if blank, will use a help topic with the same name as this setting if it exists (optional)
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
			$db->exec("INSERT INTO settings_<myname> (`name`, `module`, `type`, `mode`, `value`, `options`, `intoptions`, `description`, `source`, `admin`, `verify`, `help`) VALUES ('$name', '$module', '$type', '$mode', '" . str_replace("'", "''", $value) . "', '$options', '$intoptions', '$description', 'db', '$admin', 1, '$help')");
		  	$chatBot->settings[$name] = $value;
	  	} else {
			$db->exec("UPDATE settings_<myname> SET `module` = '$module', `type` = '$type', `mode` = '$mode', `options` = '$options', `intoptions` = '$intoptions', `description` = '$description', `admin` = '$admin', `verify` = 1, `help` = '$help' WHERE `name` = '$name'");
		}
	}

	/**
	 * @name: get
	 * @description: Gets the value of a setting
	 * @return: the value of the setting, or false if a setting with that name does not exist
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
	 * @param: $name - the name of the setting
	 * @param: @value - the new value to set the setting to
	 * @return: false if the setting with that name does not exist, true otherwise
	 */	
	public static function save($name, $value) {
		$db = DB::get_instance();
		global $chatBot;

		$name = strtolower($name);

		if (isset($chatBot->settings[$name])) {
			$db->exec("UPDATE settings_<myname> SET `verify` = 1, `value` = '" . str_replace("'", "''", $value) . "' WHERE `name` = '$name'");
			$chatBot->settings[$name] = $value;
			return true;
		} else {
			return false;
		}
	}
}

?>