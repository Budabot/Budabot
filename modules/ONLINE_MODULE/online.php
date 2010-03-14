<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Who is online(online design)
   ** Version: 1.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.11.2005
   ** Date(last modified): 03.02.2007
   ** 
   ** Copyright (C) 2005, 2006, 2007 Carsten Lohmann
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

$msg = "";
if(eregi("^online$", $message)){
  	if($type == "guild" || ($this->settings["online_tell"] == 0 && $type == "msg") || ($type == "priv" && $this->vars["Guest"][$sender] == true)) {
	  	if($this->settings["relaybot"])
			$db->query("SELECT * FROM guild_chatlist_<myname> UNION ALL SELECT * FROM guild_chatlist_".strtolower($this->settings["relaybot"])." ORDER BY `profession`, `level` DESC");
	    else
			$db->query("SELECT * FROM guild_chatlist_<myname> ORDER BY `profession`, `level` DESC");
	} elseif($type == "priv" || ($this->settings["online_tell"] == 1 && $type == "msg")) {
	  	$db->query("SELECT * FROM priv_chatlist_<myname> ORDER BY `profession`, `level` DESC");
	}
    $numonline = $db->numrows();
    $list .= "<header>::::: $numonline member(s) online :::::<end>\n";
    $data = $db->fObject("all");
    foreach($data as $row) {
	    $name = bot::makeLink($row->name, "/tell $row->name", "chatcmd");
	    
        if($row->profession == "")
	        $row->profession = "Unknown";
	        
        if($oldprof != $row->profession) {
			$list .= "\n<tab><highlight>$row->profession<end>\n";		
            $oldprof = $row->profession;
        }
        
        if($row->afk == "kiting")
            $afk = " <highlight>::<end> <red>KITING<end>";
		elseif($row->afk != "0")
            $afk = " <highlight>::<end> <red>AFK<end>";
        else
            $afk = "";
            		            
		if($type == "guild" || ($this->settings["online_tell"] == 0 && $type == "msg") || ($type == "priv" && $this->vars["Guest"][$sender] == true)) {
	        $db->query("SELECT * FROM alts WHERE `alt` = '$row->name'");
	        if($db->numrows() == 0)
	        	$alt = "<highlight>::<end> <a href='chatcmd:///tell <myname> alts $row->name'>Alts</a>";
	        else {
	          	$row1 = $db->fObject();
			  	$alt = "<highlight>::<end> <a href='chatcmd:///tell <myname> alts $row->name'>Alts of $row1->main</a>";
			}

		    if($row->guild == "") 
		    	$guild = "Not in a guild";
		    else
		    	$guild = $row->guild." (<highlight>$row->rank<end>)";	
	        $list .= "<tab><tab><highlight>$name<end> (Lvl $row->level/<green>$row->ai_level<end>) <highlight>::<end> $guild$afk $alt\n";		  
		} else {
		    if($row->guild == "") 
		    	$guild = "Not in a guild";
		    else
		    	$guild = $row->guild;		  
	        $list .= "<tab><tab><highlight>$name<end> (Lvl $row->level/<green>$row->ai_level<end>) <highlight>::<end> $guild$afk\n";	  
		}
    }

	// Guest Channel Part
    if((count($this->vars["Guest"]) > 0 || $this->settings["relaybot"]) && ($type == "guild" || ($this->settings["online_tell"] == 0 && $type == "msg")  || ($type == "priv" && $this->vars["Guest"][$sender] == true))) {
	    if($this->settings["relaybot"])
			$db->query("SELECT * FROM priv_chatlist_<myname> UNION ALL SELECT * FROM priv_chatlist_".strtolower($this->settings["relaybot"])." ORDER BY `profession`, `level` DESC");
	    else
	    	$db->query("SELECT * FROM priv_chatlist_<myname> ORDER BY `profession`, `level` DESC");
		$numguest = $db->numrows();
		$list .= "\n\n<highlight><u>$numguest User(s) in Guestchannel<end></u>\n";
		$oldprof = "";
	    while($row = $db->fObject()) {
		    $name = bot::makeLink($row->name, "/tell $row->name", "chatcmd");
		    
            if($row->profession == "")
		        $row->profession = "Unknown";
		    
		    if($oldprof != $row->profession) {
	    	    $list .= "\n<tab><highlight>$row->profession<end>\n";
	            $oldprof = $row->profession;
	        }
	        if($row->afk == "kiting")
            	$afk = " <highlight>::<end> <red>KITING<end>";
			elseif($row->afk != "0")
	            $afk = " <highlight>::<end> <red>AFK<end>";
	        else
	            $afk = "";

		    if($row->guild == "") 
		    	$guild = "Not in a guild";
		    else
		    	$guild = $row->guild;
	        $list .= "<tab><tab><highlight>$name<end> (Lvl $row->level/<green>$row->ai_level<end>) <highlight>::<end> $guild$afk\n";	            
	    }      
	}
	$numonline += $numguest;
    
    $msg .= "<highlight>$numonline<end> members are online :: ";
    $link = bot::makeLink('Click here', $list);

    if($type == "msg")
        bot::send($msg.$link, $sender);
    elseif($type == "priv")
       	bot::send($msg.$link);
    elseif($type == "guild")
       	bot::send($msg.$link, "guild");
} else if(eregi("^online (.*)$", $message, $arr)) {
    switch(strtolower($arr[1])) {
        case "all":
            $prof = "all";
            break;
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
    if(!$prof) {
        $msg = "Please choose one of these professions: adv, agent, crat, doc, enf, eng, fix, keep, ma, mp, nt, sol, shade, trad or all";
	    if($type == "msg")
	        bot::send($msg, $sender);
	    elseif($type == "priv")
	       	bot::send($msg);
	    elseif($type == "guild")
	       	bot::send($msg, "guild");
	    return;
    }

	if($type == "guild" || ($this->settings["online_tell"] == 0 && $type == "msg")  || ($type == "priv" && $this->vars["Guest"][$sender] == true)) {
		if($this->settings["relaybot"]) {
			if($prof == "all")
		        $db->query("SELECT * FROM guild_chatlist_<myname> UNION ALL SELECT * FROM guild_chatlist_".strtolower($this->settings["relaybot"])." ORDER BY `profession`, `level` DESC");
		    else
	            $db->query("SELECT * FROM guild_chatlist_<myname> WHERE `profession` = '$prof' UNION ALL SELECT * FROM guild_chatlist_".strtolower($this->settings["relaybot"])." WHERE `profession` = '$prof'"); 	
		} else {
	        if($prof == "all")
	            $db->query("SELECT * FROM guild_chatlist_<myname> ORDER BY `profession`, `level` DESC");
	        else
	            $db->query("SELECT * FROM guild_chatlist_<myname> WHERE `profession` = '$prof'");
		}
	} elseif($type == "priv" || ($this->settings["online_tell"] == 1 && $type == "msg")) {
        if($prof == "all")
            $db->query("SELECT * FROM priv_chatlist_<myname> ORDER BY `profession`, `level` DESC");
        else
            $db->query("SELECT * FROM priv_chatlist_<myname> WHERE `profession` = '$prof'");
	}

    $numonline = $db->numrows();
    $list .= "<header>::::: $numonline members online :::::<end>\n";
    $data = $db->fObject("all");
    foreach($data as $row) {
	    $name = bot::makeLink($row->name, "/tell $row->name", "chatcmd");
	    
        if($row->profession == "")
	        $row->profession = "Unknown";
	        
        if($oldprof != $row->profession) {
			$list .= "\n<tab><highlight>$row->profession<end>\n";		
            $oldprof = $row->profession;
        }
        
        if($row->afk == "kiting")
           	$afk = " <highlight>::<end> <red>KITING<end>";
		elseif($row->afk != "0")
            $afk = " <highlight>::<end> <red>AFK<end>";
        else
            $afk = "";

		if($type == "guild" || ($this->settings["online_tell"] == 0 && $type == "msg") || ($type == "priv" && $this->vars["Guest"][$sender] == true)) {
	        $db->query("SELECT * FROM alts WHERE `alt` = '$row->name'");
	        if($db->numrows() == 0)
	        	$alt = "<highlight>::<end> <a href='chatcmd:///tell <myname> alts $row->name'>Alts</a>";
	        else {
	          	$row1 = $db->fObject();
			  	$alt = "<highlight>::<end> <a href='chatcmd:///tell <myname> alts $row->name'>Alts of $row1->main</a>";
			}

		    if($row->guild == "") 
		    	$guild = "Not in a guild";
		    else
		    	$guild = $row->guild." (<highlight>$row->rank<end>)";	
	        $list .= "<tab><tab><highlight>$name<end> (Lvl $row->level/<green>$row->ai_level<end>) <highlight>::<end> $guild$afk $alt\n";		  
		} else {
		    if($row->guild == "") 
		    	$guild = "Not in a guild";
		    else
		    	$guild = $row->guild;		  
	        $list .= "<tab><tab><highlight>$name<end> (Lvl $row->level/<green>$row->ai_level<end>) <highlight>::<end> $guild$afk\n";	  
		}
    }
        
    // Guest Channel Part
    if(count($this->vars["Guest"]) > 0 && ($type == "guild" || ($this->settings["online_tell"] == 0 && $type == "msg") || ($type == "priv" && $this->vars["Guest"][$sender] == true))) {
	    if($prof == "all")
		    if($this->settings["relaybot"])
				$db->query("SELECT * FROM priv_chatlist_<myname> UNION ALL SELECT * FROM priv_chatlist_".strtolower($this->settings["relaybot"])." ORDER BY `profession`, `level` DESC");
		    else
		    	$db->query("SELECT * FROM priv_chatlist_<myname> ORDER BY `profession`, `level` DESC");
        else
        	if($this->settings["relaybot"])
				$db->query("SELECT * FROM priv_chatlist_<myname> UNION ALL SELECT * FROM priv_chatlist_".strtolower($this->settings["relaybot"])." WHERE `profession` = '$prof' ORDER BY `level` DESC");
		    else
		    	$db->query("SELECT * FROM priv_chatlist_<myname> WHERE `profession` = '$prof' ORDER BY `level` DESC");

		$numguest = $db->numrows();
		$list .= "\n\n<highlight><u>$numguest User(s) in Guestchannel<end></u>\n";
	    while($row = $db->fObject()) {
		    $name = bot::makeLink($row->name, "/tell $row->name", "chatcmd");
		    
	        if($row->profession == "")
		        $row->profession = "Unknown";

            if($oldprof != $row->profession) {
                $list .= "\n<tab><highlight>$row->profession<end>\n";
                $oldprof = $row->profession;
            }
   	        if($row->afk == "kiting")
            	$afk = " <highlight>::<end> <red>KITING<end>";
			elseif($row->afk != "0")
	            $afk = " <highlight>::<end> <red>AFK<end>";
	        else
	            $afk = "";
	            
		    if($row->guild == "") 
		    	$guild = "Not in a guild";
		    else
		    	$guild = $row->guild; 	
	        $list .= "<tab><tab><highlight>$name<end> (Lvl $row->level/<green>$row->ai_level<end>) <highlight>::<end> $guild$afk\n";	            
        }      
	}
	$numonline += $numguest;

    $msg .= "<highlight>".$numonline."<end> members are online ";
    $link = ":: ".bot::makeLink('Click here', $list);
    if($numonline != 0) {
    	if($type == "msg")
	        bot::send($msg.$link, $sender);
	    elseif($type == "priv")
	       	bot::send($msg.$link);
	    elseif($type == "guild")
	       	bot::send($msg.$link, "guild");		  
	} else {
	    if($type == "msg")
	        bot::send($msg, $sender);
	    elseif($type == "priv")
	       	bot::send($msg);
	    elseif($type == "guild")
	       	bot::send($msg, "guild");		  
	}
}
?>
