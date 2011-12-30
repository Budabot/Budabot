<?php

function findItem($ql, $name) {
	global $chatBot;
	$db = $chatBot->getInstance('db');

	$row = $db->queryRow("SELECT * FROM aodb WHERE name = ? AND lowql <= ? AND highql >= ?", $name, $ql, $ql);
	
	return Text::make_item($row->lowid, $row->highid, $ql, $row->name);
}

function getWeaponInfo($ql) {
	$ts_wep = floor($ql * 6);
	$text .= "\n\n<highlight>QL $ql<end> is the highest weapon this type will combine into.";
	if ($ql != 300) {
		$text .= "\nNote: <highlight>The weapon can bump several QL's.<end>";
	}
	$text .= "\n\nIt will take <highlight>$ts_wep<end> ME & WS (<highlight>6 * QL<end>) to combine with a QL $ql Kyr'ozch Weapon.";
	
	return $text;
}

function ofabArmorBio($ql, $type) {
	global $chatBot;
	$db = $chatBot->getInstance('db');

	$name = "Kyr'Ozch Bio-Material - Type $type";
	$item = findItem($ql, $name);
	
	$data = $db->query("SELECT * FROM ofabarmortype WHERE type = ?", $type);

	$blob = "<header> :::::: $name (QL $ql) :::::: <end>\n\n";
	$blob .= $item . "\n\n";
	$blob .= "<highlight>Upgrades Ofab armor for:<end>\n";
	forEach ($data as $row) {
		$blob .= Text::make_chatcmd($row->profession, "/tell <myname> ofabarmor {$row->profession}") . "\n";
	}
	
	return Text::make_blob("$name (QL $ql)", $blob);
}

function ofabWeaponBio($ql, $type) {
	global $chatBot;
	$db = $chatBot->getInstance('db');

	$name = "Kyr'Ozch Bio-Material - Type $type";
	$item = findItem($ql, $name);
	
	$data = $db->query("SELECT * FROM ofabweapons WHERE type = ?", $type);

	$blob = "<header> :::::: $name (QL $ql) :::::: <end>\n\n";
	$blob .= $item . "\n\n";
	$blob .= "<highlight>Upgrades Ofab weapons:<end>\n";
	forEach ($data as $row) {
		$blob .= Text::make_chatcmd("Ofab {$row->name} Mk 1", "/tell <myname> ofabweapons {$row->name}") . "\n";
	}
	
	return Text::make_blob("$name (QL $ql)", $blob);
}

function alienWeaponBio($ql, $type) {
	global $chatBot;
	$db = $chatBot->getInstance('db');

	$name = "Kyr'Ozch Bio-Material - Type $type";
	$item = findItem($ql, $name);
	
	// Ensures that the maximum AI weapon that combines into doesn't go over QL 300 when the user presents a QL 271+ bio-material
	$maxaitype = floor($ql / 0.9);
	if ($maxaitype > 300 || $maxaitype < 1) {
		$maxaitype = 300;
	}
	
	$ts_bio = floor($ql * 4.5);
	
	$row = $db->queryRow("SELECT specials FROM alienweaponspecials WHERE type = ?", $type);
	$specials = $row->specials;
	
	$data = $db->query("SELECT * FROM alienweapons WHERE type = ?", $type);

	$blob = "<header> :::::: $name (QL $ql) :::::: <end>\n\n";
	$blob .= $item . "\n\n";
	$blob .= "It will take <highlight>$ts_bio<end> EE & CL (<highlight>4.5 * QL<end>) to analyze the Bio-Material.\n\n";
	$blob .= "<highlight>Adds {$specials} to:<end>\n";
	forEach ($data as $row) {
		$blob .= findItem($maxaitype, $row->name) . "\n";
	}
	$blob .= getWeaponInfo($maxaitype);
	$blob .= "\n\n<yellow>Tradeskilling info added by Mdkdoc420 (RK2)<end>";
	
	return Text::make_blob("$name (QL $ql)", $blob);
}

