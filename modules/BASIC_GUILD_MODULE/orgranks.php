<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Show Orgmembers sorted after ranks
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 06.11.2006
   ** Date(last modified): 26.11.2006
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
   
if(eregi("^orgranks$", $message)) {
	if($this->vars["my guild id"] == "") {
	  	$msg = "The Bot needs to be in a org to show the orgmembers.";
	  	// Send info back
	    if($type == "msg")
	        bot::send($msg, $sender);
	    elseif($type == "priv")
	       	bot::send($msg);
	    elseif($type == "guild")
	       	bot::send($msg, "guild");
	}
	
	$db->query("SELECT * FROM org_members_<myname> WHERE `mode` != 'del' ORDER BY `rank_id`");  
	$members = $db->numrows();
  	if($members == 0) {
	  	$msg = "No members recorded.";
	  	// Send info back
	    if($type == "msg")
	        bot::send($msg, $sender);
	    elseif($type == "priv")
	       	bot::send($msg);
	    elseif($type == "guild")
	       	bot::send($msg, "guild");	    
	}
	
	
	$msg = "Processing orgmember list. This can take a few seconds.";
  	// Send info back
    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
       	bot::send($msg);
    elseif($type == "guild")
       	bot::send($msg, "guild");
       	
	$list = "<header>::::: Members of the org {$this->vars["my guild"]}(Sorted by orgrank) :::::<end>\n\n";
	while($row = $db->fObject()) {
        if($row->logged_off != "0")
	        $logged_off = gmdate("l F d, Y - H:i", $row->logged_off)."(GMT)";
	    else
	    	$logged_off = "<red>Not set yet.<end>";
	    	
	  	$list .= "<tab><highlight>$row->name<end> (Lvl $row->level/<green>$row->ai_level<end> $row->profession) (<highlight>$row->rank<end>) <highlight>::<end> Last logoff: $logged_off\n";
	}
	
	$msg = bot::makeLink("{$this->vars["my guild"]} has $members members currently.", $list);
 	// Send info back
    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
       	bot::send($msg);
    elseif($type == "guild")
       	bot::send($msg, "guild");
} else
	$syntax_error = true;
?>