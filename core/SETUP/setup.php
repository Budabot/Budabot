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
   

function read_input ($output = "") {
	echo $output;
	return trim(fgets(STDIN));
}

global $config_file;


function savecfg($vars, $settings) {
	global $config_file;
	$lines = file($config_file);
	foreach($lines as $key => $line) {
	  	if(preg_match("/^(.+)vars\[('|\")(.+)('|\")](.*)=(.*)\"(.*)\";(.*)$/i", $line, $arr))
			$lines[$key] = "$arr[1]vars['$arr[3]']$arr[5]=$arr[6]\"{$vars[$arr[3]]}\"; $arr[8]";
		elseif(preg_match("/^(.+)vars\[('|\")(.+)('|\")](.*)=([ 	]+)([0-9]+);(.*)$/i", $line, $arr))
			$lines[$key] = "$arr[1]vars['$arr[3]']$arr[5]=$arr[6]{$vars[$arr[3]]}; $arr[8]";
	  	elseif(preg_match("/^(.+)settings\[('|\")(.+)('|\")](.*)=(.*)\"(.*)\";(.*)$/i", $line, $arr))
			$lines[$key] = "$arr[1]settings['$arr[3]']$arr[5]=$arr[6]\"{$settings[$arr[3]]}\"; $arr[8]";
		elseif(preg_match("/^(.+)settings\[('|\")(.+)('|\")](.*)=([ 	]+)([0-9]+);(.*)$/i", $line, $arr))
			$lines[$key] = "$arr[1]settings['$arr[3]']$arr[5]=$arr[6]{$settings[$arr[3]]}; $arr[8]";
	}
	file_put_contents($config_file, $lines);
}

//Check current $config_file settings(if they are manually inserted)
if($vars["login"] != "" && $vars["password"] != "" && $vars["dimension"] != "" && $vars["name"] != "" && $settings["Super Admin"] != "" && $settings["DB Type"] != "") {
	do {
	    echo "             **********************************************\n";		
		echo "             All needed informations are already entered\n";
		echo "             from the $config_file.\n";
		echo "             Do you want to re-enter them or use the ones\n";
		echo "             from the $config_file file?\n";
		echo "             **********************************************\n";
		echo "             \n\n\n\n\n\n\n\n\n";

		$msg = "Do you want to re-enter it?(yes/no): \n";
		$answer = strtolower(read_input($msg));
	} while($answer == "" || ($answer != "yes" && $answer != "no"));
} else 
	$answer = "yes";

