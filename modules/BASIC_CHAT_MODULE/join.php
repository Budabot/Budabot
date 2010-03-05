<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Handles the join/invite commands
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 17.02.2006
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

$msg = "";
if((eregi("^join$", $message) || eregi("^invite$", $message)) && $type == "msg") {
	if($this->settings["priv_req_open"] == "members") {
	  	$db->query("SELECT * FROM members_<myname> WHERE `name` = '$sender'");
		if($db->numrows() == 0) {
		  	$msg = "Only members of this bot can join it.";
		  	bot::send($msg, $sender);
		  	return;
		} else {
		  	AOChat::privategroup_kick($sender);
			AOChat::privategroup_invite($sender);
			return;
		}
	}

	if($this->settings["priv_status"] == "closed" && $this->settings["priv_status_reason"] == "not set" && !isset($this->admins[$sender])) {
	  	bot::send("The privategroup is closed atm. Please try it later again.", $sender);
	  	return;
	} elseif($this->settings["priv_status"] == "closed" && $this->settings["priv_status_reason"] != "none" && !isset($this->admins[$sender])) {
	  	bot::send("The privategroup is closed atm with the reason: <highlight>{$this->settings["priv_status_reason"]}<end>. Please try it again later.", $sender);
	  	return;
	} 	
	
	$whois = new whois($sender);
   	if($whois->errorCode != 0) {
	    $msg = "Sry. i was unable to get your char infos.";
	    bot::send($msg, $sender);
	    return;
	}
	
	if($this->settings["priv_req_open"] == "org" && !isset($this->guildmembers[$sender])) {
	  	$msg = "Only members of the org <highlight>{$this->vars["my guild"]}<end> can join this bot.";
	} elseif($this->settings["priv_req_lvl"] > $whois->level) {
	  	$msg = "You need to be at least <highlight>{$this->settings["priv_req_lvl"]}<end> to join this bot.";	
	} elseif(($this->settings["priv_req_faction"] == "Omni" || $this->settings["priv_req_faction"] == "Clan" || $this->settings["priv_req_faction"] == "Neutral") && $this->settings["priv_req_faction"] != $whois->faction) {
	  	$msg = "You are not allowed to join this bot.";
	} elseif($this->settings["priv_req_faction"] == "not Omni" || $this->settings["priv_req_faction"] == "not Clan" || $this->settings["priv_req_faction"] == "not Neutral") {
		$tmp = explode(" ", $this->settings["priv_req_faction"]);
		if($tmp[1] == $whois->faction)
			$msg = "You are not allowed to join this bot.";
	}
	if($msg) {
	  	bot::send($msg, $sender);
	  	return;
	}
	AOChat::privategroup_kick($sender);
	AOChat::privategroup_invite($sender);
} elseif((eregi("^join$", $message) || eregi("^invite$", $message)) && $type == "guild") {
	$db->query("SELECT * FROM org_members_<myname> WHERE `name` = '$sender'");
	$whois = $db->fObject();
	if($this->settings["topic_guild_join"] == 1) {
		$time = time() - $this->settings["topic_time"];
		$mins = floor($time / 60);
		$hours = floor($mins / 60);
		$mins = floor($mins - ($hours * 60));
	  	bot::send("<highlight>Topic:<end> {$this->settings["topic"]} [set by <highlight>{$this->settings["topic_setby"]}<end>][<highlight>{$hours}hrs and {$mins}mins ago<end>]", "guild");
	}
	AOChat::privategroup_kick($sender);
	AOChat::privategroup_invite($sender);
}
?>