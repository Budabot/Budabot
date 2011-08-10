<?php

/*
   ** Author: Dare2005 (RK2), based on code for dbloot module by Chachy (RK2)
   ** Description: Albtraum Loot Module
   ** Version: 0.5
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 18.02.2011
   ** Date(last modified): 18.02.2011
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

if (!function_exists('get_alb_loot')) {
	function get_alb_loot($raid, $category) {
		$blob = Raid::find_raid_loot($raid, $category);
		$blob .= "\n\nAlbtraum Loot By Dare2005 (RK2)";
		return Text::make_blob("$raid $category Loot", $blob);
	}
}

if (preg_match("/^alb$/i", $message)) {
	$chatBot->send(get_alb_loot('Albtraum', 'Crystals & Crystalised Memories'), $sendto);
	$chatBot->send(get_alb_loot('Albtraum', 'Ancients'), $sendto);
	$chatBot->send(get_alb_loot('Albtraum', 'Samples'), $sendto);
	$chatBot->send(get_alb_loot('Albtraum', 'Rings and Preservation Units'), $sendto);
	$chatBot->send(get_alb_loot('Albtraum', 'Pocket Boss Crystals'), $sendto);
} else {
	$syntax_error = true;
}
  
?>