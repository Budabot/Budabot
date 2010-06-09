<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Check if a player mets the requirements for joining Privatechannel or responding on the tell
   ** Version: 0.2
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 15.10.2006
   ** Date(last modified): 23.01.2007
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
   
//If the incoming message was a join request
if ((preg_match("/^join$/i", $message) || preg_match("/^invite$/i", $message)) && !isset($this->admins[$sender])) {
	//Chek if he is a member of the Bot
	$is_member = false;
	if($this->settings["priv_req_open"] == "members") {
	  	$db->query("SELECT * FROM members_<myname> WHERE `name` = '$sender'");
		if($db->numrows() == 0) {
		  	$msg = "<red>Error! Only Members of this bot can join this bot.<end>";
		  	bot::send($msg, $sender);
  		  	$restricted = true;
		  	return;
		} else
			$is_member = true;
	}

	//Check if he is a org Member
	if($this->settings["priv_req_open"] == "org" && !isset($this->guildmembers)) {
	  	$msg = "<red>Error! Only members of the org {$this->vars["my guild"]} can join this bot.<end>";
	  	bot::send($msg, $sender);
	  	$restricted = true;
	  	return;
	}
	
	//Get his character infos if minlvl or faction is set
	if($this->settings["priv_req_lvl"] != 0 || $this->settings["priv_req_faction"] != "all") {
		$whois = new whois($sender);
	   	if($whois->errorCode != 0) {
		    $msg = "<red>Error! Unable to get your character info. Please try again later.<end>";
		    bot::send($msg, $sender);
		  	$restricted = true;
		    return;
		}
	}
	
	//Check the Minlvl
	if($this->settings["priv_req_lvl"] != 0 && $this->settings["priv_req_lvl"] > $whois->level) {
	  	$msg = "<red>Error! You need to be at least {$this->settings["priv_req_lvl"]} to join this bot.<end>";
	    bot::send($msg, $sender);
	  	$restricted = true;
	    return;
	}
	
	//Check the Faction Limit
	if(($this->settings["priv_req_faction"] == "Omni" || $this->settings["priv_req_faction"] == "Clan" || $this->settings["priv_req_faction"] == "Neutral") && $this->settings["priv_req_faction"] != $whois->faction) {
	  	$msg = "<red>Error! Only Members of the Faction {$this->settings["priv_req_faction"]} can join this bot.<end>";
	    bot::send($msg, $sender);
	  	$restricted = true;
	    return;
	} elseif($this->settings["priv_req_faction"] == "not Omni" || $this->settings["priv_req_faction"] == "not Clan" || $this->settings["priv_req_faction"] == "not Neutral") {
		$tmp = explode(" ", $this->settings["priv_req_faction"]);
		if($tmp[1] == $whois->faction) {
			$msg = "<red>Error! Only members that are not in the Faction {$tmp[1]} can join this bot.<end>";
		    bot::send($msg, $sender);
		  	$restricted = true;
		    return;
		}
	}

	//Check the Maximum Limit for the PrivateGroup
	if($this->settings["priv_req_maxplayers"] != 0 && count($this->chatlist) > $this->settings["priv_req_maxplayers"]) {
	  	$msg = "<red>Error! Only players who are at least level {$this->settings["priv_req_lvl"]} can join this bot.<end>";
	    bot::send($msg, $sender);
	  	$restricted = true;
	    return;
	}
} else if (!isset($this->admins[$sender]) && $sender != $this->settings["relaybot"] && $sender != $this->settings["apftimerbot"] && !preg_match("/^guestjoin/i", $message) && !preg_match("/^verify/i", $message)) {
	//Chek if he is a member of the Bot
	if($this->settings["tell_req_open"] == "members") {
	  	$db->query("SELECT * FROM members_<myname> WHERE `name` = '$sender'");
		if($db->numrows() == 0) {
		  	$msg = "<red>Error! I am only responding to members of this bot!<end>.";
		  	bot::send($msg, $sender);
  		  	$restricted = true;
		  	return;
		}
	}

	//Check if he is a org Member
	if($this->settings["tell_req_open"] == "org" && !isset($this->guildmembers[$sender])) {
	  	$msg = "<red>Error! I am only respondling to members of the org {$this->vars["my guild"]}.<end>";
	  	bot::send($msg, $sender);
	  	$restricted = true;
	  	return;
	}
	
	//Get his character infos if minlvl or faction is set
	if($this->settings["tell_req_lvl"] != 0 || $this->settings["tell_req_faction"] != "all") {
		$whois = new whois($sender);
	   	if($whois->errorCode != 0) {
		    $msg = "<red>Error! Sorry, I was unable to get your char infos. Please try again later.<end>";
		    bot::send($msg, $sender);
		  	$restricted = true;
		    return;
		}
	}
	
	//Check the Minlvl
	if($this->settings["tell_req_lvl"] != 0 && $this->settings["tell_req_lvl"] > $whois->level) {
	  	$msg = "<red>Error! I am only responding to players that are higher then Level {$this->settings["tell_req_lvl"]}.<end>";
	    bot::send($msg, $sender);
   	  	$restricted = true;
	    return;
	}
	
	//Check the Faction Limit
	if(($this->settings["tell_req_faction"] == "Omni" || $this->settings["tell_req_faction"] == "Clan" || $this->settings["tell_req_faction"] == "Neutral") && $this->settings["tell_req_faction"] != $whois->faction) {
	  	$msg = "<red>Error! I am only responding to members of the Faction {$this->settings["tell_req_faction"]}.<end>";
	    bot::send($msg, $sender);
	  	$restricted = true;
	    return;
	} elseif($this->settings["tell_req_faction"] == "not Omni" || $this->settings["tell_req_faction"] == "not Clan" || $this->settings["tell_req_faction"] == "not Neutral") {
		$tmp = explode(" ", $this->settings["tell_req_faction"]);
		if($tmp[1] == $whois->faction) {
			$msg = "<red>Error! I am responding only to members that are not in the Faction {$tmp[1]}.<end>";
		    bot::send($msg, $sender);
    	  	$restricted = true;
		    return;
		}
	}
}
?>