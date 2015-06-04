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
	
	if (checkIfTableExists($db, 'usage_<myname>') && !checkIfColumnExists($db, 'usage_<myname>', 'handler')) {
		$db->exec("ALTER TABLE usage_<myname> ADD COLUMN handler VARCHAR(100) NOT NULL DEFAULT ''");
	}
	
	if ($db->get_type() == DB::MYSQL && checkIfTableExists($db, 'scout_info') && getColumnType($db, 'scout_info', 'scouted_on') != 'int(11)') {
		$db->exec("ALTER TABLE scout_info MODIFY COLUMN scouted_on INT NOT NULL DEFAULT 0");
	}
	
	if ($db->get_type() == DB::MYSQL && checkIfTableExists($db, 'events') && getColumnType($db, 'events', 'event_date') != 'int(11)') {
		$db->exec("ALTER TABLE events MODIFY COLUMN time_submitted INT NOT NULL");
		$db->exec("ALTER TABLE events MODIFY COLUMN submitter_name VARCHAR(25) NOT NULL");
		$db->exec("ALTER TABLE events MODIFY COLUMN event_name VARCHAR(255) NOT NULL");
		$db->exec("ALTER TABLE events MODIFY COLUMN event_date INT");
	}

	// re-number quotes, rename IDNumber column to id
	if (checkIfTableExists($db, 'quote') && checkIfColumnExists($db, 'quote', 'IDNumber')) {
		$data = $db->query("SELECT * FROM quote ORDER BY IDNumber ASC");
		$db->exec("DROP TABLE quote");
		$db->exec("CREATE TABLE IF NOT EXISTS `quote` (`id` INTEGER NOT NULL PRIMARY KEY, `poster` VARCHAR(25) NOT NULL, `OfWho` VARCHAR(25) NOT NULL, `When` INT NOT NULL, `What` VARCHAR(1000) NOT NULL)");
		$quoteId = 1;
		forEach ($data as $row) {
			$quoteMSG = $row->What;
			if (preg_match("/^(\(\d\d:\d\d\) )?To \[([a-z0-9-]+)\]:/i", $quoteMSG, $arr)) {
				//To [Person]: message
				$quoteOfWHO = $arr[2];
			} else if (preg_match("/^(\(\d\d:\d\d\) )?\[([a-z0-9-]+)\]:/i", $quoteMSG, $arr)) {
				//[Person]: message
				$quoteOfWHO = $arr[2];
			} else if (preg_match("/^(\(\d\d:\d\d\) )?\[[^\]]+\] ([a-z0-9-]+):/i", $quoteMSG, $arr)) {
				//[Neu. OOC] Lucier: message
				$quoteOfWHO = $arr[2];
			} else if (preg_match("/^(\(\d\d:\d\d\) )?([a-z0-9-]+) shouts:/i", $quoteMSG, $arr)) {
				//Lucier shouts: message
				$quoteOfWHO = $arr[2];
			} else if (preg_match("/^(\(\d\d:\d\d\) )?([a-z0-9-]+) whispers:/i", $quoteMSG, $arr)) {
				//Lucier whispers: message
				$quoteOfWHO = $arr[2];
			} else if (preg_match("/^(\(\d\d:\d\d\) )?([a-z0-9-]+):/i", $quoteMSG, $arr)) {
				//Lucier: message
				$quoteOfWHO = $arr[2];
			} else {
				$quoteOfWHO = $row->Who;
			}
			
			$db->exec("INSERT INTO `quote` (`id`, `poster`, `OfWho`, `When`, `What`) VALUES (?, ?, ?, ?, ?)", $quoteId, $row->Who, $quoteOfWHO, $row->When, $row->What);
			$quoteId++;
		}
	}
?>
