<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Uploads Settings to the db
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 05.02.2006
   ** Date(last modified): 05.02.2007
   ** 
   ** Copyright (C) 2006, 2007 Carsten Lohmann
   **
   ** Licence Infos: 
   ** This file is part of Budabot.
   **
   ** Budabot is free software; you can redistribute it and/or modify
   ** it under the terms of the GNU General Public License as published by
   ** the Free Software Foundation; either version 2 of the License, or
   ** (at your option) any later version.
   **
   ** Budabot is distributed in the hope that it will be useful,
   ** but WITHOUT ANY WARRANTY; without even the implied warranty of
   ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   ** GNU General Public License for more details.
   **
   ** You should have received a copy of the GNU General Public License
   ** along with Budabot; if not, write to the Free Software
   ** Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   */
   
if (!function_exists("save_setting_to_db")) {
	function save_setting_to_db($name, $value, $options, $intoptions, $description, $help) {
		$db = db::get_instance();
	
		$name = str_replace("'", "''", $name);
		$value = str_replace("'", "''", $value);
		$options = str_replace("'", "''", $options);
		$intoptions = str_replace("'", "''", $intoptions);
		$description = str_replace("'", "''", $description);
		$help = str_replace("'", "''", $help);
		$db->query("SELECT * FROM settings_<myname> WHERE `name` = '$name'");
		if ($db->numrows() == 0) {
			$db->exec("INSERT INTO settings_<myname> (`name`, `module`, `mode`, `setting`, `options`, `intoptions`, `description`, `source`, `admin`, `help`)
				VALUES ('$name', 'Basic Settings', 'edit', '$value', '$options', '$intoptions', '$description', 'cfg', 'mod', '$help')");
		}
	}
}

save_setting_to_db('default_module_status', $this->settings["default_module_status"], 'ON;OFF', '1;0', 'Default Status for new Modules', 'SETTINGS/module_status_help.txt');
save_setting_to_db('max_blob_size', $this->settings["max_blob_size"], 'number', null, 'Max chars for a window', 'SETTINGS/max_blob_size_help.txt');

//Upload Settings from the db that are set by modules
$db->query("SELECT * FROM settings_<myname>");
while ($row = $db->fObject()) {
	$this->settings[$row->name] = $row->setting;
}

?>