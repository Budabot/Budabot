<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Refresh/Create org memberlist
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 22.07.2006
   ** Date(last modified): 03.02.2007
   ** 
   ** Copyright (C) 2006, 2007 Carsten Lohmann
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
   
if(preg_match("/^orgmembers$/i", $message)) {
	if ($this->vars["my guild id"] == "") {
	  	$msg = "The Bot needs to be in a org to show the orgmembers.";
	    bot::send($msg, $sendto);
		return;
	}
	
	$db->query("SELECT * FROM org_members_<myname> o LEFT JOIN players p ON o.name = p.name WHERE `mode` != 'del' ORDER BY o.name");  
	$members = $db->numrows();
  	if ($members == 0) {
	  	$msg = "No members recorded.";
	    bot::send($msg, $sendto);
		return;
	}
	
	
	$msg = "Processing orgmember list. This can take a few seconds.";
    bot::send($msg, $sendto);
    
    $first_char = "";
	$list = "<header>::::: Members of the org {$this->vars["my guild"]} :::::<end>";
	while ($row = $db->fObject()) {
        if ($row->logged_off != "0")
	        $logged_off = " :: <highlight>Last logoff:<end> ".gmdate("D F d, Y - H:i", $row->logged_off)."(GMT)";
	    
	    if ($row->name[0] != $first_char) {
	     	$first_char = $row->name[0];
			$list .= "\n\n<highlight><u>$first_char</u><end>\n";
		}
		
		switch ($row->profession) {
        case "Adventurer":
            $prof = "Advy";
            break;
        case "agent":
            $prof = "Agent";
            break;
        case "Bureaucrat":
            $prof = "Crat";
            break;
        case "Doctor":
            $prof = "Doc";
            break;
        case "Enforcer":
            $prof = "Enf";
            break;
        case "Engineer":
            $prof = "Engy";
            break;
        case "Fixer":
            $prof = "Fixer";
            break;
        case "Keeper":
            $prof = "Keeper";
            break;
        case "Martial Artist":
            $prof = "MA";
            break;
        case "Meta-Physicist":
            $prof = "MP";
            break;
        case "Nano-Technician":
            $prof = "NT";
            break;
        case "Soldier":
            $prof = "Sol";
            break;
        case "Trader":
            $prof = "Trader";
            break;
        case "Shade":
            $prof = "Shade";
            break;
	    }
	    
		$list .= "<tab><highlight>$row->name<end> (Lvl $row->level/<green>$row->ai_level<end>/$prof/<highlight>$row->rank<end>)$logged_off\n";	    
	}
	
	$msg = bot::makeLink("{$this->vars["my guild"]} has $members members currently.", $list);
 	bot::send($msg, $sendto);
} else if (preg_match("/^orgmembers (.*)$/i", $message, $arr)) {
	if ($this->vars["my guild id"] == "") {
	  	$msg = "The Bot needs to be in a org to show the orgmembers.";
	  	bot::send($msg, $sendto);
		return;
	}
	
	switch(strtolower($arr[1])) {
        case "adv":
            $prof = "Adventurer";
            break;
        case "agent":
            $prof = "Agent";
            break;
        case "crat":
            $prof = "Bureaucrat";
            break;
        case "doc":
            $prof = "Doctor";
            break;
        case "enf":
            $prof = "Enforcer";
            break;
        case "eng":
            $prof = "Engineer";
            break;
        case "fix":
            $prof = "Fixer";
            break;
        case "keep":
            $prof = "Keeper";
            break;
        case "ma":
            $prof = "Martial Artist";
            break;
        case "mp":
            $prof = "Meta-Physicist";
            break;
        case "nt":
            $prof = "Nano-Technician";
            break;
        case "sol":
            $prof = "Soldier";
            break;
        case "trad":
            $prof = "Trader";
            break;
        case "shade":
            $prof = "Shade";
            break;
    }
    
    if (!$prof) {
        $msg = "Please choose one of these professions: adv, agent, crat, doc, enf, eng, fix, keep, ma, mp, nt, sol, shade or trad";
	    bot::send($msg, $sendto);
	    return;
    }
    
	$db->query("SELECT * FROM org_members_<myname> o LEFT JOIN players p ON o.name = p.name WHERE `mode` != 'del' AND `profession` = '$prof' ORDER BY o.name");

	$members = $db->numrows();
  	if ($members == 0) {
		$msg = "No <highlight>$prof<end>'s as member recorded";		
	  	bot::send($msg, $sendto);
		return; 
	}
	
	
	$msg = "Processing orgmember list. This can take a few seconds.";
  	bot::send($msg, $sendto);
       	
	$list = "<header>::::: Members of the org {$this->vars["my guild"]}:Profession: $prof :::::<end>\n\n";
	while ($row = $db->fObject()) {
        if ($row->logged_off != "0") {
	        $logged_off = gmdate("l F d, Y - H:i", $row->logged_off)."(GMT)";
	    } else {
	    	$logged_off = "<red>Not set yet.<end>";
		}
	    	
	  	$list .= "<tab><highlight>$row->name<end> (Lvl $row->level/<green>$row->ai_level<end> $row->profession) (<highlight>$row->guild_rank<end>) <highlight>::<end> Last logoff: $logged_off\n";
	}
	
	$msg = bot::makeLink("{$this->vars["my guild"]} has $members members currently.", $list);
 	bot::send($msg, $sendto);
} else {
	$syntax_error = true;
}
?>