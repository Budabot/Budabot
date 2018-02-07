<?php

use Budabot\Core\DB;
use Budabot\Core\SQLException;
use Budabot\Core\Registry;
use Budabot\Core\LoggerWrapper;

$db = Registry::getInstance('db');

/**
 * Returns array of information of each column in the given $table.
 */
function describeTable($db, $table) {
	$results = array();

	switch ($db->getType()) {
		case DB::MYSQL:
			$rows = $db->query("DESCRIBE $table");
			// normalize the output somewhat to make it more compatible with sqlite
			forEach ($rows as $row) {
				$row->name = $row->Field;
				unset($row->Field);
				$row->type = $row->Type;
				unset($row->Type);
			}
			$results = $rows;
			break;

		case DB::SQLITE:
			$results = $db->query("PRAGMA table_info($table)");
			break;

		default:
			throw new Exception("Unknown database type '". $db->getType() ."'");
	}

	return $results;
}

/**
 * Returns db-type of given $column name as a string.
 */
function getColumnType($db, $table, $column) {
	$column = strtolower($column);
	$columns = describeTable($db, $table);
	forEach ($columns as $col) {
		if (strtolower($col->name) == $column) {
			return strtolower($col->type);
		}
	}
	return null;
}

function checkIfTableExists($db, $table) {
	try {
		$data = $db->query("SELECT * FROM $table LIMIT 1");
	} catch (SQLException $e) {
		return false;
	}
	return true;
}

function checkIfColumnExists($db, $table, $column) {
	try {
		$data = $db->query("SELECT $column FROM $table LIMIT 1");
	} catch (SQLException $e) {
		return false;
	}
	return true;
}

function normalizeVersion($version) {
	// RC versions should come before GA versions when sorted in ASCENDING direction
	return
		str_replace("_RC", ".0.", 
			str_replace("_GA", ".1", $version));
}

function minRequiredVersion($db, $minVersion) {
	if (checkIfTableExists($db, "settings_<myname>")) {
		global $version;
		if (version_compare(normalizeVersion($version), normalizeVersion($minVersion), '<')) {
			throw new Exception("Current required version is too old; you must upgrade to version $minVersion first before upgrading to $version");
		}
	}
}

minRequiredVersion($db, "3.5_GA");

if (checkIfTableExists($db, "cmd_alias_<myname>")) {
	$db->exec("DELETE FROM cmd_alias_<myname> WHERE alias = ? AND cmd = ?", "whatbuffs", "whatbuffs2");
}

if (checkIfTableExists($db, "players")) {
	if (!checkIfColumnExists($db, "players", "head_id")) {
		$db->exec("ALTER TABLE players ADD COLUMN `head_id` INT DEFAULT NULL");
	}
	if (!checkIfColumnExists($db, "players", "pvp_rating")) {
		$db->exec("ALTER TABLE players ADD COLUMN `pvp_rating` SMALLINT DEFAULT NULL");
	}
	if (!checkIfColumnExists($db, "players", "pvp_title")) {
		$db->exec("ALTER TABLE players ADD COLUMN `pvp_title` VARCHAR(20) DEFAULT NULL");
	}
}