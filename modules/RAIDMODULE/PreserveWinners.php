<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Preserve Winners
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 04.03.2006
   ** Date(last modified): 31.07.2006
   ** 
   ** Copyright (C) 2006 Carsten Lohmann
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

global $loot_winners;
if(eregi("^pwinners (0|off)$", $message, $arr)) {
	bot::savesetting("preserve_winners", "0");
    $msg = "Preserve Winners is <red>disabled<end> now. To clear the winner list do <sympol>pwinners clear.";
    bot::send($msg);
} elseif(eregi("^pwinners (1|on)$", $message)) {
	bot::savesetting("preserve_winners", "1");
    $msg = "Preserve Winners is <green>enabled<end> now.";
    bot::send($msg);
} elseif(eregi("^pwinners clear$", $message)) {
  	$loot_winners = "";
  	$msg = "Winnerlist has been cleared by <highlight>$sender<end>.";
  	bot::send($msg);
} elseif(eregi("^pwinners$", $message)) {
  	if($this->settings["preserve_winners"] == 0)
  		$msg = "Preserve Winners is currently <red>disabled<end>.";
  	else
  		$msg = "Preserve Winners is currently <green>enabled<end>.";
  	bot::send($msg);
} else
	$syntax_error = true;
?>