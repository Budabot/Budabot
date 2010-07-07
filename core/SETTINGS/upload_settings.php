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

//Uploading all needed settings from config.php to the db
//Prefix for commands
$symbol = str_replace("'", "''", $this->settings["symbol"]);
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'symbol'");
if($db->numrows() == 0)
  	$db->query("INSERT INTO settings_<myname> (`name`, `module`, `mode`, `setting`, `options`, `description`, `source`, `admin`) VALUES ('symbol', 'Basic Settings', 'edit', '$symbol', '!;#;*;@;$;+;-', 'Prefix for Guild- or Privatechat Commands', 'cfg', 'mod')");
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = '$symbol' WHERE `name` = 'symbol'");

//Debug messages
$debug = str_replace("'", "''", $this->settings["debug"]);
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'debug'");
if($db->numrows() == 0)
  	$db->query("INSERT INTO settings_<myname> (`name`, `module`, `mode`, `setting`, `options`, `intoptions`, `description`, `source`, `admin`) VALUES ('debug', 'Basic Settings', 'edit', '$debug', 'Disabled;Show basic msg''s;Show enhanced debug msg''s;Show enhanced debug msg''s + 1s Delay', '0;1;2;3', 'Show debug messages', 'cfg', 'admin')");
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = '$debug' WHERE `name` = 'debug'");

//Log Messages
$echo = str_replace("'", "''", $this->settings["echo"]);
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'echo'");
if($db->numrows() == 0)
  	$db->query("INSERT INTO settings_<myname> (`name`, `module`, `mode`, `setting`, `options`, `intoptions`, `description`, `source`, `admin`) VALUES ('echo', 'Basic Settings', 'edit', '$echo', 'Disabled;Only Console;Console and Logfiles','0;1;2' ,'Show messages in console and log them to files', 'cfg', 'admin')");
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = '$echo' WHERE `name` = 'echo'");

//Guildadmin
$guild_admin_level = str_replace("'", "''", $this->settings["guild admin level"]);
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'guild admin level'");
if($db->numrows() == 0)
  	$db->query("INSERT INTO settings_<myname> (`name`, `module`, `mode`, `setting`, `options`, `intoptions`, `description`, `source`, `admin`) VALUES ('guild admin level', 'Basic Settings', 'edit', '$guild_admin_level', 'President;General;Squad Commander;Unit Commander;Unit Leader;Unit Member;Applicant', '0;1;2;3;4;5;6', 'Min Level for Rank Guildadmin', 'cfg', 'admin')");
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = '$guild_admin_level' WHERE `name` = 'guild admin level'");

//Default guild Colors
$default_guild_color = str_replace("'", "''", $this->settings["default guild color"]);
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'default guild color'");
if($db->numrows() == 0)
  	$db->query("INSERT INTO settings_<myname> (`name`, `module`, `mode`, `setting`, `options`, `description`, `source`, `admin`) VALUES ('default guild color', 'Basic Settings', 'edit', '$default_guild_color', 'color', 'Default Guild Color', 'cfg', 'admin')");
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = '$default_guild_color' WHERE `name` = 'default guild color'");

//Default private Colors
$default_priv_color = str_replace("'", "''", $this->settings["default priv color"]);
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'default priv color'");
if($db->numrows() == 0)
 	$db->query("INSERT INTO settings_<myname> (`name`, `module`, `mode`, `setting`, `options`, `description`, `source`, `admin`) VALUES ('default priv color', 'Basic Settings', 'edit', '$default_priv_color', 'color', 'Default Private Color', 'cfg', 'admin')");
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = '$default_priv_color' WHERE `name` = 'default priv color'");

//Default Window Colors
$default_window_color = str_replace("'", "''", $this->settings["default window color"]);
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'default window color'");
if($db->numrows() == 0)
	$db->query("INSERT INTO settings_<myname> (`name`, `module`, `mode`, `setting`, `options`, `description`, `source`, `admin`) VALUES ('default window color', 'Basic Settings', 'edit', '$default_window_color', 'color', 'Default Window Color', 'cfg', 'admin')"); 	
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = '$default_window_color' WHERE `name` = 'default window color'");

