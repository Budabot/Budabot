<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Configuration of the Basicbot settings
   ** Version: 0.3
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 15.01.2006
   ** Date(last modified): 22.07.2006
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

global $config_file;

function read_input ($output = "") {
	echo $output;
	return trim(fgets(STDIN));
}

function savecfg($vars, $settings) {
	global $config_file;
	$lines = file($config_file);
	forEach ($lines as $key => $line) {
	  	if (preg_match("/^(.+)vars\[('|\")(.+)('|\")](.*)=(.*)\"(.*)\";(.*)$/si", $line, $arr)) {
			$lines[$key] = "$arr[1]vars['$arr[3]']$arr[5]=$arr[6]\"{$vars[$arr[3]]}\";$arr[8]";
		} else if (preg_match("/^(.+)vars\[('|\")(.+)('|\")](.*)=([ 	]+)([0-9]+);(.*)$/si", $line, $arr)) {
			$lines[$key] = "$arr[1]vars['$arr[3]']$arr[5]=$arr[6]{$vars[$arr[3]]};$arr[8]";
	  	} else if (preg_match("/^(.+)settings\[('|\")(.+)('|\")](.*)=(.*)\"(.*)\";(.*)$/si", $line, $arr)) {
			$lines[$key] = "$arr[1]settings['$arr[3]']$arr[5]=$arr[6]\"{$settings[$arr[3]]}\";$arr[8]";
		} else if (preg_match("/^(.+)settings\[('|\")(.+)('|\")](.*)=([ 	]+)([0-9]+);(.*)$/si", $line, $arr)) {
			$lines[$key] = "$arr[1]settings['$arr[3]']$arr[5]=$arr[6]{$settings[$arr[3]]};$arr[8]";
		}
	}
	file_put_contents($config_file, $lines);
}

echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
echo "             ***************************************************\n";	
echo "             You will need to provide some information\n";
echo "             regarding the basic configuration of the bot.\n";
echo "             ***************************************************\n";
echo "             \n\n\n\n\n\n\n\n\n";
$msg = "Press enter to continue. \n";
$answer = strtolower(read_input($msg));

do {		
	echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
	echo "             ***************************************************\n";
	echo "             Enter the Account name that contains the\n";
	echo "             character the bot will run on. \n";
	echo "             Remember this name is case-sensitive!\n";
	echo "             ***************************************************\n";
	echo "             \n\n\n\n\n\n\n\n\n";
	$msg = "Enter the bot AO-Username(case-senstitive): \n";
	$vars["login"] = read_input($msg);
} while ($vars["login"] == "");

do {
	echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
	echo "             ************************************************\n";	
	echo "             Enter the Password for the for the Account.\n";
	echo "             Remember this is also case-sensitive!\n";
	echo "             ************************************************\n";
	echo "             \n\n\n\n\n\n\n\n\n";
	$msg = "Enter the bot AO-Password(case-senstitive): \n";
	$vars["password"] = read_input($msg);
} while ($vars["password"] == "");	

do {
	echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";		
	echo "             **************************************************\n";
	echo "             Enter the dimension that the bot will run on.\n";
	echo "             **************************************************\n";
	echo "             \n\n\n\n\n\n\n\n\n";
	$msg = "Choose a Dimension (1 = Atlantean, 2 = Rimor, 3 = Die Neue Welt, 4 = Test): \n";
	$vars["dimension"] = read_input($msg);
} while ($vars["dimension"] != 1 && $vars["dimension"] != 2 && $vars["dimension"] != 3 && $vars["dimension"] != 4);

do {
	echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
	echo "             *******************************************************\n";
	echo "             Enter the character the bot will run on.\n";
	echo "             If the character does not already exist, close this\n";
	echo "             and create the character and then start the bot again.\n";
	echo "             Make sure the bot toon is not currently logged on\n";
	echo "             by someone else or the bot will not be able to log on.\n";
	echo "             *******************************************************\n";
	echo "             \n\n\n\n\n\n\n\n";
	$msg = "Enter the Character you want to use as bot: \n";
	$vars["name"] = read_input($msg);
} while ($vars["name"] == "");

echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
echo "             ***************************************************\n";	
echo "             To run the bot as a raid bot, leave this setting blank.\n";
echo "             To run the bot as an org bot, enter the organization name.\n";
echo "             The organization name must match exactly including case\n";
echo "             and punctuation!\n";
echo "             ***************************************************\n";
echo "             \n\n\n\n\n\n\n\n\n";
$msg = "Enter your Guild:  \n";
$vars["my_guild"] = read_input($msg);

do {
	echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
	echo "             ********************************************************\n";	
	echo "             Who should be the Administrator for this bot?\n";
	echo "             This is the character that has access to all commands\n";
	echo "             and settings for this bot.\n";
	echo "             ********************************************************\n";	
	echo "             \n\n\n\n\n\n\n\n\n";
	$msg = "Enter the Administrator for this bot: \n";
	
	$vars["SuperAdmin"] = read_input($msg);
} while ($vars["SuperAdmin"] == "");

do {
	echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
	echo "             *********************************************************\n";		
	echo "             Now we are coming to the 'heart' of this bot\n";
	echo "             the Database system where nearly everything is\n";
	echo "             stored. You have 2 options now. Either you can\n";
	echo "             set it up manually or leave the default setting.\n";
	echo "             The default settomg is recommended for normal\n";
	echo "             Users. If you choose to set it up manually\n";
	echo "             you will be able to choose between\n";
	echo "             Mysql and Sqlite.\n";
	echo "             *********************************************************\n";
	echo "             \n\n\n\n\n\n\n";
	$msg = "Do you want to setup the database manually(yes/no - Recommended): \n";
	$mansetupdb = strtolower(read_input($msg));
} while ($mansetupdb != "no" && $mansetupdb != "yes");

