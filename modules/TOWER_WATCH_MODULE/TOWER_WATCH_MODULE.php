<?php
   /*
   ** Module: TOWER_WATCH
   ** Author: Tyrence/Whiz (RK2)
   ** Description: Allows you to keep track of the opentimes of tower sites.
   ** Version: 1.2
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23-November-2007
   ** Date(last modified): 9-Mar-2010
   ** 
   ** Copyright (C) 2008 Jason Wheeler (bigwheels16@hotmail.com)
   **
   ** This module and all it's files and contents are licensed
   ** under the GNU General Public License.  You may distribute
   ** and modify this module and it's contents freely.
   **
   ** This module may be obtained at: http://www.box.net/shared/bgl3cx1c3z
   **
   */

	$MODULE_NAME = "TOWER_WATCH_MODULE";

	//adds tower info to 'watch' list
	bot::command("guild", "$MODULE_NAME/scout.php", "scout", "all", "adds tower info to watch list");
	bot::command("priv", "$MODULE_NAME/scout.php", "scout", "all", "adds tower info to watch list");
	bot::command("msg", "$MODULE_NAME/scout.php", "scout", "all", "adds tower info to watch list");
	
	// removes tower info from 'watch' list
	bot::command("guild", "$MODULE_NAME/remscout.php", "remscout", "all", "removes tower info from watch list");
	bot::command("priv", "$MODULE_NAME/remscout.php", "remscout", "all", "removes tower info from watch list");
	bot::command("msg", "$MODULE_NAME/remscout.php", "remscout", "all", "removes tower info from watch list");
	
	//shows the open times for each tower site on the 'watch' list
	bot::command("guild", "$MODULE_NAME/opentimes.php", "opentimes", "all", "shows status of towers");
	bot::command("priv", "$MODULE_NAME/opentimes.php", "opentimes", "all", "shows status of towers");
	bot::command("msg", "$MODULE_NAME/opentimes.php", "opentimes", "all", "shows status of towers");
	
	//Helpfiles
	bot::help("Tower Watch", "$MODULE_NAME/tower_watch.txt", "guild", "Tower Watch Help", "Tower Watch");
	
	//Settings for this module	
	bot::addsetting("alarmpreview", "Sets how early alarm should sound for gas change in minutes.", "edit", 5, "number");
	bot::addsetting("displaylogon", "Summary of tower sites should be sent to player at logon.", "edit", "1", "true;false", "1;0");
	bot::addsetting("displayalarm", "Alarm should be displayed in org chat", "edit", "1", "true;false", "1;0");
	
	bot::event("2sec", "$MODULE_NAME/check_gas_change.php", "scout", "Checks for gas changes for tower sites on watch list");
	bot::event("logOn", "$MODULE_NAME/logon.php", "scout", "Displays summary of tower sites and gas levels.");
	
	//Setup
	bot::loadSQLFile($MODULE_NAME, "tower_watch");
	
?>