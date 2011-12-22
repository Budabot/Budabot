<?php
   /*
   ** Author: Legendadv (RK2)
   ** Description: Shows research % needed to still get maximum xp/sk from a mission/high level mob
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 7/13/2009
   ** Date(last modified): 7/24/2009
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

if (preg_match("/^capxp ([0-9]+) ([0-9]+)$/i", $message, $arr)) {
	$reward = $arr[1];
	$level = $arr[2];
	
	if ($level > 219 || $level < 1) {
		$chatBot->send("Your level cannot be greater than 219 or less than 1.", $sendto);
		return;
	}
	
	$row = Level::get_level_info($level);

	if ($level < 200) {
		$xp = $row->xpsk;
		$research = (1-(($xp*.2)/$reward))*100;
	} else {
		$sk = $row->xpsk;
		$research = (1-(($sk*.2)/$reward))*100;
	}
	if ($research < 0) {
		$research = 0;
	}
	
	if ($level < 200) {
		$msg = "At lvl <highlight>".number_format($level)."<end> you need <highlight>".number_format($xp)."<end> xp to level. With a mission reward of <highlight>".number_format($reward)."<end> xp, set your research bar to <highlight>".ceil($research)."%<end> to receive maximum xp from this mission reward.";
	} else {
		$msg = "At lvl <highlight>".number_format($level)."<end> you need <highlight>".number_format($sk)."<end> sk to level. With a mission reward of <highlight>".number_format($reward)."<end> sk, set your research bar to <highlight>".ceil($research)."%<end> to receive maximum sk from this mission reward.";
	}
	
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