if (strtolower($mansetupdb) == "yes") {
	do {
		echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
		echo "             *********************************************************\n";		
		echo "             The bot is able to use 2 different Database Types.\n";
		echo "             1 = Sqlite. It is the easiest way togo.\n";
		echo "                 Nearly as fast as MySQL\n";
		echo "             2 = MySQL. An Open-Source Database.\n";
		echo "                 You need to install and setup it manually\n";
		echo "                 http://www.mysql.com/\n";
		echo "                 Be aware that when you setup it wrong\n";
		echo "                 that it can be slower then SQLite!\n";
		echo "             *********************************************************\n";
		echo "             \n\n\n\n\n\n\n";
		$msg = "Choose a Databasesystem (1 = Sqlite - Recommended, 2 = MySQL): \n";
		$vars["DB Type"] = read_input($msg);
	} while (strtolower($vars["DB Type"]) != "1" && strtolower($vars["DB Type"]) != "2");

	switch($vars["DB Type"]) {
		case "1":
			$vars["DB Type"] = "Sqlite";
		break;
		case "2":
			$vars["DB Type"] = "Mysql";
		break;
	}
	echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
	echo "             *********************************************************\n";	
	echo "             What is the name of the database that you\n";
	echo "             wannna use?\n";
	if ($vars["DB Type"] == "Sqlite") {
		echo "             (This is the filename of the database)\n";
		echo "             (Default: budabot.db)\n";
	} else {
		echo "             (Default: budabot)\n";
	}
	echo "             *********************************************************\n";	
	echo "             \n\n\n\n\n\n\n\n";
	$msg = "Enter the Databasename(leave blank for default setting): \n";
	$vars["DB Name"] = read_input($msg);
	
	if ($vars["DB Name"] == "" && $vars["DB Type"] == "Sqlite") {
		$vars["DB Name"] = "budabot.db";
	} else if ($vars["DB Name"] == "" && $vars["DB Type"] == "Mysql") {
		$vars["DB Name"] = "budabot";
	}
		
	if ($vars["DB Type"] == "Mysql") {
		echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
		echo "             **********************************************\n";		
		echo "             On what Host is the Database running?\n";
		echo "             If it is running on this PC use:\n";
		echo "             localhost or 127.0.0.1\n";
		echo "             otherwise insert Hostname or IP\n";
		echo "             (Default: localhost)\n";
		echo "             **********************************************\n";
		echo "             \n\n\n\n\n\n\n\n\n";
		$msg = "Enter the Hostname for the Database(leave blank for default setting): \n";
		$vars["DB Host"] = read_input($msg);

		if ($vars["DB Host"] == "") {
			$vars["DB Host"] = "localhost";
		}
		echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
		echo "             **********************************************\n";
		echo "             What is the username for the MySQL Database?\n";
		echo "             If you did not specify a username when you installed\n";
		echo "             the Database then it will be 'root'\n";
		echo "             (Default: root)\n";
		echo "             **********************************************\n";
		echo "             \n\n\n\n\n\n\n\n";
		$msg = "Enter username for the Database(leave blank for default setting): \n";
		$vars["DB username"] = read_input($msg);

		if ($vars["DB username"] == "") {
			$vars["DB username"] = "root";
		}

		echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
		echo "             **********************************************\n";
		echo "             What is the password for the MySQL Database?\n";
		echo "             if you did not specify a username when you installed\n";
		echo "             the Database then it will be blank (none)\n";
		echo "             (Default: none)\n";
		echo "             **********************************************\n";
		echo "             \n\n\n\n\n\n\n\n\n";
		$msg = "Enter password for the Database(leave blank for default setting): \n";
		$vars["DB password"] = read_input($msg);
	} else {
		echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n";
		echo "             **********************************************\n";			
		echo "             Where is the Sqlite Database stored?\n";
		echo "             You may leave this setting blank to use the default\n";
		echo "             location which is the Data dir of your bot folder.\n";
		echo "             The Database will be created if it does\n";
		echo "             not already exists.\n";
		echo "             (Default: data/)\n";
		echo "             **********************************************\n";
		echo "             \n\n\n\n\n\n\n";
		$msg = "Enter the path for the Database(leave blank for default setting):  \n";
		$vars["DB Host"] = read_input($msg);

		if ($vars["DB Host"] == "") {
			$vars["DB Host"] = "./data/";
		}
	}
}

do {
	echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n";
	echo "             **********************************************\n";	
	echo "             Do you want to have all modules/commands enabled\n";
	echo "             by default?\n";
	echo "             This is usefull when you are using this bot the\n";
	echo "             first time so that all commands are available\n";
	echo "             from the beginning.  If you say 'no' to this question\n";
	echo "             you will need to enable the commands manually.\n";
	echo "             (Recommended: yes/)\n";
	echo "             **********************************************\n";
	echo "             \n\n\n\n\n\n\n";

	$msg = "Should all modules be enabled ? (yes - Recommended/no): \n";
	$settings["default_module_status"] = strtolower(read_input($msg));
} while ($settings["default_module_status"] != "yes" && $settings["default_module_status"] != "no");

if ($settings["default_module_status"] == "yes") {
	$settings["default_module_status"] = 1;
}
if ($settings["default_module_status"] == "no") {
	$settings["default_module_status"] = 0;
}
	
echo "         \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
echo "             **********************************************\n";		
echo "             If you have entered everything correctly \n";
echo "             the bot should start.\n";
echo "             ----------------------------------------------\n";
echo "             We would appreciate any feedback you have.\n";
echo "             Comments and suggestions are more than welcome!\n";
echo "             www.budabot.com\n";
echo "             ----------------------------------------------\n";
echo "             Have a good day on Rubi-Ka.\n";
echo "             To rerun this setup simply delete your\n";
echo "             config file: $config_file\n";
echo "             **********************************************\n";
echo "         \n\n\n\n\n";
$msg = "Press any key to start the bot.\n";
read_input($msg);

//Save the entered info to $config_file
savecfg($vars, $settings);

die("Restarting bot");
?>
