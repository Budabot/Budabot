<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows which symbiants a pocketboss can drop
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 13.01.2006
   ** Date(last modified): 21.11.2006
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

if(eregi("^pb (.+)$", $message, $arr)) {
	$search = str_replace(" ", "%", $arr[1]);
	$pb = ucfirst(strtolower($arr[1]));
  	$db->query("SELECT * FROM pbdb WHERE `pb` LIKE \"%$search%\" GROUP BY `pb` ORDER BY `pb`");
	$pb_found = $db->numrows();
  	if($pb_found >= 1 && $pb_found <= 5) {
		$msg = "Pocketbosses matching: ";
  	  	$data = $db->fObject("all");
		foreach($data as $row) {
		  	$row->pb = str_replace("\'", "'", $row->pb);
		  	$row->pb_location = str_replace("\'", "'", $row->pb_location);
		  	$row->bp_mob = str_replace("\'", "'", $row->bp_mob);
		  	$row->bp_location = str_replace("\'", "'", $row->bp_location);			  		  	
			$link  = "<header>:::::: Remains of $row->pb :::::<end>\n\n";
			$link .= "<highlight>Location:<end> $row->pb_location\n";
			$link .= "<highlight>Found on:<end> $row->bp_mob\n";
			$link .= "<highlight>Mob Level:<end> $row->bp_lvl\n";
			$link .= "<highlight>General Location:<end> $row->bp_location\n";
			$link .= "_____________________________\n";
			$db->query("SELECT * FROM pbdb WHERE pb = '$row->pb' ORDER BY ql");
			while($symb = $db->fObject()){
			  	$name = "QL $symb->ql $symb->line $symb->slot Symbiant, $symb->type Unit Aban";
			  	$link .= bot::makeItem($symb->itemid, $symb->itemid, $symb->ql, $name)."\n";
			}
			$msg .= "\n".bot::makeLink("Remains of $row->pb", $link);
		}
	} elseif($pb_found > 5)
		$msg = "Too many results.";
	else
		$msg = "Couldn´t find the Pocketboss <highlight>".$pb."<end>";
	
    bot::send($msg, $sendto);
} elseif(eregi("^symb (eye|ocular|head|brain|ear|rarm|chest|larm|rwrist|waist|lwrist|rhand|legs|leg|thigh|lhand|feet) (s(u(p(p(o(r(t)?)?)?)?)?)?|c(o(n(t(r(o(l)?)?)?)?)?)?|i(n(f(a(n(t(r(y)?)?)?)?)?)?)?|a(r(t(i(l(l(e(r(y)?)?)?)?)?)?)?)?|e(x(t(e(r(m(i(n(a(t(i(o(n)?)?)?)?)?)?)?)?)?)?)?)?)$", $message, $arr)) {
  	switch($arr[1]) {
	    case "eye":
	    	$arr[1] = "ocular";
	    break;
	    case "head":
	    	$arr[1] = "brain";
	    break;
	    case "legs":
	    	$arr[1] = "thigh";
	    break;
	    case "leg":
	    	$arr[1] = "thigh";
	    break;
	    case "larm":
	    	$arr[1] = "Left Arm";
	    break;
	    case "rarm":
	    	$arr[1] = "Right Arm";
	    break;
	    case "rwrist":
	    	$arr[1] = "Right Wrist";
	    break;
	    case "lwrist":
	    	$arr[1] = "Left Wrist";
	    break;
   	    case "rhand":
	    	$arr[1] = "Right Hand";
	    break;
	    case "lhand":
	    	$arr[1] = "Left Hand";
	    break;
  	}
	
	if(eregi("^a",$arr[2])) {
		$arr[2] = "artillery";
	} else if(eregi("^s",$arr[2])) {
		$arr[2] = "support";
	} else if(eregi("^i",$arr[2])) {
		$arr[2] = "infantry";
	} else if(eregi("^e",$arr[2])) {
		$arr[2] = "extermination";
	} else if(eregi("^c",$arr[2])) {
		$arr[2] = "control";
	}

	$arr[1] = ucfirst($arr[1]);
	$arr[2] = ucfirst($arr[2]);

  	$db->query("SELECT * FROM pbdb WHERE `slot` = '$arr[1]' AND `type` = '$arr[2]' ORDER BY `ql` DESC");
  	$data = $db->fObject("all");
	$numrows = $db->numrows();
	if($numrows != 0) {
	  	$link = "<header>::::: Result of your search :::::<end>";
		foreach($data as $row) {
		  	$row->pb = str_replace("\'", "'", $row->pb);
		  	
		  	$name = "\n\nQL $row->ql $row->line $row->slot Symbiant, $row->type Unit Aban";
		  	$link .= bot::makeItem($row->itemid, $row->itemid, $row->ql, $name)."\n";
		  	$link .= "Found on ".bot::makeLink($row->pb, "/tell <myname> pb $row->pb", "chatcmd");
		}
		$msg .= bot::makeLink("Found $numrows matches", $link);
	} else
		$msg = "Couldn´t find any symbs";

	
    bot::send($msg, $sendto);
} elseif(eregi("^symb (s(u(p(p(o(r(t)?)?)?)?)?)?|c(o(n(t(r(o(l)?)?)?)?)?)?|i(n(f(a(n(t(r(y)?)?)?)?)?)?)?|a(r(t(i(l(l(e(r(y)?)?)?)?)?)?)?)?|e(x(t(e(r(m(i(n(a(t(i(o(n)?)?)?)?)?)?)?)?)?)?)?)?) (eye|ocular|head|brain|ear|rarm|chest|larm|rwrist|waist|lwrist|rhand|legs|leg|thigh|lhand|feet)$", $message, $arr)) {
  	$index = count($arr) - 1;
	switch($arr[$index]) {
	    case "eye":
	    	$arr[$index] = "ocular";
	    break;
	    case "head":
	    	$arr[$index] = "brain";
	    break;
	    case "legs":
	    	$arr[$index] = "thigh";
	    break;
	    case "leg":
	    	$arr[$index] = "thigh";
	    break;
	    case "larm":
	    	$arr[$index] = "Left Arm";
	    break;    
	    case "rarm":
	    	$arr[$index] = "Right Arm";
	    break;
	    case "rwrist":
	    	$arr[$index] = "Right Wrist";
	    break;
	    case "lwrist":
	    	$arr[$index] = "Left Wrist";
	    break;
   	    case "rhand":
	    	$arr[$index] = "Right Hand";
	    break;
	    case "lhand":
	    	$arr[$index] = "Left Hand";
	    break;
	}
	
	if(eregi("^a",$arr[1])) {
		$arr[1] = "artillery";
	} else if(eregi("^s",$arr[1])) {
		$arr[1] = "support";
	} else if(eregi("^i",$arr[1])) {
		$arr[1] = "infantry";
	} else if(eregi("^e",$arr[1])) {
		$arr[1] = "extermination";
	} else if(eregi("^c",$arr[1])) {
		$arr[1] = "control";
	}
	
	$arr[1] = ucfirst($arr[1]);
	$arr[$index] = ucfirst($arr[$index]);

  	$db->query("SELECT * FROM pbdb WHERE `slot` = '{$arr[$index]}' AND `type` = '$arr[1]' ORDER BY `ql` DESC");
  	$data = $db->fObject("all");
	$numrows = $db->numrows();
	if($numrows != 0) {
	  	$link = "<header>::::: Result of your search :::::<end>";
		foreach($data as $row) {
		  	$row->pb = str_replace("\'", "'", $row->pb);
		  	
		  	$name = "\n\nQL $row->ql $row->line $row->slot Symbiant, $row->type Unit Aban";
		  	$link .= bot::makeItem($row->itemid, $row->itemid, $row->ql, $name)."\n";
		  	$link .= "Found on ".bot::makeLink($row->pb, "/tell <myname> pb $row->pb", "chatcmd");
		}
		$msg .= bot::makeLink("Found $numrows matches", $link);
	} else
		$msg = "Couldn´t find any symbs";

	bot::send($msg, $sendto);
} else
	$syntax_error = true;
?>