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
		number = any number
		color = any HMTL Color code
		option1;option2 = List of Options seperated by a ;
intoptions = Internal Version of options
		ONLY usable for a list of options
descriptions = Description of this setting, this is shown on !settings
access_level = access level that is needed for this setting (admin or mod)
help = Helpfile for this setting
*/

/**
 * @Instance
 */
class SettingManager {
	/** @Inject */
	public $db;

	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $util;

	/** @Inject */
	public $helpManager;

	/** @Logger */
	public $logger;

	public $settings = array();

	private $changeListeners = array();

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
	public function add($module, $name, $description, $mode, $type, $value, $options = '', $intoptions = '', $admin = 'mod', $help = '') {
		$name = strtolower($name);
		$type = strtolower($type);

		if ($admin == '') {
			$admin = 'mod';
		}

		if (!in_array($type, array('color', 'number', 'text', 'options', 'time'))) {
			$this->logger->log('ERROR', "Error in registering Setting $module:setting($name). Type should be one of: 'color', 'number', 'text', 'options', 'time'. Actual: '$type'.");
		}

		if ($type == 'time') {
			$oldvalue = $value;
			$value = $this->util->parseTime($value);
			if ($value < 1) {
				$this->logger->log('ERROR', "Error in registering Setting $module:setting($name). Invalid time: '{$oldvalue}'.");
				return;
			}
		}

		$help = $this->helpManager->checkForHelpFile($module, $help, $name);

		try {
			if (array_key_exists($name, $this->chatBot->existing_settings)) {
				$sql = "UPDATE settings_<myname> SET `module` = ?, `type` = ?, `mode` = ?, `options` = ?, `intoptions` = ?, `description` = ?, `admin` = ?, `verify` = 1, `help` = ? WHERE `name` = ?";
				$this->db->exec($sql, $module, $type, $mode, $options, $intoptions, $description, $admin, $help, $name);
			} else {
				$sql = "INSERT INTO settings_<myname> (`name`, `module`, `type`, `mode`, `value`, `options`, `intoptions`, `description`, `source`, `admin`, `verify`, `help`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
				$this->db->exec($sql, $name, $module, $type, $mode, $value, $options, $intoptions, $description, 'db', $admin, '1', $help);
				$this->settings[$name] = $value;
			}
		} catch (SQLException $e) {
			$this->logger->log('ERROR', "Error in registering Setting $module:setting($name): " . $e->getMessage());
		}
	}
	
	/**
	 * @name: exists
	 * @description: Determine if a setting with a given name exists
	 * @return: true if the setting exists, false otherwise
	 */
	public function exists($name) {
		return array_key_exists($name, $this->settings);
	}

	/**
	 * @name: get
	 * @description: Gets the value of a setting
	 * @return: the value of the setting, or false if a setting with that name does not exist
	 */
	public function get($name) {
		$name = strtolower($name);
		if (array_key_exists($name, $this->settings)) {
			return $this->settings[$name];
		} else {
			$this->logger->log("ERROR", "Could not retrieve value for setting '$name' because setting does not exist");
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
	public function save($name, $value) {
		$name = strtolower($name);

		if (array_key_exists($name, $this->settings)) {
			if ($this->settings[$name] !== $value) {
				// notify any listeners
				if (isset($this->changeListeners[$name])) {
					forEach ($this->changeListeners[$name] as $listener) {
						$result = call_user_func($listener->callback, $name, $this->settings[$name], $value, $listener->data);
						if ($result === false) {
							return false;
						}
					}
				}
				$this->settings[$name] = $value;
				$this->db->exec("UPDATE settings_<myname> SET `verify` = 1, `value` = ? WHERE `name` = ?", $value, $name);
			}
			return true;
		} else {
			$this->logger->log("ERROR", "Could not save value '$value' for setting '$name' because setting does not exist");
			return false;
		}
	}

	public function displayValue($row) {
		$options = explode(";", $row->options);
		if ($row->type == "color") {
			return $row->value."Current Color</font>\n";
		} else if ($row->type == 'time') {
			return "<highlight>" . $this->util->unixtime_to_readable($row->value) . "<end>\n";
		} else if ($row->intoptions != "") {
			$intoptions = explode(";", $row->intoptions);
			$intoptions2 = array_flip($intoptions);
			$key = $intoptions2[$row->value];
			return "<highlight>{$options[$key]}<end>\n";
		} else {
			return "<highlight>" . htmlspecialchars($row->value) . "<end>\n";
		}
	}

	public function upload() {
		$this->settings = array();

		//Upload Settings from the db that are set by modules
		$data = $this->db->query("SELECT * FROM settings_<myname>");
		forEach ($data as $row) {
			$this->settings[$row->name] = $row->value;
		}
	}

	/**
	 * Adds listener callback which will be called if given $settingName changes.
	 *
	 * The callback has following signature:
	 * <code>function callback($value, $data)</code>
	 * $value: new value of the setting
	 * $data:  optional data variable given on register
	 *
	 * Example usage:
	 * <code>
	 *	registerChangeListener("some_setting_name", function($settingName, $oldValue, $newValue, $data) {
	 *		// ...
	 *	} );
	 * </code>
	 *
	 * @param string   $settingName changed setting's name 
	 * @param callback $callback    the callback function to call
	 * $param mixed    $data        any data which will be passed to to the callback (optional)
	 */
	public function registerChangeListener($settingName, $callback, $data) {
		if (!is_callable($callback)) {
			$this->logger->log('ERROR', 'Given callback is not valid.');
			return;
		}
		$settingName = strtolower($settingName);

		$listener = new StdClass();
		$listener->callback = $callback;
		$listener->data = $data;
		$this->changeListeners[$settingName] []= $listener;
	}
}

?>