//Default tell Colors
$default_tell_color = str_replace("'", "''", $this->settings["default tell color"]);
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'default tell color'");
if($db->numrows() == 0)
	$db->query("INSERT INTO settings_<myname> (`name`, `module`, `mode`, `setting`, `options`, `description`, `source`, `admin`) VALUES ('default tell color', 'Basic Settings', 'edit', '$default_tell_color', 'color', 'Default Tell Color', 'cfg', 'admin')"); 	
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = '$default_tell_color' WHERE `name` = 'default tell color'");

//Default Highlight Color
$default_highlight_color = str_replace("'", "''", $this->settings["default highlight color"]);
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'default highlight color'");
if($db->numrows() == 0)
	$db->query("INSERT INTO settings_<myname> (`name`, `module`, `mode`, `setting`, `options`, `description`, `source`, `admin`) VALUES ('default highlight color', 'Basic Settings', 'edit', '$default_highlight_color', 'color', 'Default Highlight Color', 'cfg', 'admin')"); 	
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = '$default_highlight_color' WHERE `name` = 'default highlight color'");

//Default Header Colors
$default_header_color = str_replace("'", "''", $this->settings["default header color"]);
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'default header color'");
if($db->numrows() == 0)
	$db->query("INSERT INTO settings_<myname> (`name`, `module`, `mode`, `setting`, `options`, `description`, `source`, `admin`) VALUES ('default header color', 'Basic Settings', 'edit', '$default_header_color', 'color', 'Default Header Color', 'cfg', 'admin')"); 	
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = '$default_header_color' WHERE `name` = 'default header color'");

//Spam Protection
$spam_protection = str_replace("'", "''", $this->settings["spam protection"]);
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'spam protection'");
if($db->numrows() == 0)
	$db->query("INSERT INTO settings_<myname> (`name`, `module`, `mode`, `setting`, `options`, `intoptions`, `description`, `source`, `admin`, `help`) VALUES ('spam protection', 'Basic Settings', 'edit', '$spam_protection', 'ON;OFF', '1;0', 'Spam Protection for Private Chat', 'cfg', 'admin', './core/SETTINGS/spam_help.txt')"); 	
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = '$spam_protection' WHERE `name` = 'spam protection'");

//Default module status
$default_module_status = str_replace("'", "''", $this->settings["default module status"]);
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'default module status'");
if($db->numrows() == 0)
	$db->query("INSERT INTO settings_<myname> (`name`, `module`, `mode`, `setting`, `options`, `intoptions`, `description`, `source`, `admin`, `help`) VALUES ('default module status', 'Basic Settings', 'edit', '$default_module_status', 'ON;OFF', '1;0', 'Default Status for new Modules', 'cfg', 'admin', './core/SETTINGS/module_status_help.txt')"); 	
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = '$default_module_status' WHERE `name` = 'default module status'");

//Max Blob size
$max_blob_size = str_replace("'", "''", $this->settings["max_blob_size"]);
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'max_blob_size'");
if($db->numrows() == 0)
	$db->query("INSERT INTO settings_<myname> (`name`, `module`, `mode`, `setting`, `options`, `description`, `source`, `admin`, `help`) VALUES ('max_blob_size', 'Basic Settings', 'edit', '$max_blob_size', 'number', 'Max chars for a window', 'cfg', 'admin', './core/SETTINGS/max_blob_size_help.txt')"); 	
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = '$max_blob_size' WHERE `name` = 'max_blob_size'");

//Upload Settings from the db that are set by modules
$db->query("SELECT * FROM settings_<myname> WHERE `source` = 'db'");
while($row = $db->fObject())
	$this->settings[$row->name] = $row->setting;
?>