<?php

namespace Budabot\Core;

use PDO;
use PDOException;
use Exception;

require_once 'DBRow.class.php';

/**
 * @Instance
 */
class DB {

	/** @Inject */
	public $settingManager;

	/** @Inject */
	public $util;

	private $type;
	private $sql;
	private $botname;
	private $dim;
	private $guild;
	private $lastQuery;
	private $inTransaction = false;
	
	private $logger;
	
	const MYSQL = 'mysql';
	const SQLITE = 'sqlite';
	
	public function __construct() {
		$this->logger = new LoggerWrapper('SQL');
	}

	function connect($type, $dbName, $host = null, $user = null, $pass = null) {
		global $vars;
		$this->type = strtolower($type);
		$this->botname = strtolower($vars["name"]);
		$this->dim = $vars["dimension"];
		$this->guild = str_replace("'", "''", $vars["my_guild"]);

		if ($this->type == self::MYSQL) {
			$this->sql = new PDO("mysql:dbname=$dbName;host=$host", $user, $pass);
			$this->sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->exec("SET sql_mode = 'TRADITIONAL,NO_BACKSLASH_ESCAPES'");
			$this->exec("SET time_zone = '+00:00'");

			$mysqlVersion = $this->sql->getAttribute(PDO::ATTR_SERVER_VERSION);

			// for MySQL 5.5 and later, use 'default_storage_engine'
			// for previous versions use 'storage_engine'
			if (version_compare($mysqlVersion,  "5.5") >= 0) {
				$this->exec("SET default_storage_engine = MyISAM");
			} else {
				$this->exec("SET storage_engine = MyISAM");
			}
		} else if ($this->type == self::SQLITE) {
			if ($host == null || $host == "" || $host == "localhost") {
				$dbName = "./data/$dbName";
			} else {
				$dbName = "$host/$dbName";
			}

			$this->sql = new PDO("sqlite:".$dbName);
			$this->sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} else {
			throw new Exception("Invalid database type: '$type'.  Expecting '" . self::MYSQL . "' or '" . self::SQLITE . "'.");
		}
	}

	function getType() {
		return $this->type;
	}

	function queryRow($sql) {
		$sql = $this->formatSql($sql);

		$args = $this->getParameters(func_get_args());

		$ps = $this->executeQuery($sql, $args);
		$result = $ps->fetchAll(PDO::FETCH_CLASS, 'budabot\core\DBRow');

		if (count($result) == 0) {
			return null;
		} else {
			return $result[0];
		}
	}

	function query($sql) {
		$sql = $this->formatSql($sql);

		$args = $this->getParameters(func_get_args());

		$ps = $this->executeQuery($sql, $args);
		return $ps->fetchAll(PDO::FETCH_CLASS, 'budabot\core\DBRow');
	}

	function exec($sql) {
		$sql = $this->formatSql($sql);

		if (substr_compare($sql, "create", 0, 6, true) == 0) {
			if ($this->type == self::MYSQL) {
				$sql = str_ireplace("AUTOINCREMENT", "AUTO_INCREMENT", $sql);
			} else if ($this->type == self::SQLITE) {
				$sql = str_ireplace("AUTO_INCREMENT", "AUTOINCREMENT", $sql);
				$sql = str_ireplace(" INT ", " INTEGER ", $sql);
			}
		}

		$args = $this->getParameters(func_get_args());

		$ps = $this->executeQuery($sql, $args);

		return $ps->rowCount();
	}
	
	private function getParameters($args) {
		array_shift($args);
		if (isset($args[0]) && is_array($args[0])) {
			return $args[0];
		} else {
			return $args;
		}
	}

	private function executeQuery($sql, $params) {
		$this->lastQuery = $sql;
		$this->logger->log('DEBUG', $sql . " - " . print_r($params, true));

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
			if ($this->type == self::SQLITE && $e->errorInfo[1] == 17) {
				// fix for Sqlite schema changed error (retry the query)
				return $this->executeQuery($sql, $params);
			}
			throw new SQLException("{$e->errorInfo[2]} in: $sql - " . print_r($params, true), 0, $e);
		}
	}

	//Start of an transaction
	function beginTransaction() {
		$this->logger->log('DEBUG', "Starting transaction");
		$this->inTransaction = true;
		$this->sql->beginTransaction();
	}

	//Commit an transaction
	function commit() {
		$this->logger->log('DEBUG', "Committing transaction");
		$this->inTransaction = false;
		$this->sql->Commit();
	}

	function rollback() {
		$this->logger->log('DEBUG', "Rolling back transaction");
		$this->inTransaction = false;
		$this->sql->rollback();
	}

	function inTransaction() {
		return $this->inTransaction;
	}

	//Return the last inserted ID
	function lastInsertId() {
		return $this->sql->lastInsertId();
	}

	function formatSql($sql) {
		$sql = str_replace("<dim>", $this->dim, $sql);
		$sql = str_replace("<myname>", $this->botname, $sql);
		$sql = str_replace("<myguild>", $this->guild, $sql);

		return $sql;
	}

	function getLastQuery() {
		return $this->lastQuery;
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
		if (!preg_match('/^[a-z0-9_]+$/i', $name)) {
			$msg = "Invalid SQL file name: '$name' for module: '$module'!  Only numbers, letters, and underscores permitted!";
			$this->logger->log('ERROR', $msg);
			return $msg;
		}

		$settingName = $name . "_db_version";

		$dir = $this->util->verifyFilename($module);
		if (empty($dir)) {
			$msg = "Could not find module '$module'.";
			$this->logger->log('ERROR', $msg);
			return $msg;
		}
		$d = dir($dir);

		if ($this->settingManager->exists($settingName)) {
			$currentVersion = $this->settingManager->get($settingName);
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

					if ($this->util->compareVersionNumbers($arr[1], $maxFileVersion) >= 0) {
						$maxFileVersion = $arr[1];
						$file = $entry;
					}
				}
			}
		}

		if ($file === false) {
			$msg = "No SQL file found with name '$name' in module '$module'!";
			$this->logger->log('ERROR', $msg);
			return $msg;
		}
		
		// make sure setting is verified so it doesn't get deleted
		$this->settingManager->add($module, $settingName, $settingName, 'noedit', 'text', 0);
		
		if ($forceUpdate || $this->util->compareVersionNumbers($maxFileVersion, $currentVersion) > 0) {
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

					$this->settingManager->save($settingName, $maxFileVersion);

					if ($maxFileVersion != 0) {
						$msg = "Updated '$name' database from '$currentVersion' to '$maxFileVersion'";
						$this->logger->log('DEBUG', $msg);
					} else {
						$msg = "Updated '$name' database";
						$this->logger->log('DEBUG', $msg);
					}
				} catch (SQLException $e) {
					$msg = "Error loading sql file '$file': " . $e->getMessage();
					$this->logger->log('ERROR', $msg);
				}
			} else {
				$msg = "Could not load SQL file: '$dir/$file'";
				$this->logger->log('ERROR', $msg);
			}
		} else {
			$msg = "'$name' database already up to date! version: '$currentVersion'";
			$this->logger->log('DEBUG', $msg);
		}

		return $msg;
	}
}

?>
