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
   
require_once('functions.php');

if(preg_match("/^premadeupdate$/i", $message)) {
	
	$msg = '';
	$currentVersion = bot::getsetting('premade_implant_db_version');
	$newVersion = checkForUpdate($currentVersion, true);
	
	if ($newVersion > $currentVersion) {
		//$chatBot->savesetting('premade_implant_db_version', $version);
		$msg = "Premade Implant Database has been updated. New version: $newVersion.";
	} else {
		$msg = "Premade Implant Database is already up to date. Version: $newVersion.";
	}

    bot::send($msg, $sendto);
}
?>
