<?php

use Budabot\Core\DB;
use Budabot\Core\SQLException;
use Budabot\Core\Registry;
use Budabot\Core\LoggerWrapper;

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
				$results = $rows;
				break;

			case DB::SQLITE:
				$results = $db->query("PRAGMA table_info($table)");
				break;

			default:
				throw new Exception("Unknown database type '". $db->get_type() ."'");
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
	
	// set default items db to central
	if (checkIfTableExists($db, "settings_<myname>")) {
		$row = $db->queryRow("SELECT * FROM settings_<myname> WHERE name = ?", 'cidb_url');
		if ($row === null) {
			$db->exec("UPDATE settings_<myname> SET value = ? WHERE name = ?", 'central', 'items_database');
		}
	}
?>
