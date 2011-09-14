<?php

if (!function_exists('makeAlienWeapon')) {
	function makeAlienWeapon($ql, $name) {
		$db = DB::get_instance();
	
		$name = str_replace("'", "''", $name);
		$db->query("SELECT * FROM aodb WHERE name = '{$name}' AND lowql <= $ql AND highql >= $ql");
		$data = $db->fObject('all');
		$row = $data[0];
		
		return Text::make_item($row->lowid, $row->highid, $ql, $row->name);
	}
}

if (!function_exists('getWeaponInfo')) {
	function getWeaponInfo($ql) {
		$ts_wep = floor($ql * 6);
		$text .= "\n\n<highlight>QL $ql<end> is the highest weapon this type will combine into.";
		if ($ql != 300) {
			$text .= "\nNote: <highlight>The weapon can bump several QL's.<end>";
		}
		$text .= "\n\nIt will take <highlight>$ts_wep<end> ME & WS (<highlight>6 * QL<end>) to combine with a QL $ql Kyr'ozch Weapon.";
		
		return $text;
	}
}

if (preg_match("/^bioinfo (1|2|3|4|5|12|13|48|76|112|240|880|992|pristine|mutated|serum)$/i", $message, $arr) ||
	preg_match("/^bioinfo (1|2|3|4|5|12|13|48|76|112|240|880|992|pristine|mutated|serum) (\\d+)$/i", $message, $arr)) {

	$bio = $arr[1];
	$ql = 300;
	if ($arr[2]) {
		$ql = $arr[2];
	}
	
	// Ensures that the maximum AI weapon that combines into doesn't go over QL 300 when the user presents a QL 271+ bio-material
	if ($ql >= 1 && $ql <= 270) {
		$maxaitype = floor($ql / 0.9);
	} else {
		$maxaitype = 300;
	}

	// pristine/mutated start
	$odd_ql = $ql * 0.8;
	if ($odd_ql < 1) {
		$min_ql = 1;
	} else {
		$min_ql = floor($ql * 0.8);
	}
	$armor_cl = floor($min_ql * 4.5);
	$armor_chem_min = floor($ql * 4.5);
	$armor_chem_max = floor($ql * 7);
	$armor_pharma = floor($ql * 6);
	$armor_np = floor($min_ql * 6);
	$armor_psyco = floor($ql * 6);
	// pristine/mutated end

	switch ($bio) {
		// Double case acts as an OR for the low/high ID's of the bio-materials, so it will check for QL 1 bio-materials now
		case "1":
			$name = "Kyr'Ozch Bio-Material - Type 1";
			$high_id = 247684;
			$low_id = 247683;

			$use = "<highlight>Adds Fling shot to:<end>\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Grenade Gun - Type 1") .
				"\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Pistol - Type 1") .
				"\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Shotgun - Type 1");
			$use .= getWeaponInfo($maxaitype);
			break;
		case "2":
			$name = "Kyr'Ozch Bio-Material - Type 2";
			$high_id = 247686;
			$low_id = 247685;

			$use = "<highlight>Adds Aimed Shot to:<end>\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Crossbow - Type 2") .
				"\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Rifle - Type 2");
			$use .= getWeaponInfo($maxaitype);
			break;
		case "3":
			$name = "Kyr'Ozch Bio-Material - Type 3 ";
			$high_id = 247694;
			$low_id = 247693;

			$use = "<highlight>Adds Fling Shot/Aimed Shot to:<end>\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Crossbow - Type 3") .
				"\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Energy Carbine - Type 3") .
				"\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Rifle - Type 3");
			$use .= getWeaponInfo($maxaitype);
			break;
		case "4":
			$name = "Kyr'Ozch Bio-Material - Type 4";
			$high_id = 247688;
			$low_id = 247687;

			$use = "<highlight>Adds Burst to:<end>\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Machine Pistol - Type 4") .
				"\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Pistol - Type 4") .
				"\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Submachine Gun - Type 4");
			$use .= getWeaponInfo($maxaitype);
			break;
		case "5":
			$name = "Kyr'Ozch Bio-Material - Type 5";
			$high_id = 247690;
			$low_id = 247689;

			$use = "<highlight>Adds Fling Shot/Burst to:<end>\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Carbine - Type 5") .
				"\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Energy Carbine - Type 5") .
				"\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Energy Pistol - Type 5") .
				"\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Machine Pistol - Type 5") .
				"\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Submachine Gun - Type 5");
			$use .= getWeaponInfo($maxaitype);
			break;
		case "12":
			$name = "Kyr'Ozch Bio-Material - Type 12 ";
			$high_id = 247692;
			$low_id = 247691;

			$use = "<highlight>Adds Burst/Full Auto to:<end>\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Carbine - Type 12") .
				"\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Submachine Gun - Type 12");
			$use .= getWeaponInfo($maxaitype);
			break;
		case "13":
			$name = "Kyr'Ozch Bio-Material - Type 13";
			$high_id = 247696;
			$low_id = 247695;

			$use = "<highlight>Adds Burst/Fling Shot/Full Auto to:<end>\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Carbine - Type 13");
			$use .= getWeaponInfo($maxaitype);
			break;
		case "48":
			$name = "Kyr'Ozch Bio-Material - Type 48";
			$high_id = 288673;
			$low_id = 288672;

			$use = "<highlight>Adds Brawl/Dimach to:<end>\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Nunchacko - Type 48");
			$use .= getWeaponInfo($maxaitype);
			break;
		case "76":
			$name = "Kyr'Ozch Bio-Material - Type 76";
			$high_id = 247674;
			$low_id = 247673;

			$use = "<highlight>Adds Brawl/Fast Attack to:<end>\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Energy Sword - Type 76") .
				"\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Sledgehammer - Type 76");
			$use .= getWeaponInfo($maxaitype);
			break;
		case "112":
			$name = "Kyr'Ozch Bio-Material - Type 112";
			$high_id = 247676;
			$low_id = 247675;

			$use = "<highlight>Adds Brawl/Dimach/Fast Attack to:<end>\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Energy Hammer - Type 112") .
				"\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Hammer - Type 112") .
				"\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Spear - Type 112") .
				"\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Sword - Type 112");
			$use .= getWeaponInfo($maxaitype);
			break;
		case "240":
			$name = "Kyr'Ozch Bio-Material - Type 240";
			$high_id = 247678;
			$low_id = 247677;

			$use = "<highlight>Adds Brawl/Dimach/Fast Attack/Sneak Attack to:<end>\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Axe - Type 240");
			$use .= getWeaponInfo($maxaitype);
			break;
		case "880":
			$name = "Kyr'Ozch Bio-Material - Type 880";
			$high_id = 247680;
			$low_id = 247679;

			$use = "<highlight>Adds Dimach/Fast Attack/Parry/Riposte to:<end>\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Sword - Type 880");
			$use .= getWeaponInfo($maxaitype);
			break;
		case "992":
			$name = "Kyr'Ozch Bio-Material - Type 992";
			$high_id = 247682;
			$low_id = 247681;

			$use = "<highlight>Adds Dimach/Fast Attack/Sneak Attack/Parry/Riposte to:<end>\n" . makeAlienWeapon($maxaitype, "Kyr'Ozch Energy Rapier - Type 992");
			$use .= getWeaponInfo($maxaitype);
			break;
		case "pristine":
			// This activates it as an armor type so it will display the additional armor information without putting the information in twice for mutataed and pristine.
			$ts_notify = 2;
			$name = "Pristine Kyr'Ozch Bio-Material";
			$high_id = 247107;
			$low_id = 247106;
			$use = "Used to build Alien Armor (<highlight>less tradeskill requirements then mutated.<end>)\n\n" .
				"<highlight>The following tradeskill amounts are required to make<end> QL $ql<highlight>\n" .
				"strong/arithmetic/enduring/spiritual/supple/observant armor:<end>\n\n" .
				"Computer Literacy - <highlight>$armor_cl<end> (<highlight>4.5 * QL<end>)\nChemistry - <highlight>$armor_chem_min<end> (<highlight>4.5 * QL<end>)\n" .
				"Nano Programming - <highlight>$armor_np<end> (<highlight>6 * QL<end>)\n" . 
				"Pharma Tech - <highlight>$armor_pharma<end> (<highlight>6 * QL<end>)\n" .
				"Psychology - <highlight>$armor_psyco<end> (<highlight>6 * QL<end>)\n\n" .
				"Note:<highlight> Tradeskill requirements are based off the lowest QL items needed throughout the entire process.<end>";
			break;
		case "mutated":
			$ts_notify = 2;
			$name = "Mutated Kyr'Ozch Bio-Material";
			$high_id = 247109;
			$low_id = 247108;
			$use = "Used to build Alien Armor\n\n" .
				"<highlight>The following tradeskill amounts are required to make<end> QL $ql<highlight>\n" .
				"strong/arithmetic/enduring/spiritual/supple/observant armor:<end>\n\n" .
				"Computer Literacy - <highlight>$armor_cl<end> (<highlight>4.5 * QL<end>)\n" .
				"Chemistry - <highlight>$armor_chem_max<end> (<highlight>7 * QL<end>)\n" .
				"Nano Programming - <highlight>$armor_np<end> (<highlight>6 * QL<end>)\n" .
				"Pharma Tech - <highlight>$armor_pharma<end> (<highlight>6 * QL<end>)\n" .
				"Psychology - <highlight>$armor_psyco<end> (<highlight>6 * QL<end>)\n\n" .
				"Note:<highlight> Tradeskill requirements are based off the lowest QL items needed throughout the entire process.<end>";
			break;
		case "serum":
			$high_id = 254805;
			$low_id = 247765;
			$pharma_ts = floor($ql * 3.5);
			$chem_me_ts = floor($ql * 4);
			$ee_ts = floor($ql * 4.5);
			$cl_ts = floor($ql * 5);
			$use = "<highlight>Used to build city buildings<end>\n\n" .
				"<highlight>The following are the required skills throughout the process of making a building:<end>\n\n" .
				"Quantum FT - <highlight>400<end> (<highlight>Static<end>)\nPharma Tech - ";
			//Used to change dialog between minimum and actual requirements, for requirements that go under 400
			if ($pharma_ts < 400) {
				$use .= "<highlight>400<end>";
			} else {
				$use .= "<highlight>$pharma_ts<end>";
			}
			$use .= " (<highlight>3.5 * QL<end>) 400 is minimum requirement\nChemistry - ";
			if ($chem_me_ts < 400) {
				$use .= "<highlight>400<end>";
			} else {
				$use .= "<highlight>$chem_me_ts<end>";
			}
			$use .= " (<highlight>4 * QL<end>) 400 is minimum requirement\n" .
				"Mechanical Engineering - <highlight>$chem_me_ts<end> (<highlight>4 * QL<end>)\n" .
				"Electrical Engineering - <highlight>$ee_ts<end> (<highlight>4.5 * QL<end>)\n" .
				"Comp Liter - <highlight>$cl_ts<end> (<highlight>5 * QL<end>)";
			$name = "Kyr'Ozch Viral Serum";
			break;
		default:
			$chatBot->send("Unknown Bio-Material", $sendto);
			return;
	}

	// Weapons and armor are ticked with a $ts_notify so the additional text only goes to them, without repeating the text for each individual weapon or armor.
	if ($ts_notify == 2) {
		// All the min/max QL and tradeskill calcs for the mutated/pristine process
		if ($ql >= 1 && $ql <= 240) {
			$max_ql = floor($ql / 0.8);
		} else {
			$max_ql = 300;
		}
		$use .= "\n\nFor Supple, Arithmetic, or Enduring:\n\n" . 
			"<highlight>When completed, the armor piece can have as low as<end> QL $min_ql <highlight>combine into it, depending on available tradeskill options.\n\n" .
			"Does not change QL's, therefore takes<end> $armor_psyco <highlight>Psychology for available combinations.<end>\n\n" . 
			"For Spiritual, Strong, or Observant:\n\n" . 
			"<highlight>When completed, the armor piece can combine upto<end> QL $max_ql<highlight>, depending on available tradeskill options.\n\n" .
			"Changes QL depending on targets QL. The max combination (<end>$max_ql<highlight>) (<end>$armor_psyco required for this combination<highlight>)<end>";
	}
	
	$ts_bio = floor($ql * 4.5);
	
	$blob = "<header> :::::: $name (QL $ql) :::::: <end>\n\n";
	$blob .= Text::make_item($low_id, $high_id, $ql, $name) . "\nIt will take <highlight>$ts_bio<end> EE & CL (<highlight>4.5 * QL<end>) to analyze the Bio-Material.\n\n";
	$blob .= $use;
	$blob .= "\n\n<yellow>Tradeskilling info added by Mdkdoc420 (RK2)<end>";

	$msg = Text::make_blob("$name (ql $ql)", $blob);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>