if($answer == "yes") {
	echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
    echo "             ***************************************************\n";	
	echo "             On the first using of this Bot you need to \n";
	echo "             fill out some Informations that are needed \n";
	echo "             to start this bot. You can everytime re-enter \n";
	echo "             this setup procedure with deleting the file \n";
	echo "             'delete me for new setup' in your bot folder\n";
	echo "             ***************************************************\n";
	echo "             \n\n\n\n\n\n\n\n\n";
	$msg = "Press enter to continue. \n";
	$answer = strtolower(read_input($msg));

	do {		
		echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
		echo "             ***************************************************\n";
		echo "             First you need to enter your Accountname on \n";
		echo "             that you want to run the bot. \n";
		echo "             Remember this name is case-sensitive!\n";
		echo "             ***************************************************\n";
		echo "             \n\n\n\n\n\n\n\n\n";
		$msg = "Enter your AO-Username(case-senstitive): \n";
		$vars["login"] = read_input($msg);
	} while($vars["login"] == "");
	
	do {
		echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
	    echo "             ************************************************\n";	
		echo "             Now you need to enter the Password for the  \n";
		echo "             Account you want to use\n";
		echo "             Remember this is also case-sensitive!\n";
		echo "             ************************************************\n";
		echo "             \n\n\n\n\n\n\n\n\n";
		$msg = "Enter your AO-Password(case-senstitive): \n";
		$vars["password"] = read_input($msg);
	} while($vars["password"] == "");	
	
	do {
		echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";		
	    echo "             **************************************************\n";
		echo "             On which Dimension do you want to run the bot?\n";
		echo "             Look below to see what you need to enter for\n";
		echo "             which dimension.\n";
		echo "             **************************************************\n";
		echo "             \n\n\n\n\n\n\n\n\n";
		$msg = "Choose a Dimension (1 = Atlantean, 2 = Rimor, 3 = Die Neue Welt, 4 = Test): \n";
		$vars["dimension"] = read_input($msg);
	} while($vars["dimension"] == "" || ($vars["dimension"] != 1 && $vars["dimension"] != 2 && $vars["dimension"] != 3 && $vars["dimension"] != 4));

	do {
		echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
	    echo "             *******************************************************\n";
		echo "             Under which name should the bot running?\n";
		echo "             Note that this char already need to exists, \n";
		echo "             if not create a new char with this name and\n";
		echo "             log him off in a not much populated area\n";
		echo "             (bots can read vicinity chats too).\n";
		echo "             *******************************************************\n";
		echo "             \n\n\n\n\n\n\n\n";
		$msg = "Enter the Character you want to use as bot: \n";
		$vars["name"] = read_input($msg);
	} while($vars["name"] == "");

	do {
		echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
	    echo "             ********************************************************\n";	
		echo "             Who should be the Administrator of this bot?\n";
		echo "             This is the char that has access to all commands\n";
		echo "             and settings of this bot.\n";
	    echo "             ********************************************************\n";	
		echo "             \n\n\n\n\n\n\n\n\n";
		$msg = "Enter the Administrator for this bot: \n";
		
		$settings["Super Admin"] = read_input($msg);
	} while($settings["Super Admin"] == "");
	
	echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
	echo "             ***************************************************\n";	
	echo "             When the bot is member in a organization\n";
	echo "             you need to enter it here. This is mostly\n";
	echo "             only needed when the bot is running as orgbot\n";
	echo "             Remember this setting is case-sensitive!\n";
	echo "             ***************************************************\n";
	echo "             \n\n\n\n\n\n\n\n\n";
	$msg = "Enter your Guild:  \n";
	$vars["my guild"] = read_input($msg);
	
	echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
	echo "             ******************************************************\n";	
	echo "             This bot allows you to put someone on his\n";
	echo "             Ignorelist but it is mainly only needed when \n";
	echo "             other bots are running in your org.\n";
	echo "             To add more then one to the ignore list\n";
	echo "             they neeed to be seperated by ;\n";
	echo "             For example bot1;bot2;bot3 and so on\n";
	echo "             Remember this setting is case-sensitive!\n";
	echo "             ******************************************************\n";
	echo "             \n\n\n\n\n\n\n";
	$msg = "Ignorelist:  \n";
	$settings["Ignore"] = read_input($msg);

	do {
		echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
	    echo "             *********************************************************\n";		
		echo "             Now we are coming to the 'heart' of this bot\n";
		echo "             the Database system where nearly everything is\n";
		echo "             stored. You have 2Options now. Either you can\n";
		echo "             set it up manually or the bot does it.\n";
		echo "             The last option is recommended for normal\n";
		echo "             Users. When you want to set it up manually\n";
		echo "             you can choose between 2 different database\n";
		echo "              systems: Mysql and Sqlite.\n";
		echo "             *********************************************************\n";
		echo "             \n\n\n\n\n\n\n";
		$msg = "Do you want to setup the database manually(yes/no(recommended)): \n";
		$mansetupdb = strtolower(read_input($msg));
	} while($mansetupdb == "" || ($mansetupdb != "no" && $mansetupdb != "yes"));
	
	if(strtolower($mansetupdb) == "yes") {
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
			echo "                 that it can be slower as SQLite!\n";
			echo "             *********************************************************\n";
			echo "             \n\n\n\n\n\n\n";
			$msg = "Choose a Databasesystem (1 = Sqlite(RECOMMENDED), 2 = MySQL): \n";
			$settings["DB Type"] = read_input($msg);
		} while($settings["DB Type"] == "" || (strtolower($settings["DB Type"]) != "1" && strtolower($settings["DB Type"]) != "2"));

		switch($settings["DB Type"]) {
		  	case "1":
			  	$settings["DB Type"] = "Sqlite";
			break;
			case "2":
			  	$settings["DB Type"] = "Mysql";
			break;
		}
		echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
		echo "             *********************************************************\n";	
		echo "             What is the name of the database that you\n";
		echo "             wannna use?\n";
		if($settings["DB Type"] == "Sqlite") {
			echo "             (This is the filename of the database)\n";
			echo "             (Default: budabot.db)\n";
		} else
			echo "             (Default: budabot)\n";
		echo "             *********************************************************\n";	
		echo "             \n\n\n\n\n\n\n\n";
		$msg = "Enter the Databasename(leave blank for default setting): \n";
		$settings["DB Name"] = read_input($msg);
		
		if($settings["DB Name"] == "" && $settings["DB Type"] == "Sqlite")
			$settings["DB Name"] = "budabot.db";
		elseif($settings["DB Name"] == "" && $settings["DB Type"] == "Mysql")
			$settings["DB Name"] = "budabot";
			
		if($settings["DB Type"] == "Mysql") {
			echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
			echo "             **********************************************\n";		
			echo "             On what Host is the Database running?\n";
			echo "             When it is running on this PC use:\n";
			echo "             localhost or 127.0.0.1\n";
			echo "             otherwise insert Hostname or IP\n";
			echo "             (Default: localhost)\n";
			echo "             **********************************************\n";
			echo "             \n\n\n\n\n\n\n\n\n";
			$msg = "Enter the Hostname for the Database(leave blank for default setting): \n";
			$settings["DB Host"] = read_input($msg);
	
			if($settings["DB Host"] == "")
				$settings["DB Host"] = "localhost";
			echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
			echo "             **********************************************\n";
			echo "             What is the username for the MySQL Database?\n";
			echo "             When you did not specify on the setup of the\n";
			echo "             Database it is by default root\n";
			echo "             (Default: root)\n";
			echo "             **********************************************\n";
			echo "             \n\n\n\n\n\n\n\n";
			$msg = "Enter username for the Database(leave blank for default setting): \n";
			$settings["DB username"] = read_input($msg);

			if($settings["DB username"] == "")
				$settings["DB username"] = "root";

			echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
			echo "             **********************************************\n";
			echo "             What is the password for the MySQL Database?\n";
			echo "             When you did not specify on the setup of the\n";
			echo "             Database it is by default none\n";
			echo "             (Default: none)\n";
			echo "             **********************************************\n";
			echo "             \n\n\n\n\n\n\n\n\n";
			$msg = "Enter password for the Database(leave blank for default setting): \n";
			$settings["DB password"] = read_input($msg);
		} else {
			echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n";
			echo "             **********************************************\n";			
			echo "             Where is the Sqlite Database stored?\n";
			echo "             Only enter a path when you dont want to have\n";
			echo "             the Database in the Data dir of your bot folder.\n";
			echo "             The Database will be created in that dir if does\n";
			echo "             not exists there.\n";
			echo "             (Default: data/)\n";
			echo "             **********************************************\n";
			echo "             \n\n\n\n\n\n\n";
			$msg = "Enter the path for the Database(leave blank for default setting):  \n";
			$settings["DB Host"] = read_input($msg);

			if($settings["DB Host"] == "")
				$settings["DB Host"] = "./data/";		  	
		}
	}
	
	do {
		echo "             \n\n\n\n\n\n\n\n\n\n\n\n\n\n";
		echo "             **********************************************\n";	
		echo "             Really last question now :)\n";
		echo "             Do you want to have all modules/commands enabled\n";
		echo "             by default?\n";
		echo "             This is usefull when you are using this bot the\n";
		echo "             first time so that all commands are available\n";
		echo "             from the beginning otherwise you need to enable \n";
		echo "             them manually.\n";
		echo "             **********************************************\n";
		echo "             \n\n\n\n\n\n\n";

		$msg = "Should all modules be enabled ? (yes/no): \n";
		$settings["default module status"] = strtolower(read_input($msg));
	} while($settings["default module status"] == "" || ($settings["default module status"] != "yes" && $settings["default module status"] != "no"));

	if($settings["default module status"] == "yes")
		$settings["default module status"] = 1;
	if($settings["default module status"] == "no")
		$settings["default module status"] = 0;
		
	echo "         \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
	echo "             **********************************************\n";		
	echo "             If you have entered everything correctly \n";
	echo "             the bot should start.\n";
	echo "             You can everytime re-enter\n";
	echo "             this setup procedure with deleting the file\n";
	echo "             'delete me for new setup' in your bot folder.\n";
	echo "             ----------------------------------------------\n";
	echo "             We would appreciate every feedback if it is\n";
	echo "             positive or not. Everything will help us to\n";
	echo "             make this bot better and better :)\n";
	echo "             ----------------------------------------------\n";
	echo "             Have a good day on Rubi-Ka.\n";
	echo "             **********************************************\n";
	echo "         \n\n\n\n\n";
	$msg = "Press any key to continue.\n";
	read_input($msg);

	//Save the entered infos to $config_file
	savecfg($vars, $settings);
}
//Create file
$fp = fopen("delete me for new setup", "w");
fclose($fp);
?>
