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

if (preg_match("/^(capxp|capsk) ([0-9]+)/i$", $message, $arr)) {
	//get player lvl
	$rk_num = $this->vars["dimension"];
	$info = Player::get_by_name($sender);
	$reward = $arr[2];

	if ($info === null) {
		bot::send("An Error occurred while trying to get your level. Please input it manually via <highlight><symbol>capxp 'mission reward' 'your lvl'<end> or try again later.", $sendto);
		return;
	} else {
		$lvl = $info->level;
	}
} else if (preg_match("/^(capxp|capsk) ([0-9]+) ([0-9]+)$/i", $message, $arr)) {
	$reward = $arr[2];
	
	if (($arr[3] > 219) || ($arr[3] < 1)) {
		bot::send("Your level cannot be greater than 219 or less than 1.", $sendto);
		return;
	} else {
		$lvl = $arr[3];
	}
} else {
	$syntax_error = true;
	return;
}

if ($reward >= 300) {
	$db->query("SELECT * FROM levels WHERE `level` = $lvl");
	$row = $db->fObject();

	if ($lvl < 200) {
		$xp = $row->xpsk;
		$research = (1-(($xp*.2)/$reward))*100;
	} else {
		$sk = $row->xpsk;
		$research = (1-(($sk*.2)/$reward))*100;
	}
	if ($research < 0) {
		$research = 0;
	}
	
	if ($lvl < 200) {
		$msg = "At lvl <highlight>".number_format($lvl)."<end> you need <highlight>".number_format($xp)."<end> xp to level. With a mission reward of <highlight>".number_format($reward)."<end> xp, set your research bar to <highlight>".ceil($research)."%<end> to receive maximum xp from this mission reward.";
	} else {
		$msg = "At lvl <highlight>".number_format($lvl)."<end> you need <highlight>".number_format($sk)."<end> sk to level. With a mission reward of <highlight>".number_format($reward)."<end> sk, set your research bar to <highlight>".ceil($research)."%<end> to receive maximum sk from this mission reward.";
	}
} else {
	 $msg = "Usage: <highlight><symbol>capxp 'mission reward amount' 'custom level'<end><br><tab>ex: !capxp 165000 215<br>If no level is specified, it will use your current level.";
}
	
bot::send($msg, $sendto);

?>