function alienArmorBio($ql, $type) {
	if (strtolower($type) == "mutated") {
		$name = "Mutated Kyr'Ozch Bio-Material";
		$chem = floor($ql * 7);
	} else if (strtolower($type) == "pristine") {
		$name = "Pristine Kyr'Ozch Bio-Material";
		$chem = floor($ql * 4.5);
		$extraInfo = "(<highlight>less tradeskill requirements then mutated.<end>)";
	} else {
		$name = "UNKNOWN";
	}
	
	$min_ql = $ql * 0.8;
	if ($min_ql < 1) {
		$min_ql = 1;
	}
	
	// All the min/max QL and tradeskill calcs for the mutated/pristine process
	if ($ql >= 1 && $ql <= 240) {
		$max_ql = floor($ql / 0.8);
	} else {
		$max_ql = 300;
	}

	$cl = floor($min_ql * 4.5);
	$pharma = floor($ql * 6);
	$np = floor($min_ql * 6);
	$psyco = floor($ql * 6);
	$ts_bio = floor($ql * 4.5);
	
	$item = findItem($ql, $name);
	
	$blob = "<header> :::::: $name (QL $ql) :::::: <end>\n\n";
	$blob .= $item . "\n\n";
	$blob .= "It will take <highlight>$ts_bio<end> EE & CL (<highlight>4.5 * QL<end>) to analyze the Bio-Material.\n\n";
	
	$blob .= "Used to build Alien Armor $extraInfo\n\n" .
		"<highlight>The following tradeskill amounts are required to make<end> QL $ql<highlight>\n" .
		"strong/arithmetic/enduring/spiritual/supple/observant armor:<end>\n\n" .
		"Computer Literacy - <highlight>$cl<end> (<highlight>4.5 * QL<end>)\n" .
		"Chemistry - <highlight>$chem<end> (<highlight>7 * QL<end>)\n" .
		"Nano Programming - <highlight>$np<end> (<highlight>6 * QL<end>)\n" .
		"Pharma Tech - <highlight>$pharma<end> (<highlight>6 * QL<end>)\n" .
		"Psychology - <highlight>$psyco<end> (<highlight>6 * QL<end>)\n\n" .
		"Note:<highlight> Tradeskill requirements are based off the lowest QL items needed throughout the entire process.<end>";
		
	$blob .= "\n\nFor Supple, Arithmetic, or Enduring:\n\n" . 
		"<highlight>When completed, the armor piece can have as low as<end> QL $min_ql <highlight>combine into it, depending on available tradeskill options.\n\n" .
		"Does not change QL's, therefore takes<end> $psyco <highlight>Psychology for available combinations.<end>\n\n" . 
		"For Spiritual, Strong, or Observant:\n\n" . 
		"<highlight>When completed, the armor piece can combine upto<end> QL $max_ql<highlight>, depending on available tradeskill options.\n\n" .
		"Changes QL depending on targets QL. The max combination (<end>$max_ql<highlight>) (<end>$psyco required for this combination<highlight>)<end>";

	$blob .= "\n\n<yellow>Tradeskilling info added by Mdkdoc420 (RK2)<end>";
	
	return Text::make_blob("$name (QL $ql)", $blob);
}

function serumBio($ql, $type) {
	$name = "Kyr'Ozch Viral Serum";
	$item = findItem($ql, $name);

	$pharma_ts = floor($ql * 3.5);
	$chem_me_ts = floor($ql * 4);
	$ee_ts = floor($ql * 4.5);
	$cl_ts = floor($ql * 5);
	$ts_bio = floor($ql * 4.5);
	
	$blob = "<header> :::::: $name (QL $ql) :::::: <end>\n\n";
	$blob .= $item . "\n\n";
	$blob .= "It will take <highlight>$ts_bio<end> EE & CL (<highlight>4.5 * QL<end>) to analyze the Bio-Material.\n\n";
	
	$blob .= "<highlight>Used to build city buildings<end>\n\n" .
		"<highlight>The following are the required skills throughout the process of making a building:<end>\n\n" .
		"Quantum FT - <highlight>400<end> (<highlight>Static<end>)\nPharma Tech - ";
	
	//Used to change dialog between minimum and actual requirements, for requirements that go under 400
	if ($pharma_ts < 400) {
		$blob .= "<highlight>400<end>";
	} else {
		$blob .= "<highlight>$pharma_ts<end>";
	}
	
	$blob .= " (<highlight>3.5 * QL<end>) 400 is minimum requirement\nChemistry - ";
	
	if ($chem_me_ts < 400) {
		$blob .= "<highlight>400<end>";
	} else {
		$blob .= "<highlight>$chem_me_ts<end>";
	}
	
	$blob .= " (<highlight>4 * QL<end>) 400 is minimum requirement\n" .
		"Mechanical Engineering - <highlight>$chem_me_ts<end> (<highlight>4 * QL<end>)\n" .
		"Electrical Engineering - <highlight>$ee_ts<end> (<highlight>4.5 * QL<end>)\n" .
		"Comp Liter - <highlight>$cl_ts<end> (<highlight>5 * QL<end>)";
		
	$blob .= "\n\n<yellow>Tradeskilling info added by Mdkdoc420 (RK2)<end>";
	
	return Text::make_blob("$name (QL $ql)", $blob);
}

?>
