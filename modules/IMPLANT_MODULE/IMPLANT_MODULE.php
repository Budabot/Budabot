<?php
   /*
   ** Module: IMPLANT
   ** Author: Tyrence/Whiz (RK2)
   ** Description: Allows you lookup information on a specific ql of implant.
   ** Version: 2.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 13-October-2007
   ** Date(last modified): 9-Mar-2010
   **
   ** Copyright (C) 2009 Jason Wheeler (bigwheels16@hotmail.com)
   **
   ** Licence Infos:
   ** This file is an addon to Budabot.
   **
   ** This module is free software; you can redistribute it and/or modify
   ** it under the terms of the GNU General Public License as published by
   ** the Free Software Foundation; either version 2 of the License, or
   ** (at your option) any later version.
   **
   ** This module is distributed in the hope that it will be useful,
   ** but WITHOUT ANY WARRANTY; without even the implied warranty of
   ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   ** GNU General Public License for more details.
   **
   ** You should have received a copy of the GNU General Public License
   ** along with this module; if not, write to the Free Software
   ** Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   **
   ** This module may be obtained at: http://www.box.net/shared/bgl3cx1c3z
   **
   */
   
	require_once 'implant_functions.php';
	require_once 'Implant.class.php';

	$MODULE_NAME = "IMPLANT_MODULE";

	//Setup
	DB::loadSQLFile($MODULE_NAME, "implant");
	DB::loadSQLFile($MODULE_NAME, "premade_implant");

	//Private
	Command::register($MODULE_NAME, "", "impql.php", "impql", "all", "Shows stats for implant at given ql");
	Command::register($MODULE_NAME, "", "impreq.php", "impreq", "all", "Shows the highest ql implant that can be worn given treatment and ability");
	Command::register($MODULE_NAME, "", "premade.php", "premade", "all", "Searches for implants out of the premade implants booths");

	//Help
	Help::register($MODULE_NAME, "implant", "implant.txt", "all", "Implant help");
	Help::register($MODULE_NAME, "premade", "premade_implant.txt", "guild", "Premade Implant Help");
?>