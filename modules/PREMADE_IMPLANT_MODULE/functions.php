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
   
require_once('Implant.class.php');

function searchByProfession($profession) {
	
	global $db;
	$db->query("SELECT * FROM premade_implant WHERE profession = '$profession' ORDER BY slot");
	return $db->fObject("all");
}

function searchBySlot($slot) {

	global $db;
	$sql = "SELECT * FROM premade_implant WHERE slot = '$slot' ORDER BY shiny, bright, faded";
	echo $sql;
	$db->query($sql);
	return $db->fObject("all");
}

function searchByModifier($modifier) {
	
	global $db;
	$db->query("SELECT * FROM premade_implant WHERE shiny LIKE '%$modifier%' OR bright LIKE '%$modifier%' OR faded LIKE '%$modifier%'");
	return $db->fObject("all");
}

function formatResults($implants) {
	
	$msg = "\n";
	
	$count = 0;
	foreach ($implants as $implant) {
		$msg .= getFormattedLine($implant);
		$count++;
	}
		
	if ($count > 3) {
		
		$msg = bot::makeLink('Results', $msg, 'text');
	}
	
	return $msg;
}

function getFormattedLine($implant) {
	
	return "$implant->slot $implant->profession $implant->ability $implant->shiny $implant->bright $implant->faded\n";
}

function checkForUpdate($currentVersion, $forceUpdate) {
	
	global $db;
	$msg = '';
	$versionCheckUrl = 'http://flw.nu/tools/premadeimps.php?q=version';
	$downloadUrl = 'http://flw.nu/tools/premadeimps.php?q=dumpdb';
	
	$version = file_get_contents($versionCheckUrl);
	
	if ($version > $currentVersion || $forceUpdate) {
		
		$db->exec("DELETE FROM premade_implant");
		
		$csv = file_get_contents($downloadUrl);
		$rows = explode("\r\n", $csv);
		$count = 0;
		foreach ($rows as $row) {
			
			$count++;
			
			// skip the header row
			if ($count == 1) {
				continue;
			}
			
			if ($row == '') {
				continue;	
			}
			
			$array = explode(';', $row);

			$slot = $array[0];
			$profession = strtolower($array[1]);
			$ability = $array[2];
			$shiny = $array[3];
			$bright = $array[4];
			$faded = $array[5];

			$sql = "INSERT INTO premade_implant (slot, profession, ability, shiny, bright, faded) VALUES('$slot', '$profession', '$ability', '$shiny', '$bright', '$faded')";
			$db->exec($sql);
		}

	}
	
	return $version;
	
}

?>