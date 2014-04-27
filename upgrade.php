<?php

use Budabot\Core\DB;
use Budabot\Core\SQLException;
use Budabot\Core\Registry;

	/*
	 ** This file is part of Budabot.
	 **
	 ** Budabot is free software: you can redistribute it and/org modify
	 ** it under the terms of the GNU General Public License as published by
	 ** the Free Software Foundation, either version 3 of the License, or
	 ** (at your option) any later version.
	 **
	 ** Budabot is distributed in the hope that it will be useful,
	 ** but WITHOUT ANY WARRANTY; without even the implied warranty of
	 ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 ** GNU General Public License for more details.
	 **
	 ** You should have received a copy of the GNU General Public License
	 ** along with Budabot. If not, see <http://www.gnu.org/licenses/>.
	*/

	$db = Registry::getInstance('db');

	/**
	 * Returns array of information of each column in the given $table.
	 */
	function describeTable($db, $table) {
		$results = array();
		try {
			switch ($db->get_type()) {
				case DB::MYSQL:
					$rows = $db->query("DESCRIBE $table");
					// normalize the output somewhat to make it more compatible with sqlite
					forEach ($rows as $row) {
						$row->name = $row->Field;
						unset($row->Field);
						$row->type = $row->Type;
						unset($row->Type);
					}
					return $rows;

				case DB::SQLITE:
					return $db->query("PRAGMA table_info($table)");

				default:
					LegacyLogger::log("ERROR", 'Upgrade', "Unknown database type '". $db->get_type() ."'");
					break;
			}
		} catch (SQLException $e) {
			LegacyLogger::log("ERROR", 'Upgrade', $e->getMessage());
		}
		return array();
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

	function upgrade($db, $sql, $params = null) {
		try {
			$db->exec($sql);
		} catch (SQLException $e) {
			LegacyLogger::log("ERROR", 'Upgrade', $e->getMessage());
		}
	}
	
	function loadSQLFile($db, $filename) {
		$lines = explode("\n", file_get_contents($filename));
		forEach ($lines as $line) {
			upgrade($db, $line);
		}
	}
	
	// if roll table has 'type' column, then drop it so it can be reloaded with new schema changes
	// it shouldn't matter if the data in that table is lost -Tyrence
	if (checkIfColumnExists($db, 'roll', 'type')) {
		$db->exec("DROP TABLE roll");
	}
	
	if ($db->get_type() == DB::MYSQL && getColumnType($db, 'cmdcfg_<myname>', 'cmd') != 'VARCHAR(50)') {
		$db->exec("ALTER TABLE cmdcfg_<myname> MODIFY cmd VARCHAR(50)");
	}
?>
