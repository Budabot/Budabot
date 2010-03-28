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

function getAllSitesInfo() {

	$array = array();
	global $db;
	$db->query("SELECT * FROM tower_watch ORDER BY org, ct_ql");
	$i = 0;

	$secondsPastMidnight = time() % 86400;
	
	while($site = $db->fObject()) {

		$time = $secondsPastMidnight;
		if ($site->close_time > $secondsPastMidnight) {
			$time += 86400;
		}

		$timePastCloseTime = $time - $site->close_time;
		
		if($timePastCloseTime < 3600 * 18) {
			$site->gas_change = 3600 * 18 - $timePastCloseTime;
			$site->gas_level = '75%';
		} else if ($timePastCloseTime < 3600 * 23) {
			$site->gas_change = 3600 * 23 - $timePastCloseTime;
			$site->gas_level = '25%';
		} else if ($timePastCloseTime < 3600 * 24) {
			$site->gas_change = 3600 * 24 - $timePastCloseTime;
			$site->gas_level = '5%';
		}
		
		if ($msg != '') {
			$msg .= ' ::: ';	
		}
		
		$array[$i++] = $site;
	}
	
	return $array;
}

function getTimeObj($input) {

	$minutes = floor($input / 60);
	$seconds = floor($input - ($minutes * 60));
	$hours = floor($minutes / 60);
	$minutes = floor($minutes - ($hours * 60));
	$days = floor($hours / 24);
	$hours = floor($hours - ($days * 24));
	
	if (strlen($seconds) == 1) {
		$seconds = '0' . $seconds;	
	}
	
	if (strlen($minutes) == 1) {
		$minutes = '0' . $minutes;	
	}
	
	//$obj = new stdClass();
	$obj->seconds = $seconds;
	$obj->minutes = $minutes;
	$obj->hours = $hours;
	$obj->days = $days;
	
	return $obj;
}

function getTowerInfoMsg() {

	$displayMsg = '';
	$moreInfoMsg = "<tab>::: Tower Watch List -- More Info :::\n";
	$allSitesInfo = getAllSitesInfo();
	$org = '';
	forEach($allSitesInfo as $site)	{
		$gas_level = '';
		
		if ($site->org != $org) {
			$org = $site->org;
			$moreInfoMsg .= "\n<tab>$org towers\n";
		}

		if($site->gas_level == '75%') {
			$gas_level = "<orange>75%<end>";
		}
		else if ($site->gas_level == '25%') {
			$gas_level = "<yellow>25%<end>";
		}
		else if ($site->gas_level == '5%') {
			$gas_level = "<red>5%<end>";
		}
		
		$gas_change = getTimeObj($site->gas_change);
		
		$displayMsg .= "$site->zone $site->base_number $gas_level $gas_change->hours:$gas_change->minutes ($site->org) ::: ";
		$towerType = getTowerType($site->ct_ql);
		$moreInfoMsg .= "$site->zone $site->base_number $site->gas_level  $site->ct_ql ct (TYPE $towerType) $gas_change->hours:$gas_change->minutes:$gas_change->seconds ($site->org)\n";
	}
	
	if (count($allSitesInfo) == 0) {
	
		$displayMsg = "No sites in watch list.";			
	
	} else {
		// this is required
		global $chatBot;
		$link = $chatBot->makeLink('More info', $moreInfoMsg);
		$displayMsg .= "$link";
	}
	
	return $displayMsg;
}

function getTowerType($ql) {

	$towerType = '';
	
	if ($ql >= 276) {
		$towerType = "VIII";
	} else if ($ql >= 226) {
		$towerType = "VII";
	} else if ($ql >= 201) {
		$towerType = "VI";
	} else if ($ql >= 177) {
		$towerType = "V";
	} else if ($ql >= 129) {
		$towerType = "IV";
	} else if ($ql >= 82) {
		$towerType = "III";
	} else if ($ql >= 34) {
		$towerType = "II";
	} else {
		$towerType = "I";
	}
	
	return $towerType;	
}
?>
