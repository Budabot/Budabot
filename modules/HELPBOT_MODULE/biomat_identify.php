<?php

if (preg_match("/^bio <a href=\"itemref:\/\/([0-9]+)\/([0-9]+)\/([0-9]+)\">Solid Clump of Kyr\'Ozch Bio-Material<\/a>$/i", $message, $arr)){
	$highid = $arr[2];
	$ql = $arr[3];
	switch ($highid) {
		case 247103:
			$high_id = 247107;
			$low_id = 247106;
			$use = "<highlight>Used to build Alien Armor(lesser Req. for building as Mutated)<end>";
			$name = "Pristine Kyr'Ozch Bio-Material";
			break;
		case 247105:
			$high_id = 247109;
			$low_id = 247108;
			$use = "<highlight>Used to build Alien Armor<end>";
			$name = "Mutated Kyr'Ozch Bio-Material";
			break;
		case 247698:
			$high_id = 247674;
			$low_id = 247673;
			$use = "<highlight>Adds Brawl/Fast Attack to:<end>\n<tab>" . Text::make_item(254751, 254751, 300, "Kyr'Ozch Energy Sword - Type 76") .
				"\n<tab>" . Text::make_item(254737, 254737, 300, "Kyr'Ozch Sledgehammer - Type 76");
			$name = "Kyr'Ozch Bio-Material - Type 76";
			break;
		case 247700:
			$high_id = 247676;
			$low_id = 247675;
			$use = "<highlight>Adds Brawl/Dimach/Fast Attack to:<end>\n<tab>" . Text::make_item(254709, 254709, 300, "Kyr'Ozch Energy Hammer - Type 112") .
				"\n<tab>" . Text::make_item(254681, 254681, 300, "Kyr'Ozch Hammer - Type 112") .
				"\n<tab>" . Text::make_item(254786, 254786, 300, "Kyr'Ozch Spear - Type 112") .
				"\n<tab>" . Text::make_item(254765, 254765, 300, "Kyr'Ozch Sword - Type 112");
			$name = "Kyr'Ozch Bio-Material - Type 112";
			break;
		case 247702:
			$high_id = 247678;
			$low_id = 247677;
			$use = "<highlight>Adds Brawl/Dimach/Fast Attack/Sneak Attack to:<end>\n<tab>" . Text::make_item(254695, 254695, 300, "Kyr'Ozch Axe - Type 240");
			$name = "Kyr'Ozch Bio-Material - Type 240";
			break;
		case 247704:
			$high_id = 247680;
			$low_id = 247679;
			$use = "<highlight>Adds Dimach/Fast Attack/Parry/Riposte to:<end>\n<tab>" . Text::make_item(254772, 254772, 300, "Kyr'Ozch Sword - Type 880");
			$name = "Kyr'Ozch Bio-Material - Type 880";
			break;
		case 247706:
			$highid = 247682;
			$low_id = 247681;
			$use = "<highlight>Adds Dimach/Fast Attack/Sneak Attack/Parry/Riposte to:<end>\n<tab>" . Text::make_item(254723, 254723, 300, "Kyr'Ozch Energy Rapier - Type 992");
			$name = "Kyr'Ozch Bio-Material - Type 992";
			break;
		case 247708:
			$high_id = 247684;
			$low_id = 247683;
			$use = "<highlight>Adds Fling shot to:<end>\n<tab>" . Text::make_item(254576, 254576, 300, "Kyr'Ozch Grenade Gun - Type 1") .
				"\n<tab>" . Text::make_item(254625, 254625, 300, "Kyr'Ozch Pistol - Type 1") .
				"\n<tab>" . Text::make_item(254590, 254590, 300, "Kyr'Ozch Shotgun - Type 1");
			$name = "Kyr'Ozch Bio-Material - Type 1";
			break;
		case 247710:
			$high_id = 247686;
			$low_id = 247685;
			$use = "<highlight>Adds Aimed Shot to:<end>\n<tab>" . Text::make_item(254611, 254611, 300, "Kyr'Ozch Crossbow - Type 2") .
				"\n<tab>" . Text::make_item(254478, 254478, 300, "Kyr'Ozch Rifle - Type 2");
			$name = "Kyr'Ozch Bio-Material - Type 2";
			break;
		case 247712:
			$high_id = 247686;
			$low_id = 247685;
			$use = "<highlight>Adds Burst to:<end>\n<tab>" . Text::make_item(254660, 254660, 300, "Kyr'Ozch Machine Pistol - Type 4") .
				"\n<tab>" . Text::make_item(254632, 254632, 300, "Kyr'Ozch Pistol - Type 4") .
				"\n<tab>" . Text::make_item(254527, 254527, 300, "Kyr'Ozch Submachine Gun - Type 4");
			$name = "Kyr'Ozch Bio-Material - Type 4";
			break;
		case 247714:
			$high_id = 247690;
			$low_id = 247689;
			$use = "<highlight>Adds Fling Shot/Burst to:<end>\n<tab>" . Text::make_item(254499, 254499, 300, "Kyr'Ozch Carbine - Type 5") .
				"\n<tab>" . Text::make_item(254555, 254555, 300, "Kyr'Ozch Energy Carbine - Type 5") .
				"\n<tab>" . Text::make_item(254646, 254646, 300, "Kyr'Ozch Energy Pistol - Type 5") .
				"\n<tab>" . Text::make_item(254667, 254667, 300, "Kyr'Ozch Machine Pistol - Type 5") .
				"\n<tab>" . Text::make_item(254534, 254534, 300, "Kyr'Ozch Submachine Gun - Type 5");
			$name = "Kyr'Ozch Bio-Material - Type 5";
			break;
		case 247716:
			$high_id = 247692;
			$low_id = 247691;
			$use = "<highlight>Adds Burst/Full Auto to:\n<tab>" . Text::make_item(254506, 254506, 300, "Kyr'Ozch Carbine - Type 12") .
				"\n<tab>" . Text::make_item(254541, 254541, 300, "Kyr'Ozch Submachine Gun - Type 12");
			$name = "Kyr'Ozch Bio-Material - Type 12 ";
			break;
		case 247718:
			$high_id = 247694;
			$low_id = 247693;
			$use = "<highlight>Adds Fling Shot/Aimed Shot to:<end>\n<tab>" . Text::make_item(254604, 254604, 300, "Kyr'Ozch Crossbow - Type 3") .
				"\n<tab>" . Text::make_item(254562, 254526, 300, "Kyr'Ozch Energy Carbine - Type 3") .
				"\n<tab>" . Text::make_item(254485, 254485, 300, "Kyr'Ozch Rifle - Type 3");
			$name = "Kyr'Ozch Bio-Material - Type 3 ";
			break;
		case 247720:
			$high_id = 247696;
			$low_id = 247695;
			$use = "<highlight>Adds Burst/Fling Shot/Full Auto to:<end>\n<tab>" . Text::make_item(254513, 254513, 300, "Kyr'Ozch Carbine - Type 13");
			$name = "Kyr'Ozch Bio-Material - Type 13";
			break;
		case 254804:
			$high_id = 254805;
			$low_id = 247765;
			$use = "<highlight>Used to build city buildings<end>";
			$name = "Kyr'Ozch Viral Serum";
			break;
		case 288700:
			$high_id = 288673;
			$low_id = 288672;
			$use = "<highlight>Adds Brawl/Dimach to:<end>\n<tab>" . Text::make_item(288671, 288671, 300, "Kyr'Ozch Nunchacko - Type 48");
			$name = "Kyr'Ozch Bio-Material - Type 48";
			break;
		default:
			$chatBot->send("Unknown Bio-Material", $sendto);
			return;
	}
	
	$blob = "<header> :::::: $name (QL $ql) :::::: <end>\n\n";
	$blob .= Text::make_item($low_id, $high_id, $ql, $name) . "\n\n";
	$blob .= $use;
	
	$msg = Text::make_link("$name (ql $ql)", $blob, 'blob');
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>