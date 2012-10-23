<?php

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

	/**
	 * Returns array of information of each column in the given $table.
	 */
	function describeTable($db, $table) {
		$results = array();
		try {
			switch ($db->get_type()) {
			case 'mysql':
				$rows = $db->query("DESCRIBE $table");
				// normalize the output somewhat to make it more compatible
				// with sqlite
				forEach ($rows as $row) {
					$row->name = $row->Field;
					unset($row->Field);
					$row->type = $row->Type;
					unset($row->Type);
				}
				return $rows;

			case 'sqlite':
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
	}
	
	function checkIfTableExists($db, $table) {
		// If the table doesn't exist, return true since the table will be created with the correct column.
		try {
			$data = $db->query("SELECT * FROM $table");
		} catch (SQLException $e) {
			return false;
		}
		return true;
	}
	
	function checkIfColumnExists($db, $table, $column) {
		// If the table doesn't exist, return true since the table will be created with the correct column.
		if (checkIfTableExists($db, $table) == false) {
			return true;
		}

		// Else if the table exists but the column doesn't, return false so the table will be updated with the correct column.
		try {
			$data = $db->query("SELECT $column FROM $table");
		} catch (SQLException $e) {
			return false;
		}

		// Else return true because both the table and the column exist.
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

	try {
		if (checkIfTableExists($db, "org_members_<myname>")) {
			$data = $db->query("SELECT * FROM org_members_<myname>");
			if (property_exists($data[0], 'logon_msg') || property_exists($data[0], 'logoff_msg')) {
				loadSQLFile($db, "./core/PREFERENCES/preferences.sql");
				forEach ($data as $row) {
					if (isset($row->logon_msg) && $row->logon_msg != '') {
						$logon = $db->queryRow("SELECT * FROM preferences_<myname> WHERE sender = ? AND name = ?", $row->name, 'logon_msg');
						if ($logon === null) {
							$db->exec("INSERT INTO preferences_<myname> (sender, name, value) VALUES (?, ?, ?)", $row->name, 'logon_msg', $row->logon_msg);
						} else {
							$db->exec("UPDATE preferences_<myname> SET value = ? WHERE sender = ? AND name = ?", $row->logon_msg, $row->name, 'logon_msg');
						}
					}
					if (isset($row->logoff_msg) && $row->logoff_msg != '') {
						$logoff = $db->queryRow("SELECT * FROM preferences_<myname> WHERE sender = ? AND name = ?", $row->name, 'logoff_msg');
						if ($logoff === null) {
							$db->exec("INSERT INTO preferences_<myname> (sender, name, value) VALUES (?, ?, ?)", $row->name, 'logoff_msg', $row->logoff_msg);
						} else {
							$db->exec("UPDATE preferences_<myname> SET value = ? WHERE sender = ? AND name = ?", $row->logoff_msg, $row->name, 'logoff_msg');
						}
					}
				}
				upgrade($db, "UPDATE org_members_<myname> SET logon_msg = '', logoff_msg = ''");
			}
		}
	} catch (SQLException $e) {
		LegacyLogger::log("ERROR", 'Upgrade', $e->getMessage());
	}

	if (!checkIfColumnExists($db, "news", "sticky")) {
		upgrade($db, "ALTER TABLE news ADD `sticky` TINYINT NOT NULL DEFAULT 0");
	}

	if (checkIfTableExists($db, "eventcfg_<myname>")) {
		upgrade($db, "UPDATE eventcfg_<myname> SET type = LOWER(type)");
	}

	if (checkIfTableExists($db, "cmdcfg_<myname>")) {
		upgrade($db, "UPDATE cmdcfg_<myname> SET admin = 'rl' WHERE admin = 'leader'");
	}
	if (checkIfTableExists($db, "hlpcfg_<myname>")) {
		upgrade($db, "UPDATE hlpcfg_<myname> SET admin = 'rl' WHERE admin = 'leader'");
	}
	if (checkIfTableExists($db, "settings_<myname>")) {
		upgrade($db, "UPDATE settings_<myname> SET admin = 'rl' WHERE admin = 'leader'");
		$row = $db->queryRow("SELECT * FROM settings_<myname> WHERE name = 'relaysymbol'");
		if ($row->value = 'Always relay') {
			upgrade($db, "UPDATE settings_<myname> SET value = '@' WHERE name = 'relaysymbol'");
			upgrade($db, "UPDATE settings_<myname> SET value = '0' WHERE name = 'relaysymbolmethod'");
		} else {
			upgrade($db, "UPDATE settings_<myname> SET value = ? WHERE name = 'relaysymbol'", $row->value);
			upgrade($db, "UPDATE settings_<myname> SET value = '1' WHERE name = 'relaysymbolmethod'");
		}
	}

	if (checkIfTableExists($db, "cmd_alias_<myname>")) {
		upgrade($db, "DELETE FROM cmd_alias_<myname> WHERE alias = 'kickuser'");
		upgrade($db, "DELETE FROM cmd_alias_<myname> WHERE alias = 'inviteuser'");
	}

	// change cmdcfg_<myname> table's file-column type from VARCHAR(255) to TEXT
	try {
		if (checkIfTableExists($db, "cmdcfg_<myname>") && getColumnType($db, 'cmdcfg_<myname>', 'file') == 'varchar(255)') {
			$db->begin_transaction();
			$db->exec("ALTER TABLE cmdcfg_<myname> RENAME TO tmp_cmdcfg_<myname>");
			// copied from Budabot.class.php (without 'IF NOT EXISTS')
			$db->exec("CREATE TABLE cmdcfg_<myname> (`module` VARCHAR(50), `cmdevent` VARCHAR(6), `type` VARCHAR(18), `file` TEXT, `cmd` VARCHAR(25), `admin` VARCHAR(10), `description` VARCHAR(50) DEFAULT 'none', `verify` INT DEFAULT '0', `status` INT DEFAULT '0', `dependson` VARCHAR(25) DEFAULT 'none', `help` VARCHAR(25))");
			$db->exec("INSERT INTO cmdcfg_<myname> SELECT * FROM tmp_cmdcfg_<myname>");
			$db->exec("DROP TABLE tmp_cmdcfg_<myname>");
			$db->commit();
		}
	} catch (SQLException $e) {
		LegacyLogger::log("ERROR", 'Upgrade', $e->getMessage());
		$db->rollback();
	}

?>
