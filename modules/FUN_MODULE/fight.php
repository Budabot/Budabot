<?php

if (preg_match("/^fight (.+) vs (.+)$/i", $message, $arr) || preg_match("/^fight (.+) (.+)$/i", $message, $arr)) {
  	$player1 = $arr[1];
  	$player2 = $arr[2];

	// Checks if user is trying to get Chuck Norris to fight another Chuck Norris
	if ((strcasecmp($player1, "chuck") == 0 || strcasecmp($player1, "chuck norris") == 0) &&  (strcasecmp($player2, "chuck") == 0 || strcasecmp($player2, "chuck norris") == 0)) {
		$msg = "Theres only enough room in this world for one Chuck Norris!";
		$sendto->reply($msg);
		return;
	}

	// This checks if the user is trying to get two of the same people fighting each other
	if (strcasecmp($player1, $player2) == 0) {
		$twin = array(
			"Dejavu?",
			"$player1 can't fight $player2, it may break the voids of space and time!",
			"As much as I'd love to see $player1 punching himself/herself in the face, it just isn't theoretical...");

		$sendto->reply(Util::rand_array_value($twin));
		return;
	}

	// Checks if Player 1/2 is chuck or chuck norris, and if so, sets HP to 100k and adds 10k - 100k damage to ensure victory.
	if (strcasecmp($player1, "chuck") == 0 OR strcasecmp($player1, "chuck norris") == 0) {
		$hp1 = 100000;
		$add_damage_P1 = rand(10000, 100000);
		$wep_P1 = "round house kick";
	} else {
		$hp1 = 10000;
		$add_damage_P1 = 0;
		$wep_P1 = "nerfstick";
	}
	if (strcasecmp($player2, "chuck") == 0 OR strcasecmp($player2, "chuck norris") == 0) {
		$hp2 = 100000;
		$add_damage_P2 = rand(10000, 100000);
		$wep_P2 = "round house kick";
	} else {
		$hp2 = 10000;
		$add_damage_P2 = 0;
		$wep_P2 = "nerfstick";
	}
   
   	// Checks if Player 1/2 is a Fixer, and if so, sets HP to 1 to ensure loss.
	if(strcasecmp($player1, "fixer") == 0 OR strcasecmp($player1, "fix") == 0) {
		$hp1 = 1;
		$wep_P1 = "tickle";
	} else {
		$hp1 = 10000;
		$add_damage_P1 = 0;
		$wep_P1 = "nerfstick";
	if(strcasecmp($player2, "fixer") == 0 OR strcasecmp($player2, "fix") == 0) {
		$hp2 = 1;
		$wep_P2 = "tickle";
	} else {
		$hp2 = 10000;
		$add_damage_P2 = 0;
		$wep_P2 = "nerfstick";
	}

  	$list = "Fight <highlight>$player1<end> VS <highlight>$player2<end> \n\n";
  	while ($hp1 > 0 && $hp2 > 0) {
		// player1 dmg to player2
	    $dmg = rand(50, 4000) + $add_damage_P1;
	    if ($dmg - $add_damage_P1 > 3000) {
			$crit = " <red>Critical Hit!<end>";
		} else {
			$crit = "";
		}
			
		$list .= "<highlight>$player1<end> hit <highlight>$player2<end> for $dmg of $wep_P1 dmg.$crit\n";
		$hp2 -= $dmg;
		
		// player2 dmg to player1
		$dmg = rand(50, 4000) + $add_damage_P2;
		if ($dmg - $add_damage_P2 > 3000) {
			$crit = " <red>Critical Hit!<end>";
		} else {
			$crit = "";
		}
			
		$list .= "<highlight>$player2<end> hit <highlight>$player1<end> for $dmg of $wep_P2 dmg.$crit\n";
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

	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
