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
print("!!!Reloading all Modules!!!\n");

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
$db->query("UPDATE cmdcfg_<myname> SET `verify` = 0");
$db->query("UPDATE cmdcfg_<myname> SET `status` = 0 WHERE `cmdevent` = 'event' AND `type` = 'setup'");
$db->query("UPDATE cmdcfg_<myname> SET `grp` = 'none'");
$db->query("DELETE FROM cmdcfg_<myname> WHERE `module` = 'none'");

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

//Load the Core Modules
if($this->settings['debug'] > 0) print("\n:::::::CORE MODULES::::::::\n");
if($this->settings['debug'] > 0) print("MODULE_NAME:(SETTINGS.php)\n");
	include "./core/SETTINGS/SETTINGS.php";
if($this->settings['debug'] > 0) print("MODULE_NAME:(SYSTEM.php)\n");
	include "./core/SYSTEM/SYSTEM.php";
	$curMod = "";

if($this->settings['debug'] > 0) print("MODULE_NAME:(ADMIN.php)\n");
	include "./core/ADMIN/ADMIN.php";		
if($this->settings['debug'] > 0) print("MODULE_NAME:(BAN.php)\n");
	include "./core/BAN/BAN.php";	
if($this->settings['debug'] > 0) print("MODULE_NAME:(HELP.php)\n");
	include "./core/HELP/HELP.php";	
if($this->settings['debug'] > 0) print("MODULE_NAME:(CONFIG.php)\n");
	include "./core/CONFIG/CONFIG.php";	
if($this->settings['debug'] > 0) print("MODULE_NAME:(BASIC_CONNECTED_EVENTS.php)\n");
	include "./core/BASIC_CONNECTED_EVENTS/BASIC_CONNECTED_EVENTS.php";
if($this->settings['debug'] > 0) print("MODULE_NAME:(PRIV_TELL_LIMIT.php)\n");
	include "./core/PRIV_TELL_LIMIT/PRIV_TELL_LIMIT.php";

// Load Plugin Modules
if($this->settings['debug'] > 0) print("\n:::::::PLUGIN MODULES::::::::\n");	

//Start Transaction
$db->beginTransaction();
//Load modules
$this->loadModules();
//Submit the Transactions
$db->Commit();

//Load active commands
if($this->settings['debug'] > 0) print("\nSetting up commands.\n");
$this->loadCommands();

//Load active subcommands
if($this->settings['debug'] > 0) print("\nSetting up subcommands.\n");
$this->loadSubcommands();

//Load active events
if($this->settings['debug'] > 0) print("\nSetting up events.\n");
$this->loadEvents();

//kill unused vars
unset($this->existing_commands);
unset($this->existing_events);
unset($this->existing_subcmds);
unset($this->existing_settings);
unset($this->existing_helps);

//Delete old entrys in the DB
$db->query("DELETE FROM cmdcfg_<myname> WHERE `verify` = 0");
$db->query("DELETE FROM hlpcfg_<myname> WHERE `verify` = 0");

bot::send("Reload of the Modules completed.", $sendto);
print("!!!Reload of all Modules is done!!!\n");
?>