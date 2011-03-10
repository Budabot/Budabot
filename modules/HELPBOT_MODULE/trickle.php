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

if (preg_match("/^trickle( ([a-zA-Z]+) ([0-9]+)){1,6}$/i", $message, $arr1) || preg_match("/^trickle( ([0-9]+) ([a-zA-Z]+)){1,6}$/i", $message, $arr2)) {
	$msg = "";
	
	$abilities = array('agi' => 0, 'int' => 0, 'psy' => 0, 'sta' => 0, 'str' => 0, 'sen' => 0);
	
	if ($arr1) {
		$array = explode(" ", $message);
		array_shift($array);
		for ($i = 0; isset($array[$i]); $i += 2) {
			$ability = Util::get_ability($array[$i]);
			if ($ability == null) {
				$syntax_error = true;
				return;
			}
			
			$abilities[$ability] = $array[1 + $i];
		}
	} else if ($arr2) {
		$array = explode(" ", $message);
		array_shift($array);
		for ($i = 0; isset($array[$i]); $i += 2) {
			$ability = Util::get_ability($array[1 + $i]);
			if ($ability == null) {
				$syntax_error = true;
				return;
			}
			
			$abilities[$ability] = $array[$i];
		}
	}

	$header = "";
	forEach ($abilities as $ability => $value) {
		if ($value != 0) {
			$header .= " (" . ucfirst($ability) . " " . $value . ")";
		}
	}

	$output = Text::make_header("Trickle$header", "none");

	$results = getTrickleResults($abilities);
	$output .= formatOutput($results, $amount, $abilities);
	$msg = Text::make_link("Trickle Results", $output);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
