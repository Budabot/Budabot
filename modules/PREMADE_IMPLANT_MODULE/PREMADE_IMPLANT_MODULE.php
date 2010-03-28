<?php
   /*
   ** Module: PREMADE_IMPLANT
   ** Author: Tyrence/Whiz (RK2)
   ** Description: Allows you search for the implants in the premade implant booths.
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): Fall 2008
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

	$MODULE_NAME = "PREMADE_IMPLANT_MODULE";

	//adds tower info to 'watch' list
	bot::command("guild", "$MODULE_NAME/premade.php", "premade", "all", "Searches for implants out of the premade implants booths");
	bot::command("priv", "$MODULE_NAME/premade.php", "premade", "all", "Searches for implants out of the premade implants booths");
	bot::command("msg", "$MODULE_NAME/premade.php", "premade", "all", "Searches for implants out of the premade implants booths");
	
	// removes tower info from 'watch' list
	bot::command("guild", "$MODULE_NAME/premade_update.php", "premadeupdate", "all", "Checks the premade imp db for updates");
	bot::command("priv", "$MODULE_NAME/premade_update.php", "premadeupdate", "all", "Checks the premade imp db for updates");
	bot::command("msg", "$MODULE_NAME/premade_update.php", "premadeupdate", "all", "Checks the premade imp db for updates");
	
	//Helpfiles
	bot::help("Premade Implants", "$MODULE_NAME/premade_implant.txt", "guild", "Premade Implant Help", "Premade Implant");

	//Setup
	bot::event("setup", "$MODULE_NAME/setup.php");
	
?>