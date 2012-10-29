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

/**
 * @Instance
 */
class Help extends Annotation {

	/** @Inject */
	public $db;

	/** @Inject */
	public $accessLevel;

	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $util;

	/** @Logger */
	public $logger;

	public $helpfiles = array();

	/**
	 * @name: register
	 * @description: Registers a help command
	 */
	public function register($module, $command, $filename, $admin, $description) {
		$this->logger->log('DEBUG', "Registering $module:help($command) Helpfile:($filename)");

		$command = strtolower($command);

		// Check if the file exists
		$actual_filename = $this->util->verify_filename($module . '/' . $filename);
		if ($actual_filename == '') {
			$this->logger->log('ERROR', "Error in registering the File $filename for Help command $module:help($command). The file doesn't exist!");
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
		$this->helpfiles[$command]["filename"] = $actual_filename;
		$this->helpfiles[$command]["admin"] = $row->admin;
		$this->helpfiles[$command]["info"] = $description;
		$this->helpfiles[$command]["module"] = $module;

		if (substr($actual_filename, 0, 7) == "./core/") {
			$this->helpfiles[$command]["status"] = "enabled";
		}
	}

	/**
	 * @name: find
	 * @description: Find a help topic by name if it exists and if the user has permissions to see it
	 */
	public function find($helpcmd, $char) {
		$helpcmd = strtolower($helpcmd);

		$sql = "
			SELECT module, admin, help AS file FROM cmdcfg_<myname> WHERE `cmdevent` = 'cmd' AND `cmd` = ?  AND status = 1
			UNION
			SELECT module, admin, help AS file FROM settings_<myname> WHERE `name` = ?
			UNION
			SELECT module, admin, file FROM hlpcfg_<myname> WHERE `name` = ?
			GROUP BY module, admin, file";
		$data = $this->db->query($sql, $helpcmd, $helpcmd, $helpcmd);

		$addedHelpFiles = array();
		$output = '';
		forEach ($data as $row) {
			if (!in_array($row->file, $addedHelpFiles) && $this->accessLevel->checkAccess($char, $row->admin)) {
				$output .= file_get_contents($row->file);
				$addedHelpFiles []= $row->file;
			}
		}

		return (empty($output) ? false : $output);
	}

	public function update($helpTopic, $admin) {
		$helpTopic = strtolower($helpTopic);
		$admin = strtolower($admin);

		$this->db->exec("UPDATE hlpcfg_<myname> SET `admin` = ? WHERE `name` = ?", $admin, $helpTopic);
		$this->helpfiles[$helpTopic]["admin"] = $admin;
	}

	public function checkForHelpFile($module, $file, $name) {
		if (empty($file)) {
			$file = $name . ".txt";
		} else {
			$logError = true;
		}
	
		$actualFilename = $this->util->verify_filename("$module/$file");
		if ($actualFilename == '' && $logError === true) {
			$this->logger->log('ERROR', "Error in registering the File {$module}/{$file} for Help command $name. The file doesn't exist!");
			return '';
		}
		return $actualFilename;
	}

	public function getAllHelpTopics($char) {
		$sql = "
			SELECT module, file, name, description, sort, GROUP_CONCAT(admin) as admin_list FROM (
				SELECT module, admin, help AS file, name, description, 3 AS sort FROM settings_<myname> WHERE help <> ''
				UNION
				SELECT module, admin, help AS file, cmd AS name, description, 2 AS sort FROM cmdcfg_<myname> WHERE `cmdevent` = 'cmd' AND status = 1 AND help <> ''
				UNION
				SELECT module, admin, file, name, description, 1 AS sort FROM hlpcfg_<myname>) t
			GROUP BY module, file, name, description, sort
			ORDER BY module, name, sort DESC, description";
		$data = $this->db->query($sql);

		if ($char !== null) {
			$accessLevel = $this->accessLevel->getAccessLevelForCharacter($char);
		}

		$topics = array();
		forEach ($data as $row) {
			if ($char === null || $this->checkAccessLevels($accessLevel, explode(",", $row->admin_list))) {
				$obj = new stdClass;
				$obj->module = $row->module;
				$obj->name = $row->name;
				$obj->description = $row->description;
				$topics []= $obj;
			}
		}

		return $topics;
	}
	
	public function checkAccessLevels($accessLevel1, $accessLevelsArray) {
		forEach ($accessLevelsArray as $accessLevel2) {
			if ($this->accessLevel->compareAccessLevels($accessLevel1, $accessLevel2) >= 0) {
				return true;
			}
		}
		return false;
	}
}

?>
