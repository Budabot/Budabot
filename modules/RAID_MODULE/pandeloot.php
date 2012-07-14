<?php

/*
   ** Author: Marinerecon (RK2)
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 08.01.2009
   ** Date(last modified): 08.02.2009
   **
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

if (!function_exists('get_pande_loot')) {
	function get_pande_loot($raid, $category) {
		$blob = Raid::find_raid_loot($raid, $category);
		$blob .= "\n\nPande Loot By Marinerecon (RK2)";
		return Text::make_blob("$raid $category Loot", $blob);
	}
}

if (preg_match("/^pande$/i", $message)){
	$list = "\n";
	$list .= "The Beast\n";
	$list .= "<tab>".Text::make_chatcmd("Beast Armor\n", "/tell <myname> <symbol>beastarmor");
	$list .= "<tab>".Text::make_chatcmd("Beast Weapons\n", "/tell <myname> <symbol>beastweaps");
	$list .= "<tab>".Text::make_chatcmd("Beast Stars\n", "/tell <myname> <symbol>beaststars");
	$list .= "\nThe Night Heart\n";
	$list .= "<tab>".Text::make_chatcmd("TNH\n", "/tell <myname> <symbol>tnh");
	$list .= "\nWest Zodiacs\n";
	$list .= "<tab>".Text::make_chatcmd("Aries\n", "/tell <myname> <symbol>aries");
	$list .= "<tab>".Text::make_chatcmd("Leo\n", "/tell <myname> <symbol>leo");
	$list .= "<tab>".Text::make_chatcmd("Virgo\n", "/tell <myname> <symbol>virgo");
	$list .= "\nEast Zodiacs\n";
	$list .= "<tab>".Text::make_chatcmd("Aquarius\n", "/tell <myname> <symbol>aquarius");
	$list .= "<tab>".Text::make_chatcmd("Cancer\n", "/tell <myname> <symbol>cancer");
	$list .= "<tab>".Text::make_chatcmd("Gemini\n", "/tell <myname> <symbol>gemini");
	$list .= "\nMiddle Zodiacs\n";
	$list .= "<tab>".Text::make_chatcmd("Libra\n", "/tell <myname> <symbol>libra");
	$list .= "<tab>".Text::make_chatcmd("Pisces\n", "/tell <myname> <symbol>pisces");
	$list .= "<tab>".Text::make_chatcmd("Taurus\n", "/tell <myname> <symbol>taurus");
	$list .= "\nNorth Zodiacs\n";
	$list .= "<tab>".Text::make_chatcmd("Capricorn\n", "/tell <myname> <symbol>capricorn");
	$list .= "<tab>".Text::make_chatcmd("Sagittarius\n", "/tell <myname> <symbol>sagittarius");
	$list .= "<tab>".Text::make_chatcmd("Scorpio\n", "/tell <myname> <symbol>scorpio");
	$list .= "\nOther\n";
	$list .= "<tab>".Text::make_chatcmd("Shadowbreeds\n", "/tell <myname> <symbol>sb");
	$list .= "<tab>".Text::make_chatcmd("Bastion\n", "/tell <myname> <symbol>bastion");

	$list .= "\n\nPandemonium Loot By Marinerecon (RK2)";

	$msg = Text::make_blob("Pandemonium Loot", $list);
	$sendto->reply($msg);
} else if (preg_match("/^beastarmor$/i", $message)){
	$sendto->reply(get_pande_loot('Pande', 'Beast Armor'));
} else if (preg_match("/^beastweaps$/i", $message)){
	$sendto->reply(get_pande_loot('Pande', 'Beast Weapons'));
} else if (preg_match("/^beaststars$/i", $message)){
	$sendto->reply(get_pande_loot('Pande', 'Stars'));
} else if (preg_match("/^sb$/i", $message)){
	$sendto->reply(get_pande_loot('Pande', 'Shadowbreeds'));
} else if (preg_match("/^tnh$/i", $message)){
	$sendto->reply(get_pande_loot('Pande', 'The Night Heart'));
} else if (preg_match("/^aries$/i", $message)){
	$sendto->reply(get_pande_loot('Pande', 'Aries'));
} else if (preg_match("/^leo$/i", $message)){
	$sendto->reply(get_pande_loot('Pande', 'Leo'));
} else if (preg_match("/^virgo$/i", $message)){
	$sendto->reply(get_pande_loot('Pande', 'Virgo'));
} else if (preg_match("/^aquarius$/i", $message)){
	$sendto->reply(get_pande_loot('Pande', 'Aquarius'));
} else if (preg_match("/^cancer$/i", $message)){
	$sendto->reply(get_pande_loot('Pande', 'Cancer'));
} else if (preg_match("/^gemini$/i", $message)){
	$sendto->reply(get_pande_loot('Pande', 'Gemini'));
} else if (preg_match("/^libra$/i", $message)){
	$sendto->reply(get_pande_loot('Pande', 'Libra'));
} else if (preg_match("/^pisces$/i", $message)){
	$sendto->reply(get_pande_loot('Pande', 'Pisces'));
} else if (preg_match("/^taurus$/i", $message)){
	$sendto->reply(get_pande_loot('Pande', 'Taurus'));
} else if (preg_match("/^capricorn$/i", $message)){
	$sendto->reply(get_pande_loot('Pande', 'Capricorn'));
} else if (preg_match("/^sagittarius$/i", $message)){
	$sendto->reply(get_pande_loot('Pande', 'Sagittarius'));
} else if (preg_match("/^scorpio$/i", $message)){
	$sendto->reply(get_pande_loot('Pande', 'Scorpio'));
} else if (preg_match("/^bastion$/i", $message)){
	$sendto->reply(get_pande_loot('Pande', 'Bastion'));
} else {
	$syntax_error = true;
}

?>
