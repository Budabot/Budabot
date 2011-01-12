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

preg_match("/^impql (.*)$/i", $message, $arr);

// get the argument and set the ql variable
$ql = $arr[1];

$msg = "";

// make sure the $ql is an integer between 1 and 300
if (!preg_match("/^[0-9]+$/i", $ql, $p) || ($ql < 1) || ($ql > 300)) {
	$msg = "\nUsage: <symbol>impql &lt;implant_ql&gt;\nYou must enter a value between 1 and 300.";
} else {
	$obj = getRequirements($ql);
	$clusterInfo = formatClusterBonuses($obj);
	$link = bot::makeLink('More info', $clusterInfo, 'blob');
	$msg = "\nFor ql $ql imps\nTreatment required: $obj->treatment.\nAbility Required: $obj->ability\n$link";
}

bot::send($msg, $sendto);

?>
