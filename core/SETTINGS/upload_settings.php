<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Uploads Settings to the db
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 05.02.2006
   ** Date(last modified): 21.11.2006
   ** 
   ** Copyright (C) 2006 Carsten Lohmann
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
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'symbol'");
if($db->numrows() == 0)
  	$db->query("INSERT INTO settings_<myname> (`name`, `mod`, `mode`, `setting`, `options`, `description`, `source`, `admin`) VALUES ('symbol', 'Basic Settings', 'edit', '{$this->settings["symbol"]}', '!;#;*;§;$;+', 'Prefix for Guild- or Privatechat Commands', 'cfg', 'mod')");
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = '{$this->settings["symbol"]}' WHERE `name` = 'symbol'");

//Debug messages
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'debug'");
if($db->numrows() == 0)
  	$db->query("INSERT INTO settings_<myname> (`name`, `mod`, `mode`, `setting`, `options`, `intoptions`, `description`, `source`, `admin`) VALUES ('debug', 'Basic Settings', 'edit', '{$this->settings["debug"]}', 'Disabled;Show basic msg´s;Show enhanced debug msg´s;Show enhanced debug msg´s + 1s Delay', '0;1;2;3', 'Show debug messages', 'cfg', 'admin')");
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = '{$this->settings["debug"]}' WHERE `name` = 'debug'");

//Log Messages
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'echo'");
if($db->numrows() == 0)
  	$db->query("INSERT INTO settings_<myname> (`name`, `mod`, `mode`, `setting`, `options`, `intoptions`, `description`, `source`, `admin`) VALUES ('echo', 'Basic Settings', 'edit', '{$this->settings["echo"]}', 'Disabled;Only Console;Console and Logfiles','0;1;2' ,'Show messages in console and log them to files', 'cfg', 'admin')");
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = '{$this->settings["echo"]}' WHERE `name` = 'echo'");

//Guildadmin
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'guild admin level'");
if($db->numrows() == 0)
  	$db->query("INSERT INTO settings_<myname> (`name`, `mod`, `mode`, `setting`, `options`, `intoptions`, `description`, `source`, `admin`) VALUES ('guild admin level', 'Basic Settings', 'edit', '{$this->settings["guild admin level"]}', 'President;General;Squad Commander;Unit Commander;Unit Leader;Unit Member;Applicant', '0;1;2;3;4;5;6', 'Min Level for Rank Guildadmin', 'cfg', 'admin')");
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = '{$this->settings["guild admin level"]}' WHERE `name` = 'guild admin level'");

//Default guild Colors
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'default guild color'");
if($db->numrows() == 0)
  	$db->query("INSERT INTO settings_<myname> (`name`, `mod`, `mode`, `setting`, `options`, `description`, `source`, `admin`) VALUES ('default guild color', 'Basic Settings', 'edit', \"{$this->settings["default guild color"]}\", 'color', 'Default Guild Color', 'cfg', 'admin')");
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = \"{$this->settings["default guild color"]}\" WHERE `name` = 'default guild color'");

//Default private Colors
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'default priv color'");
if($db->numrows() == 0)
 	$db->query("INSERT INTO settings_<myname> (`name`, `mod`, `mode`, `setting`, `options`, `description`, `source`, `admin`) VALUES ('default priv color', 'Basic Settings', 'edit', \"{$this->settings["default priv color"]}\", 'color', 'Default Private Color', 'cfg', 'admin')");
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = \"{$this->settings["default priv color"]}\" WHERE `name` = 'default priv color'");

//Default Window Colors
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'default window color'");
if($db->numrows() == 0)
	$db->query("INSERT INTO settings_<myname> (`name`, `mod`, `mode`, `setting`, `options`, `description`, `source`, `admin`) VALUES ('default window color', 'Basic Settings', 'edit', \"{$this->settings["default window color"]}\", 'color', 'Default Window Color', 'cfg', 'admin')"); 	
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = \"{$this->settings["default window color"]}\" WHERE `name` = 'default window color'");

//Default tell Colors
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'default tell color'");
if($db->numrows() == 0)
	$db->query("INSERT INTO settings_<myname> (`name`, `mod`, `mode`, `setting`, `options`, `description`, `source`, `admin`) VALUES ('default tell color', 'Basic Settings', 'edit', \"{$this->settings["default tell color"]}\", 'color', 'Default Tell Color', 'cfg', 'admin')"); 	
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = \"{$this->settings["default tell color"]}\" WHERE `name` = 'default tell color'");

//Default Highlight Color
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'default highlight color'");
if($db->numrows() == 0)
	$db->query("INSERT INTO settings_<myname> (`name`, `mod`, `mode`, `setting`, `options`, `description`, `source`, `admin`) VALUES ('default highlight color', 'Basic Settings', 'edit', \"{$this->settings["default highlight color"]}\", 'color', 'Default Highlight Color', 'cfg', 'admin')"); 	
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = \"{$this->settings["default highlight color"]}\" WHERE `name` = 'default highlight color'");

//Default Header Colors
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'default header color'");
if($db->numrows() == 0)
	$db->query("INSERT INTO settings_<myname> (`name`, `mod`, `mode`, `setting`, `options`, `description`, `source`, `admin`) VALUES ('default header color', 'Basic Settings', 'edit', \"{$this->settings["default header color"]}\", 'color', 'Default Header Color', 'cfg', 'admin')"); 	
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = \"{$this->settings["default header color"]}\" WHERE `name` = 'default header color'");

//Spam Protection
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'spam protection'");
if($db->numrows() == 0)
	$db->query("INSERT INTO settings_<myname> (`name`, `mod`, `mode`, `setting`, `options`, `intoptions`, `description`, `source`, `admin`, `help`) VALUES ('spam protection', 'Basic Settings', 'edit', \"{$this->settings["spam protection"]}\", 'ON;OFF', '1;0', 'Spam Protection for Private Chat', 'cfg', 'admin', './core/SETTINGS/spam_help.txt')"); 	
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = '{$this->settings["spam protection"]}' WHERE `name` = 'spam protection'");

//Default module status
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'default module status'");
if($db->numrows() == 0)
	$db->query("INSERT INTO settings_<myname> (`name`, `mod`, `mode`, `setting`, `options`, `intoptions`, `description`, `source`, `admin`, `help`) VALUES ('default module status', 'Basic Settings', 'edit', \"{$this->settings["default module status"]}\", 'ON;OFF', '1;0', 'Default Status for new Modules', 'cfg', 'admin', './core/SETTINGS/module_status_help.txt')"); 	
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = '{$this->settings["default module status"]}' WHERE `name` = 'default module status'");

//Max Blob size
$db->query("SELECT * FROM settings_<myname> WHERE `name` = 'max_blob_size'");
if($db->numrows() == 0)
	$db->query("INSERT INTO settings_<myname> (`name`, `mod`, `mode`, `setting`, `options`, `description`, `source`, `admin`, `help`) VALUES ('max_blob_size', 'Basic Settings', 'edit', \"{$this->settings["max_blob_size"]}\", 'number', 'Max chars for a window', 'cfg', 'admin', './core/SETTINGS/max_blob_size_help.txt')"); 	
else 
  	$db->query("UPDATE settings_<myname> SET `setting` = '{$this->settings["max_blob_size"]}' WHERE `name` = 'max_blob_size'");

//Upload Settings from the db that are set by modules
$db->query("SELECT * FROM settings_<myname> WHERE `source` = 'db'");
while($row = $db->fObject())
	$this->settings[$row->name] = $row->setting;
?>