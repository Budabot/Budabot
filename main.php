<?php
   /*
   ** Author: Sebuda/Derroylo (both RK2) + Linux compatibility Changes from Dak (RK2)
   ** Description: Creates the setup Procedure, Loads core classes and creates the bot mainloop.
   ** Version: 0.6
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 01.10.2005	
   ** Date(last modified): 12.01.2007
   ** 
   ** Copyright (C) 2005, 2006 Carsten Lohmann and J. Gracik
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

$version = "2.0";

echo "\n\n\n\n\n
	**************************************************
	****         Budabot Version: $version             ****
	****    written by Sebuda & Derroylo(RK2)     ****
	****                Project Site:             ****
	****    http://code.google.com/p/budabot2/    ****
	****               Support Forum:             ****
	****          http://www.budabot.com/         ****
	**************************************************
\n";

date_default_timezone_set("UTC");

if (isWindows()) {
    // Load Extention 
    dl("php_sockets.dll");
    dl("php_pdo_sqlite.dll");
    dl("php_pdo_mysql.dll");
} else {
    /*
    * Load Extentions, if not already loaded.
    *
    * Note: These are normally present in a
    * modern Linux system. This is a safeguard.
    */
    if (!extension_loaded('pdo_sqlite')) {
        @dl('pdo_sqlite.so');
    }
    if (!extension_loaded('pdo_mysql')) {
        @dl('pdo_mysql.so');
    }
    
}

//Load Required Files
$config_file = $argv[1];
if (!file_exists($config_file)) {
	copy('config.template.php', $config_file) or Logger::log('ERROR', 'StartUp', "could not create config file: {$config_file}");
}

require $config_file;
require_once "./core/Logger.class.php";

//Set Error Level
//error_reporting(-1);
error_reporting(E_ERROR | E_PARSE);
ini_set("log_errors", 1);
ini_set("error_log", "./logs/{$vars['name']}.{$vars['dimension']}/php_errors.log");


require_once "./core/AOChat.class.php";
require_once "./core/Budabot.class.php";
require_once "./core/DB.class.php";
require_once "./core/xml.php";
require_once './core/MyCurl.class.php';
require_once './core/Playfields.class.php';
require_once './core/AccessLevel.class.php';
require_once './core/Command.class.php';
require_once './core/Subcommand.class.php';
require_once './core/CommandAlias.class.php';
require_once './core/Event.class.php';
require_once './core/Setting.class.php';
require_once './core/Help.class.php';
require_once './core/Buddylist.class.php';
require_once './core/Util.class.php';
require_once './core/Text.class.php';

//Show setup dialog
if ($vars['login']		== "" ||
	$vars['password']	== "" ||
	$vars['name']		== "") {

	include "./core/SETUP/setup.php";
}

Logger::log('INFO', 'StartUp', "Starting {$vars['name']}...");

//Bring the ignore list to a bot readable format
$ignore = explode(";", $settings["Ignore"]);
unset($settings["Ignore"]);
forEach ($ignore as $bot) {
	$bot = ucfirst(strtolower($bot));
	$settings["Ignore"][$bot] = true;
}
unset($ignore);

//////////////////////////////////////////////////////////////
// Create new objects
$db = new DB($settings["DB Type"], $settings["DB Name"], $settings["DB Host"], $settings["DB username"], $settings["DB password"]);
if ($db->errorCode != 0) {
	Logger::log('ERROR', 'StartUp', "Error in creating Database Object: {$db->errorInfo}");
	sleep(5);
	die();
}

$chatBot = new Budabot($vars, $settings);
$chatBot->init();
$chatBot->connectAO($vars['login'], $vars['password']);

//Clear the login and the password	
unset($vars['login']);
unset($vars['password']);

//Clear database settings
unset($settings["DB Type"]);
unset($settings["DB Name"]);
unset($settings["DB Host"]);
unset($settings["DB username"]);
unset($settings["DB password"]);

// Call Main Loop
main($chatBot);
/*
** Name: main
** Main Loop
** Inputs: (bool)$forever
** Outputs: None
*/	
function main(&$chatBot) {
	$start = time();
	
	$exec_connected_events = false;
	while (true) {
		$chatBot->wait_for_packet();
		$chatBot->crons();
		if ($exec_connected_events == false && ((time() - $start) > 5))	{
			$chatBot->connectedEvents();
			$exec_connected_events = true;
		}
	}	
}	

/**
* isWindows is a little utility function to check
* whether the bot is running Windows or something
* else: returns true if under Windows, else false
*/
function isWindows() {
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		return true;
	} else {
		return false;
	}
}

?>
