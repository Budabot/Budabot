<?php

require_once 'DBRow.class.php';

/**
 * @Instance
 */
class DB {

	/** @Inject */
	public $setting;

	/** @Inject */
	public $util;

	private $type;
	private $sql;
	private $dbName;
	private $user;
	private $pass;
	private $host;
	private $botname;
	private $dim;
	private $guild;
	private $lastQuery;
	private $in_transaction = false;
	public $table_replaces = array();

	function connect($type, $dbName, $host = NULL, $user = NULL, $pass = NULL) {
		global $vars;
		$this->type = strtolower($type);
		$this->dbName = $dbName;
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->botname = strtolower($vars["name"]);
		$this->dim = $vars["dimension"];
		$this->guild = str_replace("'", "''", $vars["my_guild"]);

		if ($this->type == 'mysql') {
			$this->sql = new PDO("mysql:dbname=$dbName;host=$host", $user, $pass);
			$this->sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->exec("SET sql_mode = 'TRADITIONAL,NO_BACKSLASH_ESCAPES'");
			$this->exec("SET time_zone = '+00:00'");
			$this->exec("SET storage_engine = MyISAM");
		} else if ($this->type == 'sqlite') {
			if ($host == NULL || $host == "" || $host == "localhost") {
				$this->dbName = "./data/$this->dbName";
			} else {
				$this->dbName = "$host/$this->dbName";
			}

			$this->sql = new PDO("sqlite:".$this->dbName);
			$this->sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} else {
			throw new Exception("Invalid database type: '$type'.  Expecting 'mysql' or 'sqlite'.");
		}
	}

	function get_type() {
		return $this->type;
	}

	function queryRow($sql) {
		$sql = $this->formatSql($sql);

		$args = func_get_args();
		array_shift($args);

		$ps = $this->executeQuery($sql, $args);
		$result = $ps->fetchAll(PDO::FETCH_CLASS, 'DBRow');

		if (count($result) == 0) {
			return null;
		} else {
			return $result[0];
		}
	}

	//Sends a query to the Database and gives the result back
	function query($sql) {
		$sql = $this->formatSql($sql);

		$args = func_get_args();
		array_shift($args);

		$ps = $this->executeQuery($sql, $args);
		return $ps->fetchAll(PDO::FETCH_CLASS, 'DBRow');
	}

	//Does Basicly the same thing just don't gives the result back(used for create table, Insert, delete etc), a bit faster as normal querys
	function exec($sql) {
		$sql = $this->formatSql($sql);

		if (substr_compare($sql, "create", 0, 6, true) == 0) {
			if ($this->type == "mysql") {
				$sql = str_ireplace("AUTOINCREMENT", "AUTO_INCREMENT", $sql);
			} else if ($this->type == "sqlite") {
				$sql = str_ireplace("AUTO_INCREMENT", "AUTOINCREMENT", $sql);
				$sql = str_ireplace(" INT ", " INTEGER ", $sql);
			}
		}

		$args = func_get_args();
		array_shift($args);

		$ps = $this->executeQuery($sql, $args);

		return $ps->rowCount();
	}

	private function executeQuery($sql, $params) {
		$this->lastQuery = $sql;
		LegacyLogger::log('DEBUG', "SQL", $sql . " - " . print_r($params, true));

		try {
			$ps = $this->sql->prepare($sql);
			$count = 1;
			forEach ($params as $param) {
				if ($param === "NULL") {
					$ps->bindValue($count++, $param, PDO::PARAM_NULL);
				} else {
					$ps->bindValue($count++, $param);
				}
			}
			$ps->execute();
			return $ps;
		} catch (PDOException $e) {
			if ($this->type == "Sqlite" && $e->errorInfo[1] == 17) {
				// fix for Sqlite schema changed error (retry the query)
				return $this->executeQuery($sql, $params);
			}
			throw new SQLException("{$e->errorInfo[2]} in: $sql - " . print_r($params, true), 0, $e);
		}
	}

	//Start of an transaction
	function begin_transaction() {
		$this->in_transaction = true;
		$this->sql->beginTransaction();
	}

	//Commit an transaction
	function commit() {
		$this->in_transaction = false;
		$this->sql->Commit();
	}

	function rollback() {
		$this->in_transaction = false;
		$this->sql->rollback();
	}

	function in_transaction() {
		return $this->in_transaction;
	}

	//Return the last inserted ID
	function lastInsertId() {
		return $this->sql->lastInsertId();
	}

