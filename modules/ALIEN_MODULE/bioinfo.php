<?php

if (preg_match("/^bioinfo (1|2|3|4|5|12|13|48|76|112|240|880|992|pristine|mutated|serum)$/i", $message, $arr) || preg_match("/^bioinfo (1|2|3|4|5|12|13|48|76|112|240|880|992|pristine|mutated|serum) (\\d+)$/i", $message, $arr)) {
	$bio = $arr[1];
	if ($arr[2]) {
		$ql = $arr[2];
	} else {
		$ql = 300;
	}
	
	// Ensures that the maximum AI weapon that combines into, doesn't go over QL 300 when the user presents a QL 271+ bio-material
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
			$high_id = 247684;
			$low_id = 247683;
			$ts_notify = 1;
			if ($maxaitype >= 1 && $maxaitype <= 99) {
				$wep_lowid_g = 254570;
				$wep_highid_g = 254571;
				$wep_lowid_p = 254619;
				$wep_highid_p = 254620;
				$wep_lowid_s = 254584;
				$wep_highid_s = 254585;
			} else if ($maxaitype >= 100 && $maxaitype <= 199) {
				$wep_lowid_g = 254572;
				$wep_highid_g = 254573;
				$wep_lowid_p = 254621;
				$wep_highid_p = 254622;
				$wep_lowid_s = 254586;
				$wep_highid_s = 254587;
			} else if ($maxaitype >= 200 && $maxaitype <= 299) {
				$wep_lowid_g = 254574;
				$wep_highid_g = 254575;
				$wep_lowid_p = 254623;
				$wep_highid_p = 254624;
				$wep_lowid_s = 254588;
				$wep_highid_s = 254589;
			} else {
				$wep_lowid_g = 254570;
				$wep_highid_g = 254576;
				$wep_lowid_p = 254619;
				$wep_highid_p = 254625;
				$wep_lowid_s = 254584;
				$wep_highid_s = 254590;
			}
			$use = "<highlight>Adds Fling shot to:<end>\n" . Text::make_item($wep_lowid_g, $wep_highid_g, $maxaitype, "Kyr'Ozch Grenade Gun - Type 1") .
				"\n" . Text::make_item($wep_lowid_p, $wep_highid_p, $maxaitype, "Kyr'Ozch Pistol - Type 1") .
				"\n" . Text::make_item($wep_lowid_s, $wep_highid_s, $maxaitype, "Kyr'Ozch Shotgun - Type 1");
			$name = "Kyr'Ozch Bio-Material - Type 1";
			break;
		case "2":
			$high_id = 247686;
			$low_id = 247685;
			$ts_notify = 1;
			if ($maxaitype >= 1 && $maxaitype <= 99) {
				$wep_lowid_c = 254605;
				$wep_highid_c = 254606;
				$wep_lowid_r = 254472;
				$wep_highid_r = 254473;
			} else if ($maxaitype >= 100 && $maxaitype <= 199) {
				$wep_lowid_c = 254607;
				$wep_highid_c = 254608;
				$wep_lowid_r = 254474;
				$wep_highid_r = 254475;
			} else if ($maxaitype >= 200 && $maxaitype <= 299) {
				$wep_lowid_c = 254609;
				$wep_highid_c = 254610;
				$wep_lowid_r = 254476;
				$wep_highid_r = 254477;
			} else {
				$wep_lowid_c = 254605;
				$wep_highid_c = 254611;
				$wep_lowid_r = 254472;
				$wep_highid_r = 254478;
			}
			$use = "<highlight>Adds Aimed Shot to:<end>\n" . Text::make_item($wep_lowid_c, $wep_highid_c, $maxaitype, "Kyr'Ozch Crossbow - Type 2") .
				"\n" . Text::make_item($wep_lowid_r, $wep_highid_r, $maxaitype, "Kyr'Ozch Rifle - Type 2");
			$name = "Kyr'Ozch Bio-Material - Type 2";
			break;
		case "3":
			$high_id = 247694;
			$low_id = 247693;
			$ts_notify = 1;
			if ($maxaitype >= 1 && $maxaitype <= 99) {
				$wep_lowid_c = 254598;
				$wep_highid_c = 254599;
				$wep_lowid_e = 254556;
				$wep_highid_e = 254557;
				$wep_lowid_r = 254479;
				$wep_highid_r = 254480;
			} else if ($maxaitype >= 100 && $maxaitype <= 199) {
				$wep_lowid_c = 254600;
				$wep_highid_c = 254601;
				$wep_lowid_e = 254558;
				$wep_highid_e = 254559;
				$wep_lowid_r = 254481;
				$wep_highid_r = 254482;
			} else if ($maxaitype >= 200 && $maxaitype <= 299) {
				$wep_lowid_c = 254602;
				$wep_highid_c = 254603;
				$wep_lowid_e = 254560;
				$wep_highid_e = 254561;
				$wep_lowid_r = 254483;
				$wep_highid_r = 254484;
			} else {
				$wep_lowid_c = 254598;
				$wep_highid_c = 254604;
				$wep_lowid_e = 254556;
				$wep_highid_e = 254562;
				$wep_lowid_r = 254479;
				$wep_highid_r = 254485;
			}
			$use = "<highlight>Adds Fling Shot/Aimed Shot to:<end>\n" . Text::make_item($wep_lowid_c, $wep_highid_c, $maxaitype, "Kyr'Ozch Crossbow - Type 3") .
				"\n" . Text::make_item($wep_lowid_e, $wep_highid_e, $maxaitype, "Kyr'Ozch Energy Carbine - Type 3") .
				"\n" . Text::make_item($wep_lowid_r, $wep_highid_r, $maxaitype, "Kyr'Ozch Rifle - Type 3");
			$name = "Kyr'Ozch Bio-Material - Type 3 ";
			break;
		case "4":
			$high_id = 247688;
			$low_id = 247687;
			$ts_notify = 1;
			if ($maxaitype >= 1 && $maxaitype <= 99) {
				$wep_lowid_m = 254654;
				$wep_highid_m = 254655;
				$wep_lowid_p = 254626;
				$wep_highid_p = 254627;
				$wep_lowid_s = 254521;
				$wep_highid_s = 254522;
			} else if ($maxaitype >= 100 && $maxaitype <= 199) {
				$wep_lowid_m = 254656;
				$wep_highid_m = 254657;
				$wep_lowid_p = 254628;
				$wep_highid_p = 254629;
				$wep_lowid_s = 254523;
				$wep_highid_s = 254524;
			} else if ($maxaitype >= 200 && $maxaitype <= 299) {
				$wep_lowid_m = 254658;
				$wep_highid_m = 254659;
				$wep_lowid_p = 254630;
				$wep_highid_p = 254631;
				$wep_lowid_s = 254525;
				$wep_highid_s = 254526;
			} else {
				$wep_lowid_m = 254654;
				$wep_highid_m = 254660;
				$wep_lowid_p = 254626;
				$wep_highid_p = 254632;
				$wep_lowid_s = 254521;
				$wep_highid_s = 254527;
			}
			$use = "<highlight>Adds Burst to:<end>\n" . Text::make_item($wep_lowid_m, $wep_highid_m, $maxaitype, "Kyr'Ozch Machine Pistol - Type 4") .
			"\n" . Text::make_item($wep_lowid_p, $wep_highid_p, $maxaitype, "Kyr'Ozch Pistol - Type 4") .
			"\n" . Text::make_item($wep_lowid_s, $wep_highid_s, $maxaitype, "Kyr'Ozch Submachine Gun - Type 4");
			$name = "Kyr'Ozch Bio-Material - Type 4";
			break;
		case "5":
			$high_id = 247690;
			$low_id = 247689;
			$ts_notify = 1;
			if ($maxaitype >= 1 && $maxaitype <= 99) {
				$wep_lowid_c = 254493;
				$wep_highid_c = 254494;
				$wep_lowid_ec = 254549;
				$wep_highid_ec = 254550;
				$wep_lowid_ep = 254640;
				$wep_highid_ep = 254641;
				$wep_lowid_m = 254661;
				$wep_highid_m = 254662;
				$wep_lowid_s = 254528;
				$wep_highid_s = 254529;
			} else if ($maxaitype >= 100 && $maxaitype <= 199) {
				$wep_lowid_c = 254495;
				$wep_highid_c = 254496;
				$wep_lowid_ec = 254551;
				$wep_highid_ec = 254552;
				$wep_lowid_ep = 254642;
				$wep_highid_ep = 254643;
				$wep_lowid_m = 254663;
				$wep_highid_m = 254664;
				$wep_lowid_s = 254530;
				$wep_highid_s = 254531;
			} else if ($maxaitype >= 200 && $maxaitype <= 299) {
				$wep_lowid_c = 254497;
				$wep_highid_c = 254498;
				$wep_lowid_ec = 254553;
				$wep_highid_ec = 254554;
				$wep_lowid_ep = 254644;
				$wep_highid_ep = 254645;
				$wep_lowid_m = 254665;
				$wep_highid_m = 254666;
				$wep_lowid_s = 254532;
				$wep_highid_s = 254533;
			} else {
				$wep_lowid_c = 254493;
				$wep_highid_c = 254499;
				$wep_lowid_ec = 254549;
				$wep_highid_ec = 254555;
				$wep_lowid_ep = 254640;
				$wep_highid_ep = 254646;
				$wep_lowid_m = 254661;
				$wep_highid_m = 254667;
				$wep_lowid_s = 254528;
				$wep_highid_s = 254534;
			}
			$use = "<highlight>Adds Fling Shot/Burst to:<end>\n" . Text::make_item($wep_lowid_c, $wep_highid_c, $maxaitype, "Kyr'Ozch Carbine - Type 5") .
				"\n" . Text::make_item($wep_lowid_ec, $wep_highid_ec, $maxaitype, "Kyr'Ozch Energy Carbine - Type 5") .
				"\n" . Text::make_item($wep_lowid_ep, $wep_highid_ep, $maxaitype, "Kyr'Ozch Energy Pistol - Type 5") .
				"\n" . Text::make_item($wep_lowid_m, $wep_highid_m, $maxaitype, "Kyr'Ozch Machine Pistol - Type 5") .
				"\n" . Text::make_item($wep_lowid_s, $wep_highid_s, $maxaitype, "Kyr'Ozch Submachine Gun - Type 5");
			$name = "Kyr'Ozch Bio-Material - Type 5";
			break;
		case "12":
			$high_id = 247692;
			$low_id = 247691;
			$ts_notify = 1;
			if ($maxaitype >= 1 && $maxaitype <= 99) {
				$wep_lowid_c = 254500;
				$wep_highid_c = 254501;
				$wep_lowid_s = 254535;
				$wep_highid_s = 254536;
			} else if ($maxaitype >= 100 && $maxaitype <= 199) {
				$wep_lowid_c = 254502;
				$wep_highid_c = 254503;
				$wep_lowid_s = 254537;
				$wep_highid_s = 254538;
			} else if ($maxaitype >= 200 && $maxaitype <= 299) {
				$wep_lowid_c = 254504;
				$wep_highid_c = 254505;
				$wep_lowid_s = 254539;
				$wep_highid_s = 254540;
			} else {
				$wep_lowid_c = 254500;
				$wep_highid_c = 254506;
				$wep_lowid_s = 254535;
				$wep_highid_s = 254541;
			}
			$use = "<highlight>Adds Burst/Full Auto to:<end>\n" . Text::make_item($wep_lowid_c, $wep_highid_c, $maxaitype, "Kyr'Ozch Carbine - Type 12") .
				"\n" . Text::make_item($wep_lowid_s, $wep_highid_s, $maxaitype, "Kyr'Ozch Submachine Gun - Type 12");
			$name = "Kyr'Ozch Bio-Material - Type 12 ";
			break;
		case "13":
			$high_id = 247696;
			$low_id = 247695;
			$ts_notify = 1;
			if ($maxaitype >= 1 && $maxaitype <= 99) {
				$wep_lowid_c = 254507;
				$wep_highid_c = 254508;
			} else if ($maxaitype >= 100 && $maxaitype <= 199) {
				$wep_lowid_c = 254509;
				$wep_highid_c = 254510;
			} else if ($maxaitype >= 200 && $maxaitype <= 299) {
				$wep_lowid_c = 254511;
				$wep_highid_c = 254512;
			} else {
				$wep_lowid_c = 254507;
				$wep_highid_c = 254513;
			}
			$use = "<highlight>Adds Burst/Fling Shot/Full Auto to:<end>\n" . Text::make_item($wep_lowid_c, $wep_highid_c, $maxaitype, "Kyr'Ozch Carbine - Type 13");
			$name = "Kyr'Ozch Bio-Material - Type 13";
			break;
		case "48":
			$high_id = 288673;
			$low_id = 288672;
			$ts_notify = 1;
			if ($maxaitype >= 1 && $maxaitype <= 99) {
				$wep_lowid_n = 288665;
				$wep_highid_n = 288666;
			} else if ($maxaitype >= 100 && $maxaitype <= 199) {
				$wep_lowid_n = 288667;
				$wep_highid_n = 288668;
			} else if ($maxaitype >= 200 && $maxaitype <= 299) {
				$wep_lowid_n = 288669;
				$wep_highid_n = 288670;
			} else {
				$wep_lowid_n = 288665;
				$wep_highid_n = 288671;
			}
			$use = "<highlight>Adds Brawl/Dimach to:<end>\n" . Text::make_item($wep_lowid_n, $wep_highid_n, $maxaitype, "Kyr'Ozch Nunchacko - Type 48");
			$name = "Kyr'Ozch Bio-Material - Type 48";
			break;
		case "76":
			$high_id = 247674;
			$low_id = 247673;
			// This activates a notification that it's a weapon, so the tradeskill process can be done in one go rather then each individual case.
			$ts_notify = 1;
			// Each weapon has several low/high item ID's, to get correct value, had to do IFs for each QL range. Uses each variable to determine the weapons low/high ID.
			if ($maxaitype >= 1 && $maxaitype <= 99) {
				$wep_lowid_e = 254745;
				$wep_highid_e = 254746;
				$wep_lowid_s = 254731;
				$wep_highid_s = 254732;
			} else if ($maxaitype >= 100 && $maxaitype <= 199) {
				$wep_lowid_e = 254747;
				$wep_highid_e = 254748;
				$wep_lowid_s = 254733;
				$wep_highid_s = 254734;
			} else if ($maxaitype >= 200 && $maxaitype <= 299) {
				$wep_lowid_e = 254749;
				$wep_highid_e = 254750;
				$wep_lowid_s = 254735;
				$wep_highid_s = 254736;
			} else {
				$wep_lowid_e = 254745;
				$wep_highid_e = 254751;
				$wep_lowid_s = 254731;
				$wep_highid_s = 254737;
			}
			$use = "<highlight>Adds Brawl/Fast Attack to:<end>\n" . Text::make_item($wep_lowid_e, $wep_highid_e, $maxaitype, "Kyr'Ozch Energy Sword - Type 76") .
				"\n" . Text::make_item($wep_lowid_s, $wep_highid_s, $maxaitype, "Kyr'Ozch Sledgehammer - Type 76");
			$name = "Kyr'Ozch Bio-Material - Type 76";
			break;
		case "112":
			$high_id = 247676;
			$low_id = 247675;
			$ts_notify = 1;
			if ($maxaitype >= 1 && $maxaitype <= 99) {
				$wep_lowid_e = 254703;
				$wep_highid_e = 254704;
				$wep_lowid_h = 254675;
				$wep_highid_h = 254676;
				$wep_lowid_sp = 254780;
				$wep_highid_sp = 254781;
				$wep_lowid_sw = 254759;
				$wep_highid_sw = 254760;
			} else if ($maxaitype >= 100 && $maxaitype <= 199) {
				$wep_lowid_e = 254705;
				$wep_highid_e = 254706;
				$wep_lowid_h = 254677;
				$wep_highid_h = 254678;
				$wep_lowid_sp = 254782;
				$wep_highid_sp = 254783;
				$wep_lowid_sw = 254761;
				$wep_highid_sw = 254762;
			} else if ($maxaitype >= 200 && $maxaitype <= 299) {
				$wep_lowid_e = 254707;
				$wep_highid_e = 254708;
				$wep_lowid_h = 254679;
				$wep_highid_h = 254680;
				$wep_lowid_sp = 254784;
				$wep_highid_sp = 254785;
				$wep_lowid_sw = 254763;
				$wep_highid_sw = 254764;
			} else {
				$wep_lowid_e = 254703;
				$wep_highid_e = 254709;
				$wep_lowid_h = 254675;
				$wep_highid_h = 254681;
				$wep_lowid_sp = 254780;
				$wep_highid_sp = 254786;
				$wep_lowid_sw = 254759;
				$wep_highid_sw = 254765;
			}
			$use = "<highlight>Adds Brawl/Dimach/Fast Attack to:<end>\n" . Text::make_item($wep_lowid_e, $wep_highid_e, $maxaitype, "Kyr'Ozch Energy Hammer - Type 112") .
				"\n" . Text::make_item($wep_lowid_h, $wep_highid_h, $maxaitype, "Kyr'Ozch Hammer - Type 112") .
				"\n" . Text::make_item($wep_lowid_sp, $wep_highid_sp, $maxaitype, "Kyr'Ozch Spear - Type 112") .
				"\n" . Text::make_item($wep_lowid_sw, $wep_highid_sw, $maxaitype, "Kyr'Ozch Sword - Type 112");
			$name = "Kyr'Ozch Bio-Material - Type 112";
			break;
		case "240":
			$high_id = 247678;
			$low_id = 247677;
			$ts_notify = 1;
			if ($maxaitype >= 1 && $maxaitype <= 99) {
				$wep_lowid_a = 254689;
				$wep_highid_a = 254690;
			} else if ($maxaitype >= 100 && $maxaitype <= 199) {
				$wep_lowid_a = 254691;
				$wep_highid_a = 254692;
			} else if ($maxaitype >= 200 && $maxaitype <= 299) {
				$wep_lowid_a = 254693;
				$wep_highid_a = 254694;
			} else {
				$wep_lowid_a = 254689;
				$wep_highid_a = 254695;
			}
			$use = "<highlight>Adds Brawl/Dimach/Fast Attack/Sneak Attack to:<end>\n" . Text::make_item($wep_lowid_a, $wep_highid_a, $maxaitype, "Kyr'Ozch Axe - Type 240");
			$name = "Kyr'Ozch Bio-Material - Type 240";
			break;
		case "880":
			$high_id = 247680;
			$low_id = 247679;
			$ts_notify = 1;
			if ($maxaitype >= 1 && $maxaitype <= 99) {
				$wep_lowid_s = 254766;
				$wep_highid_s = 254767;
			} else if ($maxaitype >= 100 && $maxaitype <= 199) {
				$wep_lowid_s = 254768;
				$wep_highid_s = 254769;
			} else if ($maxaitype >= 200 && $maxaitype <= 299) {
				$wep_lowid_s = 254770;
				$wep_highid_s = 254771;
			} else {
				$wep_lowid_s = 254766;
				$wep_highid_s = 254772;
			}
			$use = "<highlight>Adds Dimach/Fast Attack/Parry/Riposte to:<end>\n" . Text::make_item($wep_lowid_s, $wep_highid_s, $maxaitype, "Kyr'Ozch Sword - Type 880");
			$name = "Kyr'Ozch Bio-Material - Type 880";
			break;
		case "992":
			$high_id = 247682;
			$low_id = 247681;
			$ts_notify = 1;
			if ($maxaitype >= 1 && $maxaitype <= 99) {
				$wep_lowid_e = 254717;
				$wep_highid_e = 254718;
			} else if ($maxaitype >= 100 && $maxaitype <= 199) {
				$wep_lowid_e = 254719;
				$wep_highid_e = 254720;
			} else if ($maxaitype >= 200 && $maxaitype <= 299) {
				$wep_lowid_e = 254721;
				$wep_highid_e = 254722;
			} else {
				$wep_lowid_e = 254717;
				$wep_highid_e = 254723;
			}
			$use = "<highlight>Adds Dimach/Fast Attack/Sneak Attack/Parry/Riposte to:<end>\n" . Text::make_item($wep_lowid_e, $wep_highid_e, $maxaitype, "Kyr'Ozch Energy Rapier - Type 992");
			$name = "Kyr'Ozch Bio-Material - Type 992";
			break;
		case "pristine":
			// This activates it as an armor type so it will display the additional armor information without putting the information in twice for mutataed and pristine.
			$ts_notify = 2;
			$high_id = 247107;
			$low_id = 247106;
			$use = "Used to build Alien Armor (<highlight>less tradeskill requirements then mutated.<end>)\n\n<highlight>The following tradeskill amounts are required to make<end> QL $ql<highlight>\nstrong/arithmetic/enduring/spiritual/supple/observant armor:<end>\n\nComputer Literacy - <highlight>$armor_cl<end> (<highlight>4.5 * QL<end>)\nChemistry - <highlight>$armor_chem_min<end> (<highlight>4.5 * QL<end>)\nNano Programming - <highlight>$armor_np<end> (<highlight>6 * QL<end>)\nPharma Tech - <highlight>$armor_pharma<end> (<highlight>6 * QL<end>)\nPsychology - <highlight>$armor_psyco<end> (<highlight>6 * QL<end>)\n\nNote:<highlight> Tradeskill requirements are based off the lowest QL items needed throughout the entire process.<end>";
			$name = "Pristine Kyr'Ozch Bio-Material";
			break;
		case "mutated":
			$ts_notify = 2;
			$high_id = 247109;
			$low_id = 247108;
			$use = "Used to build Alien Armor\n\n<highlight>The following tradeskill amounts are required to make<end> QL $ql<highlight>\nstrong/arithmetic/enduring/spiritual/supple/observant armor:<end>\n\nComputer Literacy - <highlight>$armor_cl<end> (<highlight>4.5 * QL<end>)\nChemistry - <highlight>$armor_chem_max<end> (<highlight>7 * QL<end>)\nNano Programming - <highlight>$armor_np<end> (<highlight>6 * QL<end>)\nPharma Tech - <highlight>$armor_pharma<end> (<highlight>6 * QL<end>)\nPsychology - <highlight>$armor_psyco<end> (<highlight>6 * QL<end>)\n\nNote:<highlight> Tradeskill requirements are based off the lowest QL items needed throughout the entire process.<end>";
			$name = "Mutated Kyr'Ozch Bio-Material";
			break;
		case "serum":
			$high_id = 254805;
			$low_id = 247765;
			$pharma_ts = floor($ql * 3.5);
			$chem_me_ts = floor($ql * 4);
			$ee_ts = floor($ql * 4.5);
			$cl_ts = floor($ql * 5);
			$use = "<highlight>Used to build city buildings<end>\n\n<highlight>The following are the required skills throughout the process of making a building:<end>\n\nQuantum FT - <highlight>400<end> (<highlight>Static<end>)\nPharma Tech - ";
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
			$use .= " (<highlight>4 * QL<end>) 400 is minimum requirement\nMechanical Engineering - <highlight>$chem_me_ts<end> (<highlight>4 * QL<end>)\nElectrical Engineering - <highlight>$ee_ts<end> (<highlight>4.5 * QL<end>)\nComp Liter - <highlight>$cl_ts<end> (<highlight>5 * QL<end>)";
			$name = "Kyr'Ozch Viral Serum";
			break;
		default:
			$chatBot->send("Unknown Bio-Material", $sendto);
			return;
	}

	// Weapons and armor are ticked with a $ts_notify so the additional text only goes to them, without repeating the text for each individual weapon or armor.
	if ($ts_notify == 1) {
		$ts_wep = floor($maxaitype * 6);
		$use .= "\n\n<highlight>QL $maxaitype<end> is the highest weapon this type will combine into.";
		if ($maxaitype != 300) {
			$use .= "\nNote: <highlight>The weapon can bump several QL's.<end>";
		}
		$use .= "\n\nIt will take <highlight>$ts_wep<end> ME & WS (<highlight>6 * QL<end>) to combine with a QL $maxaitype Kyr'ozch Weapon.";
	} else if ($ts_notify == 2) {
		// All the min/max QL and tradeskill calcs for the mutated/pristine process
		if ($ql >= 1 && $ql <= 240) {
			$max_ql = floor($ql / 0.8);
		} else {
			$max_ql = 300;
		}
		$use .= "\n\nFor Supple, Arithmetic, or Enduring:\n\n<highlight>When completed, the armor piece can have as low as<end> QL $min_ql <highlight>combine into it, depending on available tradeskill options.\n\nDoes not change QL's, therefore takes<end> $armor_psyco <highlight>Psychology for available combinations.<end>\n\nFor Spiritual, Strong, or Observant:\n\n<highlight>When completed, the armor piece can combine upto<end> QL $max_ql<highlight>, depending on available tradeskill options.\n\nChanges QL depending on targets QL. The max combination (<end>$max_ql<highlight>) (<end>$armor_psyco required for this combination<highlight>)<end>";
	}
	
	$ts_bio = floor($ql * 4.5);
	
	$blob = "<header> :::::: $name (QL $ql) :::::: <end>\n\n";
	$blob .= Text::make_item($low_id, $high_id, $ql, $name) . "\nIt will take <highlight>$ts_bio<end> EE & CL (<highlight>4.5 * QL<end>) to analyze the Bio-Material." . "\n\n";
	$blob .= $use;
	$bio_info = "$arr2[0]";
	$fixed_bio_info =  str_replace("\'", "&#39;", "$bio_info");
	$bio_link = "<a href='chatcmd:///tell <myname> <symbol>bio $fixed_bio_info'>$name (QL $ql)</a>";

	$msg = Text::make_link("$name (ql $ql)", $blob);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>