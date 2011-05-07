<?php

if (preg_match("/^bio <a href=\"itemref:\/\/([0-9]+)\/([0-9]+)\/([0-9]+)\">Solid Clump of Kyr\'Ozch Bio-Material<\/a>$/i", $message, $arr)){

	$highid = $arr[2];
	$ql = $arr[3];
		if ($arr[3] >= 1 && $arr[3] <= 270) {
			$maxaitype = (int)round($arr[3] / 0.9);
		} else {
			$maxaitype = $arr[3];
		}
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
			$use = "<highlight>Adds Brawl/Fast Attack to:<end>\n<tab>" . Text::make_item(247631, 254751, $maxaitype, "Kyr'Ozch Energy Sword - Type 76") .
				"\n<tab>" . Text::make_item(247617, 254737, $maxaitype, "Kyr'Ozch Sledgehammer - Type 76") . "\n\n"  . "<highlight>$maxaitype is the highest QL weapon this type will combine into.<end>";
			$name = "Kyr'Ozch Bio-Material - Type 76";
			break;
		case 247700:
			$high_id = 247676;
			$low_id = 247675;
			$use = "<highlight>Adds Brawl/Dimach/Fast Attack to:<end>\n<tab>" . Text::make_item(247587, 254709, $maxaitype, "Kyr'Ozch Energy Hammer - Type 112") .
				"\n<tab>" . Text::make_item(247559, 254681, $maxaitype, "Kyr'Ozch Hammer - Type 112") .
				"\n<tab>" . Text::make_item(247666, 254786, $maxaitype, "Kyr'Ozch Spear - Type 112") .
				"\n<tab>" . Text::make_item(247645, 254765, $maxaitype, "Kyr'Ozch Sword - Type 112") . "\n\n"  . "<highlight>$maxaitype is the highest QL weapon this type will combine into.<end>";
			$name = "Kyr'Ozch Bio-Material - Type 112";
			break;
		case 247702:
			$high_id = 247678;
			$low_id = 247677;
			$use = "<highlight>Adds Brawl/Dimach/Fast Attack/Sneak Attack to:<end>\n<tab>" . Text::make_item(247573, 254695, $maxaitype, "Kyr'Ozch Axe - Type 240") . "\n\n"  . "<highlight>$maxaitype is the highest QL weapon this type will combine into.<end>";
			$name = "Kyr'Ozch Bio-Material - Type 240";
			break;
		case 247704:
			$high_id = 247680;
			$low_id = 247679;
			$use = "<highlight>Adds Dimach/Fast Attack/Parry/Riposte to:<end>\n<tab>" . Text::make_item(247652, 254772, $maxaitype, "Kyr'Ozch Sword - Type 880") . "\n\n"  . "<highlight>$maxaitype is the highest QL weapon this type will combine into.<end>";
			$name = "Kyr'Ozch Bio-Material - Type 880";
			break;
		case 247706:
			$highid = 247682;
			$low_id = 247681;
			$use = "<highlight>Adds Dimach/Fast Attack/Sneak Attack/Parry/Riposte to:<end>\n<tab>" . Text::make_item(247603, 254723, $maxaitype, "Kyr'Ozch Energy Rapier - Type 992") . "\n\n"  . "<highlight>$maxaitype is the highest QL weapon this type will combine into.<end>";
			$name = "Kyr'Ozch Bio-Material - Type 992";
			break;
		case 247708:
			$high_id = 247684;
			$low_id = 247683;
			$use = "<highlight>Adds Fling shot to:<end>\n<tab>" . Text::make_item(247437, 254576, $maxaitype, "Kyr'Ozch Grenade Gun - Type 1") .
				"\n<tab>" . Text::make_item(247503, 254625, $maxaitype, "Kyr'Ozch Pistol - Type 1") .
				"\n<tab>" . Text::make_item(247452, 254590, $maxaitype, "Kyr'Ozch Shotgun - Type 1") . "\n\n"  . "<highlight>$maxaitype is the highest QL weapon this type will combine into.<end>";
			$name = "Kyr'Ozch Bio-Material - Type 1";
			break;
		case 247710:
			$high_id = 247686;
			$low_id = 247685;
			$use = "<highlight>Adds Aimed Shot to:<end>\n<tab>" . Text::make_item(247485, 254611, $maxaitype, "Kyr'Ozch Crossbow - Type 2") .
				"\n<tab>" . Text::make_item(247313, 254478, $maxaitype, "Kyr'Ozch Rifle - Type 2") . "\n\n"  . "<highlight>$maxaitype is the highest QL weapon this type will combine into.<end>";
			$name = "Kyr'Ozch Bio-Material - Type 2";
			break;
		case 247712:
			$high_id = 247686;
			$low_id = 247685;
			$use = "<highlight>Adds Burst to:<end>\n<tab>" . Text::make_item(247538, 254660, $maxaitype, "Kyr'Ozch Machine Pistol - Type 4") .
				"\n<tab>" . Text::make_item(247510, 254632, $maxaitype, "Kyr'Ozch Pistol - Type 4") .
				"\n<tab>" . Text::make_item(247355, 254527, $maxaitype, "Kyr'Ozch Submachine Gun - Type 4") . "\n\n"  . "<highlight>$maxaitype is the highest QL weapon this type will combine into.<end>";
			$name = "Kyr'Ozch Bio-Material - Type 4";
			break;
		case 247714:
			$high_id = 247690;
			$low_id = 247689;
			$use = "<highlight>Adds Fling Shot/Burst to:<end>\n<tab>" . Text::make_item(247334, 254499, $maxaitype, "Kyr'Ozch Carbine - Type 5") .
				"\n<tab>" . Text::make_item(247408, 254555, $maxaitype, "Kyr'Ozch Energy Carbine - Type 5") .
				"\n<tab>" . Text::make_item(247524, 254646, $maxaitype, "Kyr'Ozch Energy Pistol - Type 5") .
				"\n<tab>" . Text::make_item(247545, 254667, $maxaitype, "Kyr'Ozch Machine Pistol - Type 5") .
				"\n<tab>" . Text::make_item(247362, 254534, $maxaitype, "Kyr'Ozch Submachine Gun - Type 5") . "\n\n"  . "<highlight>$maxaitype is the highest QL weapon this type will combine into.<end>";
			$name = "Kyr'Ozch Bio-Material - Type 5";
			break;
		case 247716:
			$high_id = 247692;
			$low_id = 247691;
			$use = "<highlight>Adds Burst/Full Auto to:<end>\n<tab>" . Text::make_item(247341, 254506, $maxaitype, "Kyr'Ozch Carbine - Type 12") .
				"\n<tab>" . Text::make_item(247369, 254541, $maxaitype, "Kyr'Ozch Submachine Gun - Type 12") . "\n\n"  . "<highlight>$maxaitype is the highest QL weapon this type will combine into.<end>";
			$name = "Kyr'Ozch Bio-Material - Type 12 ";
			break;
		case 247718:
			$high_id = 247694;
			$low_id = 247693;
			$use = "<highlight>Adds Fling Shot/Aimed Shot to:<end>\n<tab>" . Text::make_item(247478, 254604, $maxaitype, "Kyr'Ozch Crossbow - Type 3") .
				"\n<tab>" . Text::make_item(247423, 254526, $maxaitype, "Kyr'Ozch Energy Carbine - Type 3") .
				"\n<tab>" . Text::make_item(247320, 254485, $maxaitype, "Kyr'Ozch Rifle - Type 3") . "\n\n"  . "<highlight>$maxaitype is the highest QL weapon this type will combine into.<end>";
			$name = "Kyr'Ozch Bio-Material - Type 3 ";
			break;
		case 247720:
			$high_id = 247696;
			$low_id = 247695;
			$use = "<highlight>Adds Burst/Fling Shot/Full Auto to:<end>\n<tab>" . Text::make_item(254507, 254513, $maxaitype, "Kyr'Ozch Carbine - Type 13") . "\n\n"  . "<highlight>$maxaitype is the highest QL weapon this type will combine into.<end>";
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
			$use = "<highlight>Adds Brawl/Dimach to:<end>\n<tab>" . Text::make_item(288665, 288671, $maxaitype, "Kyr'Ozch Nunchacko - Type 48") . "\n\n"  . "<highlight>$maxaitype is the highest QL weapon this type will combine into.<end>";
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