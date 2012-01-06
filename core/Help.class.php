<?php

/*
`name` VARCHAR(30) NOT NULL
`module` VARCHAR(50) NOT NULL
`description` VARCHAR(50) NOT NULL DEFAULT ''
`file` VARCHAR(255) NOT NULL
`is_core` TINYINT NOT NULL
`access_level` INT DEFAULT 0
`verify` INT Default 1
*/

class Help extends Annotation {

	/** @Inject */
	public $db;
	
	/** @Inject */
	public $accessLevel;
	
	/** @Inject */
	public $chatBot;

	/**
	 * @name: register
	 * @description: Registers a help command
	 */
	public function register($module, $command, $filename, $admin, $description) {
		Logger::log('DEBUG', 'Help', "Registering $module:help($command) Helpfile:($filename)");

		$command = strtolower($command);

		// Check if the file exists
		$actual_filename = Util::verify_filename($module . '/' . $filename);
		if ($actual_filename == '') {
			Logger::log('ERROR', 'Help', "Error in registering the File $filename for Help command $module:help($command). The file doesn't exist!");
			return;
		}

		if (isset($this->chatBot->existing_helps[$command])) {
			$sql = "UPDATE hlpcfg_<myname> SET `verify` = 1, `file` = ?, `module` = ?, `description` = ? WHERE `name` = ?";
			$this->db->exec($sql, $actual_filename, $module, $description, $command);
		} else {
			$sql = "INSERT INTO hlpcfg_<myname> (`name`, `module`, `file`, `description`, `admin`, `verify`) VALUES (?, ?, ?, ?, ?, ?)";
			$this->db->exec($sql, $command, $module, $actual_filename, $description, $admin, '1');
		}

		$row = $this->db->queryRow("SELECT * FROM hlpcfg_<myname> WHERE `name` = ?", $command);
		$this->chatBot->helpfiles[$command]["filename"] = $actual_filename;
		$this->chatBot->helpfiles[$command]["admin"] = $row->admin;
		$this->chatBot->helpfiles[$command]["info"] = $description;
		$this->chatBot->helpfiles[$command]["module"] = $module;
		
		if (substr($actual_filename, 0, 7) == "./core/") {
			$this->chatBot->helpfiles[$command]["status"] = "enabled";
		}
	}
	
	/**
	 * @name: find
	 * @description: Find a help topic by name if it exists and if the user has permissions to see it
	 */
	public function find($helpcmd, $char) {
		$helpcmd = strtolower($helpcmd);

		$data = false;
		if (isset($this->chatBot->helpfiles[$helpcmd])) {
			$filename = $this->chatBot->helpfiles[$helpcmd]["filename"];
			$admin = $this->chatBot->helpfiles[$helpcmd]["admin"];
			
			if ($char === null) {
				$access = true;
			} else {
				$access = $this->accessLevel->checkAccess($char, $admin);
			}
			if ($access === true && file_exists($filename)) {
				$data = file_get_contents($filename);
			}
		}

		return $data;
	}
}

?>
