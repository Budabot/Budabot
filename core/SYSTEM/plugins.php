<?php
   /*
   ** Author: Sebuda (RK2)
   ** Description: Reload all modules and settings
   ** Version: 0.2
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 01.10.2005
   ** Date(last modified): 21.11.2006
   ** 
   ** Copyright (C) 2005, 2006 J. Gracik
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

//Sending Notify about the reload
bot::send("Reloading all Modules. This can take a few seconds depending on your config.", $sendto);
Logger::log('INFO', 'StartUp', "Loading USER modules...");

//Delete old vars
unset($this->subcommands);
unset($this->tellCmds);
unset($this->privCmds);
unset($this->guildCmds);
unset($this->towers);
unset($this->orgmsg);
unset($this->privMsgs);
unset($this->privChat);
unset($this->guildChat);
unset($this->joinPriv);
unset($this->leavePriv);
unset($this->logOn);
unset($this->logOff);
unset($this->_2sec);
unset($this->_1min);
unset($this->_10mins);
unset($this->_15mins);
unset($this->_30mins);
unset($this->_1hour);
unset($this->_24hrs);
unset($this->_connect);
unset($this->helpfiles);

//Prepare DB
$db->exec("UPDATE cmdcfg_<myname> SET `verify` = 0");
$db->exec("UPDATE cmdcfg_<myname> SET `status` = 0 WHERE `cmdevent` = 'event' AND `type` = 'setup'");
$db->exec("UPDATE cmdcfg_<myname> SET `grp` = 'none'");
$db->exec("DELETE FROM cmdcfg_<myname> WHERE `module` = 'none'");

//Getting existing commands, events and so on
$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'cmd'");
while($row = $db->fObject())
  	$this->existing_commands[$row->type][$row->cmd] = true;

$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'subcmd'");
while($row = $db->fObject())
  	$this->existing_subcmds[$row->type][$row->cmd] = true;

$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'event'");
while($row = $db->fObject())
  	$this->existing_events[$row->type][$row->file] = true;

$db->query("SELECT * FROM hlpcfg_<myname>");
while($row = $db->fObject())
  	$this->existing_helps[$row->name] = true;
		  	
$db->query("SELECT * FROM settings_<myname>");
while($row = $db->fObject())
  	$this->existing_settings[$row->name] = true;

// Load the Core Modules -- SETINGS must be first in case the other modules have settings
Logger::log('debug', 'Core', ":::::::CORE MODULES::::::::");

Logger::log('debug', 'Core', "MODULE_NAME:(SETTINGS.php)");
include "./core/SETTINGS/SETTINGS.php";

Logger::log('debug', 'Core', "MODULE_NAME:(SYSTEM.php)");
include "./core/SYSTEM/SYSTEM.php";

Logger::log('debug', 'Core', "MODULE_NAME:(ADMIN.php)");
include "./core/ADMIN/ADMIN.php";

Logger::log('debug', 'Core', "MODULE_NAME:(BAN.php)");
include "./core/BAN/BAN.php";

Logger::log('debug', 'Core', "MODULE_NAME:(HELP.php)");
include "./core/HELP/HELP.php";

Logger::log('debug', 'Core', "MODULE_NAME:(CONFIG.php)");
include "./core/CONFIG/CONFIG.php";

Logger::log('debug', 'Core', "MODULE_NAME:(BASIC_CONNECTED_EVENTS.php)\n");
include "./core/BASIC_CONNECTED_EVENTS/BASIC_CONNECTED_EVENTS.php";

Logger::log('debug', 'Core', "MODULE_NAME:(PRIV_TELL_LIMIT.php)\n");
include "./core/PRIV_TELL_LIMIT/PRIV_TELL_LIMIT.php";

Logger::log('debug', 'Core', "MODULE_NAME:(PLAYER_LOOKUP.php)\n");
include "./core/PLAYER_LOOKUP/PLAYER_LOOKUP.php";

// Load Plugin Modules
Logger::log('debug', 'Core', ":::::::USER MODULES::::::::");

//Start Transaction
$db->beginTransaction();
//Load modules
$this->loadModules();
//Submit the Transactions
$db->Commit();

//Load active commands
Logger::log('debug', 'Core', "Setting up commands");
$this->loadCommands();

//Load active subcommands
Logger::log('debug', 'Core', "Setting up subcommands");
$this->loadSubcommands();

//Load active events
Logger::log('debug', 'Core', "Setting up events");
$this->loadEvents();

//kill unused vars
unset($this->existing_commands);
unset($this->existing_events);
unset($this->existing_subcmds);
unset($this->existing_settings);
unset($this->existing_helps);

//Delete old entrys in the DB
$db->exec("DELETE FROM cmdcfg_<myname> WHERE `verify` = 0");
$db->exec("DELETE FROM hlpcfg_<myname> WHERE `verify` = 0");

bot::send("Reload of the Modules completed.", $sendto);
Logger::log('INFO', 'StartUp', "Finished loading USER modules...");
?>