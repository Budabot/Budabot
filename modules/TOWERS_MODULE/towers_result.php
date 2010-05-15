<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows the attack and results on tower attacks
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 04.12.2005
   ** Date(last modified): 23.08.2007
   ** 
   ** Copyright (C) 2005, 2006 Carsten Lohmann
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


$colorlabel = "<font color=#00DE42>";
$colorvalue = "<font color=#63AD63>";

$listcount = 20;

if (preg_match("/^battle(s?)$/i", $message) || preg_match("/^battle(s?) (.+)$/i", $message, $arr)) {

	$search = '';
	if ($arr[1] != "") {
		$search = " WHERE `att_guild` LIKE '".str_replace("'", "''", $arr[1])."' OR `att_player` LIKE '".str_replace("'", "''", $arr[1])."' OR `def_guild` LIKE '".str_replace("'", "''", $arr[1])."' OR `zone` LIKE '".str_replace("'", "''", $arr[1])."' ";
	}

	$db->query("SELECT * FROM tower_attack_<myname> $search ORDER BY `time` DESC LIMIT 0, $listcount");

	if($db->numrows() == 0 && $search == " ") {
        	$msg = "No Tower messages recorded yet.";

    	} elseif($db->numrows() == 0) {
		$msg = "No Tower messages found within this search.";

	} else {
		$list = "<header>::::: The last $listcount Tower Attacks :::::<end>\n\n".$colorvalue;

 		while($row = $db->fObject()) {
			$list .= $colorlabel."Attacktime:<end> ".gmdate("M j, Y, G:i", $row->time)." (GMT)\n";
			if (!$att_side = strtolower($row->att_side)) {$att_side = "unknown";}
			if (!$def_side = strtolower($row->def_side)) {$def_side = "unknown";}

	    		if ($row->att_profession == "Unknown") {
					$list .= $colorlabel."Attacker:<end> <$att_side>".$row->att_player."<end> (".ucfirst($att_side).")\n";
	    		} else if ($row->att_guild == "") {
	        		$list .= $colorlabel."Attacker:<end> <$att_side>".$row->att_player."<end> (Lvl ".$row->att_level."/".$row->att_profession.") (".ucfirst($att_side).")\n";
	    		} else {
	        		$list .= $colorlabel."Attacker:<end> ".$row->att_player." (Lvl ".$row->att_level."/".$row->att_profession."/<$att_side>".$row->att_guild."<end>) (".ucfirst($att_side).")\n";
				}


			$list .= $colorlabel."Defender:<end> <$def_side>".$row->def_guild."<end> (".ucfirst($def_side).")\n";
			$list .= $colorlabel."Zone:<end> ".$row->zone." (".$row->x."x".$row->y.")\n\n";
 		}
		$msg = bot::makeLink("Tower Battle: click to view", $list);
	}

} else if (preg_match("/^victory$/i", $message) || preg_match("/^victory (.+)$/i", $message, $arr)) {

	if ($arr[1] == "") {$search = " ";}
	else {$search = " WHERE `win_guild` LIKE '".str_replace("'", "''", $arr[1])."' OR `lose_guild` LIKE '".str_replace("'", "''", $arr[1])."' ";}

	$db->query("SELECT * FROM tower_result_<myname>".$search."ORDER BY `time` DESC LIMIT 0, $listcount");
	if($db->numrows() == 0 && $search == " ")
		$msg = "No Tower results recorded yet.";
	elseif($db->numrows() == 0)
		$msg = "No Tower results found within this search.";
	else {
		$list = "<header>::::: The last $listcount Tower Results :::::<end>\n\n".$colorvalue;
        	while($row = $db->fObject()) {
 			$list .= $colorlabel."Time:<end> ".gmdate("M j, Y, G:i", $row->time)." (GMT)\n";

			if (!$win_side = strtolower($row->win_side)) {$win_side = "unknown";}
			if (!$lose_side = strtolower($row->lose_side)) {$lose_side = "unknown";}

			$list .= $colorlabel."Winner:<end> <$win_side>".$row->win_guild."<end> (".ucfirst($win_side).")\n";
			$list .= $colorlabel."Loser:<end> <$lose_side>".$row->lose_guild."<end> (".ucfirst($lose_side).")\n\n";
		}
		$msg = "Tower Battle Results: ".bot::makeLink("click to view", $list);
	}
 
} else {
	$syntax_error = true;
}

if ($msg) {

	// Won't need these 4 lines for 0.7.0
	$msg = str_replace("<neutral>", "<font color='#EEEEEE'>", $msg);
	$msg = str_replace("<omni>", "<font color='#00FFFF'>", $msg);
	$msg = str_replace("<clan>", "<font color='#F79410'>", $msg);
	$msg = str_replace("<unknown>", "<font color='#FF0000'>", $msg);

	// Send info back
	bot::send($msg, $sendto);
}

?>
