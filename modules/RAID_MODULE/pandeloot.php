<?php

/*
   ** Author: Marinerecon (RK2)
   ** Description: Removes an item from the roll
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
	$list = "<header>::::: Pandemonium Loot :::::<end>\n\n\n";
	$list .= "The Beast\n";
	$newlink = Text::make_link("Beast Armor\n", "/tell <myname> <symbol>beastarmor", "chatcmd");
	$list .= "<tab>".$newlink;
	$newlink = Text::make_link("Beast Weapons\n", "/tell <myname> <symbol>beastweaps", "chatcmd");
	$list .= "<tab>".$newlink;
	$newlink = Text::make_link("Beast Stars\n", "/tell <myname> <symbol>beaststars", "chatcmd");
	$list .= "<tab>".$newlink;
	$list .= "\nThe Night Heart\n";
	$newlink = Text::make_link("TNH\n", "/tell <myname> <symbol>tnh", "chatcmd");
	$list .= "<tab>".$newlink;
	$list .= "\nWest Zodiacs\n";
	$newlink = Text::make_link("Aries\n", "/tell <myname> <symbol>aries", "chatcmd");
	$list .= "<tab>".$newlink;
	$newlink = Text::make_link("Leo\n", "/tell <myname> <symbol>leo", "chatcmd");
	$list .= "<tab>".$newlink;
	$newlink = Text::make_link("Virgo\n", "/tell <myname> <symbol>virgo", "chatcmd");
	$list .= "<tab>".$newlink;
	$list .= "\nEast Zodiacs\n";
	$newlink = Text::make_link("Aquarius\n", "/tell <myname> <symbol>aquarius", "chatcmd");
	$list .= "<tab>".$newlink;
	$newlink = Text::make_link("Cancer\n", "/tell <myname> <symbol>cancer", "chatcmd");
	$list .= "<tab>".$newlink;
	$newlink = Text::make_link("Gemini\n", "/tell <myname> <symbol>gemini", "chatcmd");
	$list .= "<tab>".$newlink;
	$list .= "\nMiddle Zodiacs\n";
	$newlink = Text::make_link("Libra\n", "/tell <myname> <symbol>libra", "chatcmd");
	$list .= "<tab>".$newlink;
	$newlink = Text::make_link("Pisces\n", "/tell <myname> <symbol>pisces", "chatcmd");
	$list .= "<tab>".$newlink;
	$newlink = Text::make_link("Taurus\n", "/tell <myname> <symbol>taurus", "chatcmd");
	$list .= "<tab>".$newlink;
	$list .= "\nNorth Zodiacs\n";
	$newlink = Text::make_link("Capricorn\n", "/tell <myname> <symbol>capricorn", "chatcmd");
	$list .= "<tab>".$newlink;
	$newlink = Text::make_link("Sagittarius\n", "/tell <myname> <symbol>sagittarius", "chatcmd");
	$list .= "<tab>".$newlink;
	$newlink = Text::make_link("Scorpio\n", "/tell <myname> <symbol>scorpio", "chatcmd");
	$list .= "<tab>".$newlink;
	$list .= "\nOther\n";
	$newlink = Text::make_link("Shadowbreeds\n", "/tell <myname> <symbol>sb", "chatcmd");
	$list .= "<tab>".$newlink;

	$list .= "\n\nPande Loot By Marinerecon (RK2)";

	$msg = Text::make_blob("Pandemonium Loot", $list);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^beastarmor$/i", $message)){
	$chatBot->send(get_pande_loot('Pande', 'Beast Armor'), $sendto);
} else if (preg_match("/^beastweaps$/i", $message)){
	$chatBot->send(get_pande_loot('Pande', 'Beast Weapons'), $sendto);
} else if (preg_match("/^beaststars$/i", $message)){
	$chatBot->send(get_pande_loot('Pande', 'Stars'), $sendto);
} else if (preg_match("/^sb$/i", $message)){
	$chatBot->send(get_pande_loot('Pande', 'Shadowbreeds'), $sendto);
} else if (preg_match("/^tnh$/i", $message)){
	$chatBot->send(get_pande_loot('Pande', 'The Night Heart'), $sendto);
} else if (preg_match("/^aries$/i", $message)){
	$chatBot->send(get_pande_loot('Pande', 'Aries'), $sendto);
} else if (preg_match("/^leo$/i", $message)){
	$chatBot->send(get_pande_loot('Pande', 'Leo'), $sendto);
} else if (preg_match("/^virgo$/i", $message)){
	$chatBot->send(get_pande_loot('Pande', 'Virgo'), $sendto);
} else if (preg_match("/^aquarius$/i", $message)){
	$chatBot->send(get_pande_loot('Pande', 'Aquarius'), $sendto);
} else if (preg_match("/^cancer$/i", $message)){
	$chatBot->send(get_pande_loot('Pande', 'Cancer'), $sendto);
} else if (preg_match("/^gemini$/i", $message)){
	$chatBot->send(get_pande_loot('Pande', 'Gemini'), $sendto);
} else if (preg_match("/^libra$/i", $message)){
	$chatBot->send(get_pande_loot('Pande', 'Libra'), $sendto);
} else if (preg_match("/^pisces$/i", $message)){
	$chatBot->send(get_pande_loot('Pande', 'Pisces'), $sendto);
} else if (preg_match("/^taurus$/i", $message)){
	$chatBot->send(get_pande_loot('Pande', 'Taurus'), $sendto);
} else if (preg_match("/^capricorn$/i", $message)){
	$chatBot->send(get_pande_loot('Pande', 'Capricorn'), $sendto);
} else if (preg_match("/^sagittarius$/i", $message)){
	$chatBot->send(get_pande_loot('Pande', 'Sagittarius'), $sendto);
} else if (preg_match("/^scorpio$/i", $message)){
	$chatBot->send(get_pande_loot('Pande', 'Scorpio'), $sendto);
} else {
	$syntax_error = true;
}

?>