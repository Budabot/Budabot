<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Guestchannel (invite/kick)
   ** Version: 1.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.11.2005
   ** Date(last modified): 21.11.2006
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

if(eregi("^guest invite (.+)$", $message, $arr)) {
    $who = ucfirst(strtolower($arr[1]));
    $uid = AoChat::get_uid($arr[1]);
    if(!$uid)
        $msg = "Player <highlight>".$who."<end> does not exist.";
    else {
        $this->vars["Guest"][$who] = false;
        $msg = "<highlight>$who<end> has been invited to the Guestchannel.";
        AOChat::privategroup_invite($who);
    }
    bot::send($msg, "guild");
} elseif(eregi("^guest kick (.+)$", $message, $arr)) {
    $who = ucfirst(strtolower($arr[1]));
    $uid = AoChat::get_uid($arr[1]);
    if(!$uid)
        $msg = "Player <highlight>".$who."<end> does not exist.";
    else if($this->vars["Guest"][$who] == false) {
        $msg = "<highlight>$who<end> hasn´t joined the Guestchannel.";
    } else {
        unset($this->vars["Guest"][$who]);
        $msg = "<highlight>$who<end> has been kicked from the Guestchannel.";
        AOChat::privategroup_kick($who);
    }
    bot::send($msg, "guild");
} elseif(eregi("^guest add (.+)$", $message, $arr)) {
    $who = ucfirst(strtolower($arr[1]));
    $uid = AoChat::get_uid($arr[1]);
    if(!$uid)
        $msg = "Player <highlight>".$who."<end> does not exist.";
    else {
	  	$db->query("SELECT * FROM guests_<myname> WHERE `name` = '$who'");
	  	if($db->numrows() != 0)
	  		$msg = "<highlight>$who<end> is already on the guestlist.";
	  	else {
		    $db->query("INSERT INTO guests_<myname> (`name`) VALUES ('$who')");
		    $msg = "<highlight>$who<end> has been added to the guestlist.";
			if(!isset($this->buddList[$who]))
		        bot::send("addbuddy", $uid);
		}
	}
    bot::send($msg, "guild");
} elseif(eregi("^guest (rem|del) (.+)$", $message, $arr)) {
    $who = ucfirst(strtolower($arr[2]));
    $uid = AoChat::get_uid($arr[2]);
    if(!$uid)
        $msg = "Player <highlight>".$who."<end> does not exist.";
    else {
	  	$db->query("SELECT * FROM guests_<myname> WHERE `name` = '$who'");
	  	if($db->numrows() == 0)
	  		$msg = "<highlight>$who<end> is not on the guestlist.";
	  	else {
		    $db->query("DELETE FROM guests_<myname> WHERE `name` = '$who'");
		    $msg = "<highlight>$who<end> has been removed from the guestlist.";
	        bot::send("rembuddy", $uid);
		}
	}
    bot::send($msg, "guild");
} elseif(eregi("^guestlist$", $message)) {
	$db->query("SELECT * FROM guests_<myname> ORDER BY `name`");
	$autoguests = $db->numrows();
	if($autoguests != 0) {
	  	$list .= "<header>::::: Users on Autoinvitelist :::::<end>\n\n";
	  	while($row = $db->fObject()) {
	  	  	if($this->buddyList[$row->name] == 1) {
				$status = "<green>Online";
				if($this->vars["Guest"][$row->name] == true)
			    	$status .= " and in Guestchannel";
			} else
				$status = "<red>Offline";

	  		$list .= "<tab>- $row->name ($status<end>)\n";
	  	}
	  	
	    $msg = "<highlight>".$autoguests."<end> players on the Autoinvitelist ";
	    $link = ":: ".bot::makeLink('Click here', $list);
	    if($autoguests != 0)
           	bot::send($msg.$link, "guild");
        else
           	bot::send($msg, "guild");
	} else
       	bot::send("No player is on this list.", "guild");   
} elseif(eregi("^guests$", $message)) {
	if(count($this->vars["Guest"]) > 0) {
		$db->query("SELECT * FROM priv_chatlist_<myname> WHERE `guest` = 1 ORDER BY `profession`, `level` DESC");
		$numguest = $db->numrows();

		$list = "<header>::::: $numguest User(s) in Guestchannel<end>\n";
	    while($row = $db->fObject()) {
            if($oldprof != $row->profession) {
                $list .= "\n<tab><highlight>$row->profession<end>\n";
                $oldprof = $row->profession;
            }
            if($row->afk != "0")
                $afk = "(<red>Away from keyboard<end>)";
            else
                $afk = "";
			$list .= "<tab><tab><highlight>$row->name<end> (Lvl $row->level/<green>$row->ai_level<end>) <highlight>::<end> ($row->guild) $afk\n";
	    }
		
        $msg = "<highlight>".$numguest."<end> players in Guestchannel ";
        $link = ":: ".bot::makeLink('Click here', $list);
        if($numguest != 0)
           	bot::send($msg.$link, "guild");
        else
           	bot::send($msg, "guild");
	 } else
       	bot::send("No player is in the guestchannel.", "guild");
}
?>