	function formatSql($sql) {
		forEach ($this->table_replaces as $search => $replace) {
			$sql = str_replace($search, $replace, $sql);
		}
		$sql = str_replace("<dim>", $this->dim, $sql);
		$sql = str_replace("<myname>", $this->botname, $sql);
		$sql = str_replace("<myguild>", $this->guild, $sql);

		return $sql;
	}

	function getLastQuery() {
		return $this->lastQuery;
	}

	/**
	 * @name: add_table_replace
	 * @description: creates a replace string to run on queries
	 */
	public function add_table_replace($search, $replace) {
		$this->table_replaces[$search] = $replace;
	}

	/**
	 * @name: loadSQLFile
	 * @description: Loads an sql file if there is an update
	 *    Will load the sql file with name $namexx.xx.xx.xx.sql if xx.xx.xx.xx is greater than settings[$name . "_sql_version"]
	 *    If there is an sql file with name $name.sql it would load that one every time
	 */
	public function loadSQLFile($module, $name, $forceUpdate = false) {
		$name = strtolower($name);

		// only letters, numbers, underscores are allowed
		if (!preg_match('/^[a-z0-9_]+$/', $name)) {
			$msg = "Invalid SQL file name: '$name' for module: '$module'!  Only numbers, letters, and underscores permitted!";
			LegacyLogger::log('ERROR', 'SQL', $msg);
			return $msg;
		}

		$settingName = $name . "_db_version";

		$core_dir = "./core/$module";
		$modules_dir = "./modules/$module";
		$dir = '';
		if (is_dir($modules_dir)) {
			$dir = $modules_dir;
		} else if (is_dir($core_dir)) {
			$dir = $core_dir;
		} else {
			$msg = "Could not find module '$module'.";
			LegacyLogger::log('ERROR', 'SQL', $msg);
			return $msg;
		}
		$d = dir($dir);

		if ($this->setting->exists($settingName)) {
			$currentVersion = $this->setting->get($settingName);
		} else {
			$currentVersion = false;
		}
		if ($currentVersion === false) {
			$currentVersion = 0;
		}

		$file = false;
		$maxFileVersion = 0;  // 0 indicates no version
		if ($d) {
			while (false !== ($entry = $d->read())) {
				if (is_file("$dir/$entry") && preg_match("/^" . $name . "([0-9.]*)\\.sql$/i", $entry, $arr)) {

					// If the file has no versioning in its filename, then we go off the modified timestamp
					if ($arr[1] == '') {
						$file = $entry;
						$maxFileVersion = filemtime("$dir/$file");
						break;
					}

					if ($this->util->compare_version_numbers($arr[1], $maxFileVersion) >= 0) {
						$maxFileVersion = $arr[1];
						$file = $entry;
					}
				}
			}
		}

		if ($file === false) {
			$msg = "No SQL file found with name '$name' in module '$module'!";
			LegacyLogger::log('ERROR', 'SQL', $msg);
			return;
		}
		
		// make sure setting is verified so it doesn't get deleted
		$this->setting->add($module, $settingName, $settingName, 'noedit', 'text', 0);
		
		if ($forceUpdate || $this->util->compare_version_numbers($maxFileVersion, $currentVersion) > 0) {
			$handle = @fopen("$dir/$file", "r");
			if ($handle) {
				try {
					while (($line = fgets($handle)) !== false) {
						$line = trim($line);
						// don't process comment lines or blank lines
						if ($line != '' && substr($line, 0, 1) != "#" && substr($line, 0, 2) != "--") {
							$this->exec($line);
						}
					}

					$this->setting->save($settingName, $maxFileVersion);

					if ($maxFileVersion != 0) {
						$msg = "Updated '$name' database from '$currentVersion' to '$maxFileVersion'";
						LegacyLogger::log('DEBUG', 'SQL', $msg);
					} else {
						$msg = "Updated '$name' database";
						LegacyLogger::log('DEBUG', 'SQL', $msg);
					}
				} catch (SQLException $e) {
					$msg = "Error loading sql file '$file': " . $e->getMessage();
					LegacyLogger::log('ERROR', 'SQL', $msg);
				}
			} else {
				$msg = "Could not load SQL file: '$dir/$file'";
				LegacyLogger::log('ERROR', 'SQL', $msg);
			}
		} else {
			$msg = "'$name' database already up to date! version: '$currentVersion'";
			LegacyLogger::log('DEBUG', 'SQL', $msg);
		}

		return $msg;
	}
}

?>
