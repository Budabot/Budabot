<?
   /*
   ** Author: Tyrence (RK2)
   ** Description: Add tower sites to watch list
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.11.2007
   ** Date(last modified): 23.11.2007
   ** 
   ** Copyright (C) 2007 Jason Wheeler
   **
   ** Licence Infos: 
   ** This file is module for Budabot.
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
   */

function getAllSitesInfo()
{
	$array = array();
	global $db;
	$db->query("SELECT * FROM tower_watch_<myname>");
	$i = 0;

	$secondsPastMidnight = time() % 86400;
	
	while($site = $db->fObject())
	{
		$time = $secondsPastMidnight;
		if ($site->close_time > $secondsPastMidnight)
		{
			$time += 86400;
		}

		$timePastCloseTime = $time - $site->close_time;
		
		if($timePastCloseTime < 3600 * 18)
		{
			$site->gas_change = 3600 * 18 - $timePastCloseTime;
			$site->gas_level = '75%';
		}
		else if ($timePastCloseTime < 3600 * 23)
		{
			$site->gas_change = 3600 * 23 - $timePastCloseTime;
			$site->gas_level = '25%';
		}
		else if ($timePastCloseTime < 3600 * 24)
		{
			$site->gas_change = 3600 * 24 - $timePastCloseTime;
			$site->gas_level = '5%';
		}
		
		if ($msg != '')
		{
			$msg .= ' ::: ';	
		}
		
		$array[$i++] = $site;
	}
	
	return $array;
}

function getTimeObj($input)
{
	$minutes = floor($input / 60);
	$seconds = floor($input - ($minutes * 60));
	$hours = floor($minutes / 60);
	$minutes = floor($minutes - ($hours * 60));
	$days = floor($hours / 24);
	$hours = floor($hours - ($days * 24));
	
	if (strlen($seconds) == 1)
	{
		$seconds = '0' . $seconds;	
	}
	
	if (strlen($minutes) == 1)
	{
		$minutes = '0' . $minutes;	
	}
	
	//$obj = new stdClass();
	$obj->seconds = $seconds;
	$obj->minutes = $minutes;
	$obj->hours = $hours;
	$obj->days = $days;
	
	return $obj;
}

function getTowerInfoMsg()
{
	$displayMsg = '';
	$moreInfoMsg = '';
	$allSitesInfo = getAllSitesInfo();
	forEach($allSitesInfo as $site)
	{
		$gas_level = '';

		if($site->gas_level == '75%')
		{
			$gas_level = "<orange>75%<end>";
		}
		else if ($site->gas_level == '25%')
		{
			$gas_level = "<yellow>25%<end>";
		}
		else if ($site->gas_level == '5%')
		{
			$gas_level = "<red>5%<end>";
		}
		
		if ($displayMsg != '')
		{
			$displayMsg .= ' ::: ';	
		}
		
		$gas_change = getTimeObj($site->gas_change);
		
		$displayMsg .= "$site->zone $site->base_number $gas_level $gas_change->hours:$gas_change->minutes";
		$moreInfoMsg .= "$site->zone $site->base_number   $site->gas_level   $site->ct_ql ct   $gas_change->hours:$gas_change->minutes:$gas_change->seconds\n";
	}
	
	if (count($allSitesInfo) == 0) {
		$displayMsg = "No sites in watch list.";			
	} else {
		$link = bot::makeLink('More info', $moreInfoMsg, 'text');
		$displayMsg .= "<br />$link";
	}
	
	return $displayMsg;
}
?>