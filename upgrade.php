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
	
	if ($db->get_type() == DB::MYSQL && checkIfTableExists($db, 'scout_info') && getColumnType($db, 'scout_info', 'scouted_on') != 'int(11)') {
		$db->exec("ALTER TABLE scout_info MODIFY COLUMN scouted_on INT NOT NULL DEFAULT 0");
	}
	
	if ($db->get_type() == DB::MYSQL && checkIfTableExists($db, 'events') && getColumnType($db, 'events', 'event_date') != 'int(11)') {
		$db->exec("ALTER TABLE events MODIFY COLUMN time_submitted INT NOT NULL");
		$db->exec("ALTER TABLE events MODIFY COLUMN submitter_name VARCHAR(25) NOT NULL");
		$db->exec("ALTER TABLE events MODIFY COLUMN event_name VARCHAR(255) NOT NULL");
		$db->exec("ALTER TABLE events MODIFY COLUMN event_date INT");
	}
	
	// re-number quotes, remove OfWho column, rename columns
	if (checkIfTableExists($db, 'quote') && (checkIfColumnExists($db, 'quote', 'Who') || checkIfColumnExists($db, 'quote', 'OfWho') || checkIfColumnExists($db, 'quote', 'When') || checkIfColumnExists($db, 'quote', 'What'))) {
		if (checkIfColumnExists($db, 'quote', 'IDNumber')) {
			$data = $db->query("SELECT * FROM quote ORDER BY IDNumber ASC");
		} else {
			$data = $db->query("SELECT * FROM quote ORDER BY id ASC");
		}
		
		$db->exec("ALTER TABLE quote RENAME TO quote_backup");
		$db->exec("CREATE TABLE IF NOT EXISTS `quote` (`id` INTEGER NOT NULL PRIMARY KEY, `poster` VARCHAR(25) NOT NULL, `dt` INT NOT NULL, `msg` VARCHAR(1000) NOT NULL)");
		$quoteId = 1;
		forEach ($data as $row) {
			if (isset($row->Who)) {
				$poster = $row->Who;
			} else {
				$poster = $row->poster;
			}

			if (isset($row->When)) {
				$dt = $row->When;
			} else {
				$dt = $row->dt;
			}

			if (isset($row->What)) {
				$msg = $row->What;
			} else {
				$msg = $row->msg;
			}

			$db->exec("INSERT INTO `quote` (`id`, `poster`, `dt`, `msg`) VALUES (?, ?, ?, ?)", $quoteId, $poster, $dt, $msg);
			$quoteId++;
		}
	}
	
	if (checkIfTableExists($db, 'settings_<myname>')) {
		$db->exec("UPDATE settings_<myname> SET `value` = ? WHERE `name` = ?", "local", "items_database");
	}
	
	if (checkIfTableExists($db, 'usage_<myname>') && checkIfColumnExists($db, 'usage_<myname>', 'handler')) {
		$db->exec("ALTER TABLE usage_<myname> RENAME TO usage_<myname>_bak");
		$db->exec("CREATE TABLE usage_<myname> (type VARCHAR(5) NOT NULL, command VARCHAR(20) NOT NULL, sender VARCHAR(20) NOT NULL, dt INT NOT NULL)");
		$db->exec("INSERT INTO usage_<myname> SELECT type, command, sender, dt FROM usage_<myname>_bak ORDER BY dt ASC");
		$db->exec("DROP TABLE usage_<myname>_bak");
	}
	
	if (checkIfTableExists($db, 'reputation') && (checkIfColumnExists($db, 'reputation', 'charid') || checkIfColumnExists($db, 'reputation', 'by_charid'))) {
		$db->exec("ALTER TABLE reputation RENAME TO reputation_bak");
		$db->exec("CREATE TABLE IF NOT EXISTS reputation (`id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT, `name` TEXT NOT NULL, `reputation` TEXT NOT NULL, `comment` TEXT NOT NULL, `by` TEXT NOT NULL, `dt` INT NOT NULL)");
		$db->exec("INSERT INTO reputation SELECT `id`, `name`, `reputation`, `comment`, `by`, `dt` FROM reputation_bak");
		$db->exec("DROP table reputation_bak");
	}
?>
