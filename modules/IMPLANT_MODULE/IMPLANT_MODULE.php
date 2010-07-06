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

	$MODULE_NAME = "IMPLANT_MODULE";
	
	//Setup
	bot::loadSQLFile($MODULE_NAME, "implant");

	//Private
	bot::command("", "$MODULE_NAME/impql.php", "impql", ALL, "Shows stats for implant at given ql");
	bot::command("", "$MODULE_NAME/impreq.php", "impreq", ALL, "Shows the highest ql implant that can be worn given treatment and ability");

	//Help
	bot::help("Implant", "$MODULE_NAME/implant.txt", ALL, "Implant help");
?>