<?php
   /*
   ** Module: TRICKLE
   ** Author: Tyrence/Whiz (RK2)
   ** Description: Shows how much skills you will gain by increasing an ability
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): Fall 2008
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

require_once('trickle_functions.php');

$usageMsg = "Usage: <symbol>trickle &lt;ability&gt; &lt;amount&gt;\n<tab>ex: <symbol>trickle agi 20\nValid abilites are: agi, int, psy, sta, str, sen";

$msg = "";
$agi = 0;
$int = 0;
$psy = 0;
$sta = 0;
$str = 0;
$sen = 0;

// make sure the $ql is an integer between 1 and 300
if (!preg_match("/^trickle( ([a-zA-Z]+) ([0-9]+)){1,6}$/i", $message)) {

	$msg = $usageMsg;

} else {

	$array = explode(" ", $message);
	for ($i = 0; isset($array[1 + $i]); $i += 2) {
		$ability = getAbility($array[1 + $i]);
		$$ability = $array[2 + $i];
	}

	if ($ability == null) {

		$msg = $usageMsg;

	} else {
		
		global $abilities;
		
		$header = "";
		forEach ($abilities as $ability) {
			if ($$ability != 0) {
				$header .= " (" . ucfirst($ability) . " " . $$ability . ")";
			}
		}

		$results = getTrickleResults($agi, $int, $psy, $sta, $str, $sen);
		$output = formatOutput($results, $amount);
		$msg = $this->makeBlob("Trickle$header", $output);
	}
}

$this->send($msg, $sendto);

?>
