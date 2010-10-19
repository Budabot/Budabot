<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Identifacation of Biomaterial
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 14.12.2005
   ** Date(last modified): 06.10.2005
   ** 
   ** Copyright (C) 2005 Carsten Lohmann
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

if (preg_match("/^bio <a href=\"itemref:\/\/([0-9]+)\/([0-9]+)\/([0-9]+)\">Solid Clump of Kyr\'Ozch Bio-Material<\/a>$/i", $message, $arr)){
    // Identify the biomaterial
    switch($arr[2]) {
        case 247103:
        	$high_id = 247107;
        	$low_id = 247106;
        	$use = "Used to build Alien Armor(lesser Req. for building as Mutated)";
            $name = "Pristine Kyr'Ozch Bio-Material";
            break;
        case 247105:
        	$high_id = 247109;
        	$low_id = 247108;
        	$use = "Used to build Alien Armor";
            $name = "Mutated Kyr'Ozch Bio-Material";
            break;
        case 247698:
        	$high_id = 247674;
        	$low_id = 247673;
        	$use = "Adds Specials to weapons(Brawl/Fast Attack)";
            $name = "Kyr'Ozch Bio-Material - Type 76";
            break;
        case 247700:
        	$high_id = 247676;
        	$low_id = 247675;
        	$use = "Adds Specials to weapons(Brawl/Dimach/Fast Attack)";
            $name = "Kyr'Ozch Bio-Material - Type 112";
            break;
        case 247702:
        	$high_id = 247678;
        	$low_id = 247677;
        	$use = "Adds Specials to weapons(Brawl/Dimach/Fast Attack/Sneak Attack)";
            $name = "Kyr'Ozch Bio-Material - Type 240";
            break;
        case 247704:
        	$high_id = 247680;
        	$low_id = 247679;
        	$use = "Adds Specials to weapons(Dimach/Fast Attack/Parry/Riposte)";
            $name = "Kyr'Ozch Bio-Material - Type 880";
            break;
        case 247706:
        	$highid = 247682;
        	$low_id = 247681;
        	$use = "Adds Specials to weapons(Dimach/Fast Attack/Sneak Attack/Parry/Riposte)";
            $name = "Kyr'Ozch Bio-Material - Type 992";
            break;
        case 247708:
        	$high_id = 247684;
        	$low_id = 247683;
        	$use = "Adds Specials to weapons(Fling shot)";
            $name = "Kyr'Ozch Bio-Material - Type 1";
            break;
        case 247710:
        	$high_id = 247686;
        	$low_id = 247685;
        	$use = "Adds Specials to weapons(Aimed Shot)";
            $name = "Kyr'Ozch Bio-Material - Type 2";
            break;
        case 247712:
        	$high_id = 247686;
        	$low_id = 247685;
        	$use = "Adds Specials to weapons(Burst)";
            $name = "Kyr'Ozch Bio-Material - Type 4";
            break;
        case 247714:
        	$high_id = 247690;
        	$low_id = 247689;
        	$use = "Adds Specials to weapons(Fling Shot/Burst)";
            $name = "Kyr'Ozch Bio-Material - Type 5";
            break;
        case 247716:
        	$high_id = 247692;
        	$low_id = 247691;
        	$use = "Adds Specials to weapons(Burst/Full Auto)";
            $name = "Kyr'Ozch Bio-Material - Type 12 ";
            break;
        case 247718:
        	$high_id = 247694;
        	$low_id = 247693;
        	$use = "Adds Specials to weapons(Fling Shot/Aimed Shot)";
            $name = "Kyr'Ozch Bio-Material - Type 3 ";
            break;
        case 247720:
        	$high_id = 247696;
        	$low_id = 247695;
        	$use = "Adds Specials to weapons(Burst/Fling Shot/Full Auto)";
            $name = "Kyr'Ozch Bio-Material - Type 13";
            break;
        case 254804:
        	$high_id = 254805;
        	$low_id = 247765;
        	$use = "Used to build city buildings";
            $name = "Kyr'Ozch Viral Serum";
            break;
        default:
			$use = "Unknown Bio-Material.";
			break;
	}

    //Create the output message
    $msg = bot::makeItem($low_id, $high_id, $arr[3], "QL ".$arr[3]." ".$name)." ".$use;

    // Send info back
    bot::send($msg, $sendto);
} else {
	$syntax_error = true;
}
?>