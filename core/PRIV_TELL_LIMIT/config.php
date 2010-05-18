<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Set Requirements for joining Privatechannel and responding on tells
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 12.10.2006
   ** Date(last modified): 21.10.2006
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
if(preg_match("/^limits$/i", $message)) {
	$list = "<header>::::: Limits on using the Bot :::::<end>\n\n";
	$list .= "The bot offers limits that apply to the privategroup(like faction or lvl limi) and responding to tells. Click behind a setting on Change this to set it to a new value.\n\n";
	$list .= "<u>Responding to Tells</u>\n";
	$list .= "Faction: <highlight>";
	if($this->settings["tell_req_faction"] == "all")
		$list .= "No Limit";
	else
		$list .= $this->settings["tell_req_faction"];
	$list .= "<end> (";
	$list .= bot::makeLink("Change this", "/tell <myname> limit tell faction", "chatcmd").")\n";
	$list .= "Level: <highlight>";
	if($this->settings["tell_req_lvl"] == 0)
		$list .= "No Limit";
	else
		$list .= $this->settings["tell_req_lvl"];
	$list .= "<end> (";
	$list .= bot::makeLink("Change this", "/tell <myname> limit tell minlvl", "chatcmd").")\n";
	$list .= "General: <highlight>";
	if($this->settings["tell_req_open"] == "all")
		$list .= "No general Limit";
	elseif($this->settings["tell_req_open"] == "org")
		$list .= "Responding only to Players that are in the Organistion {$this->vars["my guild"]}";
	else
		$list .= "Responding only to players that are Members of this Bot";
	$list .= "<end> (";
	$list .= bot::makeLink("Change this", "/tell <myname> limit tell open", "chatcmd").")\n";

	$list .= "\n<u>Privatgroup Invites</u>\n";
	$list .= "Faction: <highlight>{$this->settings["priv_req_faction"]}<end> (";
	$list .= bot::makeLink("Change this", "/tell <myname> limit priv faction", "chatcmd").")\n";
	$list .= "Level: <highlight>";
	if($this->settings["priv_req_lvl"] == 0)
		$list .= "No Limit";
	else
		$list .= $this->settings["priv_req_lvl"];
	$list .= "<end> (";
	$list .= bot::makeLink("Change this", "/tell <myname> limit priv minlvl", "chatcmd").")\n";
	$list .= "General: <highlight>";
	if($this->settings["priv_req_open"] == "all")
		$list .= "No general Limit";
	elseif($this->settings["priv_req_open"] == "org")
		$list .= "Accepting invites only from Members of the Organistion {$this->vars["my guild"]}";
	else
		$list .= "Accepting invites only from Members of this Bot";
	$list .= "<end> (";
	
	$list .= bot::makeLink("Change this", "/tell <myname> limit priv open", "chatcmd").")\n";
	$list .= "Player Limit: <highlight>";
	if($this->settings["priv_req_maxplayers"] == 0)
		$list .= "No Limit";
	else
		$list .= $this->settings["priv_req_maxplayers"];
	$list .= "<end> (";
	$list .= bot::makeLink("Change this", "/tell <myname> limit priv maxplayers", "chatcmd").")\n";

	$msg = bot::makeLink("Limits for privGroup and Tells", $list);
	bot::send($msg, $sender);
} elseif(preg_match("/^limit (priv|tell) faction$/i", $message, $arr)) {
 	$list .= "<header>::::: Faction Limit :::::<end>\n\n";
 	$list .= "Current Setting: <highlight>";
 	if($arr[1] == "priv") {
 	 	if($this->settings["priv_req_faction"] == "all")
			$list .= "No Limit";
		else
			$list .= $this->settings["priv_req_faction"];
 	} else {
 	 	if($this->settings["tell_req_faction"] == "all")
			$list .= "No Limit";
		else
			$list .= $this->settings["tell_req_faction"];
	}
	$list .= "<end>\n\nChange it to:\n";
	$list .= bot::makeLink("No Faction Limit", "/tell <myname> limit {$arr[1]} faction all", "chatcmd")."\n\n";	
	$list .= bot::makeLink("Omni only", "/tell <myname> limit {$arr[1]} faction omni", "chatcmd")."\n";
	$list .= bot::makeLink("Clan only", "/tell <myname> limit {$arr[1]} faction clan", "chatcmd")."\n";	
	$list .= bot::makeLink("Neutral only", "/tell <myname> limit {$arr[1]} faction neutral", "chatcmd")."\n\n";
	$list .= bot::makeLink("Not Clan", "/tell <myname> limit {$arr[1]} faction not clan", "chatcmd")."\n";
	$list .= bot::makeLink("Not Neutral", "/tell <myname> limit {$arr[1]} faction not neutral", "chatcmd")."\n";
	$list .= bot::makeLink("Not Omni", "/tell <myname> limit {$arr[1]} faction not omni", "chatcmd")."\n";
	$msg = bot::makeLink("Faction Limit", $list);
	bot::send($msg, $sender);
} elseif(preg_match("/^limit (priv|tell) faction (omni|clan|neutral|all)$/i", $message, $arr)) {
	$faction = ucfirst(strtolower($arr[2]));
	$channel = strtolower($arr[1]);
	
	if($channel == "priv")
		bot::savesetting("priv_req_faction", $faction);
	else
		bot::savesetting("tell_req_faction", $faction);
	
	if($channel == "priv" && $faction == "all") {
		$msg = "Faction limit removed from privategroup invites.";
	} elseif($channel == "priv") {
		$msg = "Privategroup Invites are accepted only from the Faction $faction.";
	} elseif($channel == "tell" && $faction == "all") {
		$msg = "Faction limit removed for tell responces.";
	} elseif($channel == "tell") {
 		$msg = "Responding on tells will be done only for players with the Faction $faction.";
 	}
 	bot::send($msg, $sender);
} elseif(preg_match("/^limit (priv|tell) faction not (omni|clan|neutral)$/i", $message, $arr)) {
	$faction = ucfirst(strtolower($arr[2]));
	$channel = strtolower($arr[1]);
	
	if($channel == "priv")
		bot::savesetting("priv_req_faction", "not ".$faction);
	else
		bot::savesetting("tell_req_faction", "not ".$faction);
	
	if($channel == "priv") {
		$msg = "Privategroup invites are accepted only from player that are not $faction.";
	} elseif($channel == "tell") {
 		$msg = "Responding on tells will be done for players that are not $faction.";
 	}
 	bot::send($msg, $sender);
} elseif(preg_match("/^limit (priv|tell) minlvl$/i", $message, $arr)) {
 	$list .= "<header>::::: Level Limit :::::<end>\n\n";
 	$list .= "Current Setting: <highlight>";
 	if($arr[1] == "priv") {
  	 	if($this->settings["priv_req_lvl"] == 0)
			$list .= "No Limit";
		else
			$list .= $this->settings["priv_req_lvl"];
 	} else {
  	 	if($this->settings["tell_req_lvl"] == 0)
			$list .= "No Limit";
		else
			$list .= $this->settings["tell_req_lvl"];
	}
	$list .= "<end>\n\nChange it to:\n";
	$list .= bot::makeLink("No Level limit", "/tell <myname> limit {$arr[1]} minlvl 0", "chatcmd")."\n\n";	
	for($i = 5; $i <= 220; $i+=5)
		$list .= bot::makeLink("Level limit $i", "/tell <myname> limit {$arr[1]} minlvl $i", "chatcmd")."\n";

	$msg = bot::makeLink("Level Limit", $list);
	bot::send($msg, $sender);
} elseif(preg_match("/^limit (priv|tell) minlvl ([0-9]+)$/i", $message, $arr)) {
	$minlvl = strtolower($arr[2]);
	$channel = strtolower($arr[1]);
	
	if($minlvl > 220 || $minlvl < 0) {
		$msg = "<red>Minimum Level can be only set between 1-220<end>";
		bot::send($msg, $sender);
		return;
	}
	
	if($channel == "priv")
		bot::savesetting("priv_req_lvl", $minlvl);
	else
		bot::savesetting("tell_req_lvl", $minlvl);
	
	if($channel == "priv" && $minlvl == 0) {
		$msg = "Player min level limit has been removed from private group invites.";
	} elseif($channel == "priv") {
		$msg = "Privategroup Invites are accepted from the level $minlvl and above.";
	} elseif($channel == "tell" && $minlvl == 0) {
		$msg = "Player min level limit has been removed from responding on tells.";
	} elseif($channel == "tell") {
 		$msg = "Responding on tells will be done for the Minimumlevel of $minlvl.";
 	}
 	bot::send($msg, $sender);
} elseif(preg_match("/^limit (priv|tell) open$/i", $message, $arr)) {
 	$list .= "<header>::::: General Limit :::::<end>\n\n";
 	$list .= "Current Setting: <highlight>";
 	if($arr[1] == "priv") {
 	 	if($this->settings["priv_req_open"] == "all")
			$list .= "No general Limit";
		elseif($this->settings["priv_req_open"] == "org")
			$list .= "Responding only to Players that are in the Organistion {$this->vars["my guild"]}";
		else
			$list .= "Responding only to players that are Members of this Bot";
 	} else {
		if($this->settings["tell_req_open"] == "all")
			$list .= "No general Limit";
		elseif($this->settings["tell_req_open"] == "org")
			$list .= "Responding only to Players that are in the Organistion {$this->vars["my guild"]}";
		else
			$list .= "Responding only to players that are Members of this Bot";
	}
	$list .= "<end>\n\nChange it to:\n";
	$list .= bot::makeLink("No General limit", "/tell <myname> limit {$arr[1]} open all", "chatcmd")."\n\n";
	$list .= bot::makeLink("Only for Members of your Organisation", "/tell <myname> limit {$arr[1]} open org", "chatcmd")."\n";
	$list .= bot::makeLink("Only for Members of the Bot", "/tell <myname> limit {$arr[1]} open members", "chatcmd")."\n\n";

	$msg = bot::makeLink("General Limit", $list);
	bot::send($msg, $sender);
} elseif(preg_match("/^limit (priv|tell) open (all|org|members)$/i", $message, $arr)) {
	$open = strtolower($arr[2]);
	$channel = strtolower($arr[1]);
	
	if($channel == "priv")
		bot::savesetting("priv_req_open", $open);
	else
		bot::savesetting("tell_req_open", $open);
	
	if($channel == "priv" && $open == "all") {
		$msg = "General restrictions for privategroup invites has been removed.";
	} elseif($channel == "priv" && $open == "org") {
		$msg = "Privategroup invites will be accepted only from Members of your Organisation";
	} elseif($channel == "priv" && $open == "members") {
		$msg = "Privategroup Invites will be accepted only from Members of this Bot";
	} elseif($channel == "tell" && $open == "all") {
		$msg = "General restriction for responding on tells has been removed.";
	} elseif($channel == "tell" && $open == "org") {
 		$msg = "Responding on tells will be done only for Members of your Organisation.";
 	} elseif($channel == "tell" && $open == "members") {
 		$msg = "Responding on tells will be done only for Members of this Bot.";
 	}
 	bot::send($msg, $sender);
} elseif(preg_match("/^limit priv maxplayers$/i", $message, $arr)) {
 	$list .= "<header>::::: Limit of Players in the Bot :::::<end>\n\n";
 	$list .= "Current Setting: <highlight>";
	if($this->settings["priv_req_maxplayers"] == 0)
		$list .= "No Limit";
	else
		$list .= $this->settings["priv_req_maxplayers"];

	$list .= "<end>\n\nChange it to:\n";
	$list .= bot::makeLink("No Limit of Players", "/tell <myname> limit priv maxplayers 0", "chatcmd")."\n\n";	
	for($i = 6; $i <= 120; $i+=6)
		$list .= bot::makeLink("Set Maximum allowed Players in the Bot to $i", "/tell <myname> limit priv maxplayers $i", "chatcmd")."\n";

	$msg = bot::makeLink("Limit of Players in the Bot", $list);
	bot::send($msg, $sender);
} elseif(preg_match("/^limit priv maxplayers ([0-9]+)$/i", $message, $arr)) {
	$maxplayers = strtolower($arr[1]);
	
	if($maxplayers > 120) {
		$msg = "<red>Maximum allowed players can be set only to lower then 120<end>";
		bot::send($msg, $sender);
		return;
	}
	
	bot::savesetting("priv_req_maxplayers", $maxplayers);
	
	if($maxplayers == 0) {
		$msg = "The Limit of the Amount of players in the privategroup has been removed.";
	} else {
		$msg = "The Limit of the Amount of players in the privategroup has been set to $maxplayers.";
	} 
 	bot::send($msg, $sender);
} else
	$syntax_error = true;
?>