<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Show Infos about Land COntrol Areas
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 06.02.2007
   ** Date(last modified): 06.02.2007
   ** 
   ** Copyright (C) 2007 Carsten Lohmann
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
   
if (preg_match("/^lca ([0-9]+)$/i", $message, $arr)) {
	$ql = $arr[1];
	if($ql < 10 || $ql > 300) {
		$msg = "Invalid QL. Please use a QL between 10 and 300.";
		
		if($type == "msg")
        	bot::send($msg, $sender);
	    elseif($type == "priv")
    	   	bot::send($msg);
	    elseif($type == "guild")
    	   	bot::send($msg, "guild");
    	return;
	}

	$db->query("SELECT * FROM `towerranges` WHERE `low_level` <= $ql AND `high_level` >= $ql ORDER BY `playfield` LIMIT 0, 40");
	if($db->numrows() == 0) {
		$msg = "No matches.";
		bot::send($msg, $sendto);
    	return;
	}
	
	$playfield = "";
	$list = "<header>::::: Land Control Areas with the QL$ql(Matches ".$db->numrows().") :::::<end>\n";
	if($db->numrows() == 40) {
		$list .= "<red>Note: Output has been limited to 40fields!<end>\n\n";
	} else
		$list .= "\n";

	while($row = $db->fObject()) {
	 	if($playfield != $row->playfield) {
	 	 	$playfield = $row->playfield;
	 		$list .= "<highlight><u>$row->playfield</u><end>\n";
	 	}
		$list .= "Hugemaplocation: <highlight>#$row->hugemaploc<end>\n";
		$list .= "Level Range: <highlight>$row->low_level-$row->high_level<end>\n";
		$list .= "Location: <highlight>$row->location<end>\n\n";
	}
	
	$msg = bot::makeLink("Land Control Areas", $list);
	bot::send($msg, $sendto);
} elseif(preg_match("/^lca ([a-z ]+)$/i", $message, $arr)) {
	$name = $arr[1];

	$db->query("SELECT * FROM `towerranges` WHERE `playfield` LIKE '$name' ORDER BY `low_level` LIMIT 0, 40");
	if($db->numrows() == 0) {
		$msg = "No matches.";
		bot::send($msg, $sendto);
    	return;
	}
	
	$playfield = "";
	$list = "<header>::::: Land Control Areas with the name $name(Matches ".$db->numrows().") :::::<end>\n";
	if($db->numrows() == 40) {
		$list .= "<red>Note: Output has been limited to 40fields!<end>\n\n";
	} else
		$list .= "\n";

	while($row = $db->fObject()) {
	 	if($playfield != $row->playfield) {
	 	 	$playfield = $row->playfield;
	 		$list .= "<highlight><u>$row->playfield</u><end>\n";
	 	}
		$list .= "Hugemaplocation: <highlight>#$row->hugemaploc<end>\n";
		$list .= "Level Range: <highlight>$row->low_level-$row->high_level<end>\n";
		$list .= "Location: <highlight>$row->location<end>\n\n";
	}
	
	$msg = bot::makeLink("Land Control Areas", $list);
	bot::send($msg, $sendto);
} elseif(preg_match("/^lca ([0-9]+) ([a-z ]+)$/i", $message, $arr)) {
	$name = $arr[2];
	$ql = $arr[1];
	if($ql < 10 || $ql > 300) {
		$msg = "Invalid QL. Please use a QL between 10 and 300.";
		
		bot::send($msg, $sendto);
    	return;
	}
	
	$db->query("SELECT * FROM `towerranges` WHERE `playfield` LIKE '$name' AND `low_level` <= $ql AND `high_level` >= $ql ORDER BY `low_level` LIMIT 0, 40");
	if($db->numrows() == 0) {
		$msg = "No matches.";
		bot::send($msg, $sendto);
    	return;
	}
	
	$playfield = "";
	$list = "<header>::::: Land Control Areas with the name $name and ql$ql(Matches ".$db->numrows().") :::::<end>\n";
	if($db->numrows() == 40) {
		$list .= "<red>Note: Output has been limited to 40fields!<end>\n\n";
	} else
		$list .= "\n";

	while($row = $db->fObject()) {
	 	if($playfield != $row->playfield) {
	 	 	$playfield = $row->playfield;
	 		$list .= "<highlight><u>$row->playfield</u><end>\n";
	 	}
		$list .= "Hugemaplocation: <highlight>#$row->hugemaploc<end>\n";
		$list .= "Level Range: <highlight>$row->low_level-$row->high_level<end>\n";
		$list .= "Location: <highlight>$row->location<end>\n\n";
	}
	
	$msg = bot::makeLink("Land Control Areas", $list);
	bot::send($msg, $sendto);

} else {
	$syntax_error = true;
}
?>