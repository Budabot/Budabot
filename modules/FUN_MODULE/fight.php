<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Let 2 players fight against each other
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 24.07.2006
   ** Date(last modified): 24.07.2006
   ** 
   ** Copyright (C) 2006 Carsten Lohmann
   **
   ** Licence Infos: 
   ** This file is part of Budabot.
   **
   ** Budabot is free software; you can redistribute it and/or modify
   ** it under the terms of the GNU General Public License as published by
   ** the Free Software Foundation; either version 2 of the License, or
   ** (at your option) any later version.
   **
   ** Budabot is distributed in the hope that it will be useful,
   ** but WITHOUT ANY WARRANTY; without even the implied warranty of
   ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   ** GNU General Public License for more details.
   **
   ** You should have received a copy of the GNU General Public License
   ** along with Budabot; if not, write to the Free Software
   ** Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   */
   
if (preg_match("/^fight (.+) vs (.+)$/i", $message, $arr) || preg_match("/^fight (.+) (.+)$/i", $message, $arr)) {
  	$player1 = $arr[1];
  	$player2 = $arr[2];
  	$hp1 = 10000;
  	$hp2 = 10000;
  	
  	$list = "Fight <highlight>$player1<end> VS <highlight>$player2<end> \n\n";
  	while ($hp1 > 0 && $hp2 > 0) {
		// player1 dmg to player2
	    $dmg = rand(50, 4000);
	    if ($dmg > 3000) {
			$crit = " <red>Critical Hit!<end>";
		} else {
			$crit = "";
		}
			
		$list .= "<highlight>$player1<end> hit <highlight>$player2<end> for $dmg of nerfstick dmg.$crit\n";
		$hp2 -= $dmg;
		
		// player2 dmg to player1
		$dmg = rand(50, 4000);
		if ($dmg > 3000) {
			$crit = " <red>Critical Hit!<end>";
		} else {
			$crit = "";
		}
			
		$list .= "<highlight>$player2<end> hit <highlight>$player1<end> for $dmg of nerfstick dmg.$crit\n";
		$hp1 -= $dmg;
		
		$list .= "\n";
	}
	
	if ($hp1 > $hp2) {
		$list .= "\nAnd the winner is ..... <highlight>$player1!<end>";
		$msg = Text::make_blob("$player1 vs $player2....$player1 wins!", $list);
	} else if ($hp2 > $hp1) {
		$list .= "\nAnd the winner is ..... <highlight>$player2!<end>";
		$msg = Text::make_blob("$player1 vs $player2....$player2 wins!", $list);
	} else {
		$list .= "\nIt's a tie!!";
		$msg = Text::make_blob("$player1 vs $player2....It's a tie!", $list);
	}

	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
