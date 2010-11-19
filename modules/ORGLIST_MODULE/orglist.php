<?php
   /*
   ** Author: Lucier (RK1)
   ** Description: Checks who from an org is online
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 05.03.2008
   ** Date(last modified): 05.03.2008
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

// Hate doing functions in plugins, but it's necessary
// because this is called in 2 completely different sections.

if (!function_exists(orgmatesformat)){
	function orgmatesformat ($memberlist, $map, $color, $timestart, $orgname) {
	
		$totalonline = 0;
		$totalcount = 0;
		foreach($memberlist["result"] as $amember) {
			$newlist[$amember["rank_id"]][] = $amember["name"];
		}
		
		for ($rankid=0; $rankid < count($map[$memberlist["orgtype"]]); $rankid++) {
			$onlinelist = "";
			$offlinelist = "";
			$sectonline=0;
			
			for ($i=0; $i<count($newlist[$rankid]); $i++) {
				sort($newlist[$rankid]);
				if ($memberlist["result"][$newlist[$rankid][$i]]["online"]) {
					$sectonline++;
					$onlinelist .= "  ".$memberlist["result"][$newlist[$rankid][$i]]["post"]."\n";

				} else {
					$offlinelist .= $newlist[$rankid][$i].", ";
				}
			}
			
			if (strlen($offlinelist) > 2) {
				$offlinelist = substr($offlinelist,0,-2).".";
			}
			$totalcount += count($newlist[$rankid]);
			$onlinecount += $sectonline;
							
			$fullist .=  "\n".$color["header"].$map[$memberlist["orgtype"]][$rankid]."<end> ";
			$fullist .=  "(".$color["onlineH"]."$sectonline</font> of ".$color["onlineH"].count($newlist[$rankid])."</font>)\n";

			if ($onlinelist) {
				$fullist .= $onlinelist;
			}
			if ($offlinelist) {
				$fullist .= $color["offline"].$offlinelist."</font>\n";
			}
		}
		$totaltime = time()-$timestart;
		$header  = $color["onlineH"].$orgname."<end> has ";
		$header .= $color["onlineH"]."$onlinecount</font> online out of a total of ".$color["onlineH"]."$totalcount</font> members. ";
		$header .= "(".$color["onlineH"]."$totaltime</font> seconds.)\n";
		$fullist = $header.$fullist;
		
		return $fullist;
		unset($newlist);
	}
}




// Some globals we are using for this plugin
// $this->data["ORGLIST_MODULE"]["check"][page]	// list of each name that still needs to be checked. (in groups)
// $this->data["ORGLIST_MODULE"]["result"] 	// list of names that have completed thier check.
// $this->data["ORGLIST_MODULE"]["sendto"]	// who gets this info?  org, prv, or a user?
// $this->data["ORGLIST_MODULE"]["org"]		// org name
// $this->data["ORGLIST_MODULE"]["start"]     	// time when the search started

// Some rankings (Will be used to help distinguish which org type is used.)
$orgrankmap["Anarchism"]  = array("Anarchist");
$orgrankmap["Monarchy"]   = array("Monarch",   "Counsel",      "Follower");
$orgrankmap["Feudalism"]  = array("Lord",      "Knight",       "Vassal",          "Peasant");
$orgrankmap["Republic"]   = array("President", "Advisor",      "Veteran",         "Member",         "Applicant");
$orgrankmap["Faction"]    = array("Director",  "Board Member", "Executive",       "Member",         "Applicant");
$orgrankmap["Department"] = array("President", "General",      "Squad Commander", "Unit Commander", "Unit Leader", "Unit Member", "Applicant");

// Don't want to reboot to see changes in color edits, so I'll store them in an array outside the function.
$orgcolor["header"]  = "<font color=#FFFFFF>";		// Org Rank title
$orgcolor["onlineH"] = "<highlight>";			// Highlights on whois info
$orgcolor["offline"] = "<font color=#555555>";		// Offline names

// No options? Target the $sender
if (preg_match("/^(orglist|onlineorg)$/i", $message, $arr)) {$message = "orglist $sender";}

$end = false;
if (preg_match("/^(orglist|onlineorg) end$/i", $message, $arr)) {
	$end = true;
} else if (preg_match("/^(orglist|onlineorg) (.+)$/i", $message, $arr)) {
	// Now we hopefully have either an org memeber, or org ID.

	// Check if we are already doing a list.
	if ($this->data["ORGLIST_MODULE"]["start"]) {
		$msg = "I'm already doing a list!";
		$this->send($msg, $sendto);
		return;
	} else if (995 <= count($this->buddyList)) {
		$msg = "No room on the buddy-list!";
		$this->send($msg, $sendto);
		unset($this->data["ORGLIST_MODULE"]);
		return;
	} else {
		$this->data["ORGLIST_MODULE"]["start"] = time();
		$this->data["ORGLIST_MODULE"]["sendto"] = $sendto;
	}

	if (!ctype_digit($arr[2])) {
		// Someone's name.  Doing a whois to get an orgID.
		$name = ucfirst(strtolower($arr[2]));
		$whois = new whois($name);
		$orgid = $whois->org_id;

		if (!$whois->name) {
			$msg = "Player <highlight>$name<end> does not exist on this dimension.";
			unset($whois);
			$this->send($msg, $sendto);
			unset($this->data["ORGLIST_MODULE"]);
			return;
		} elseif (!$orgid) {
			$msg = "Player <highlight>$name<end> does not seem to be in any org?";
			unset($whois);
			$this->send($msg, $sendto);
			unset($this->data["ORGLIST_MODULE"]);
			return;
		}
	} else {
		// We got only numbers, can't be a name.  Maybe org id?
		$orgid = $arr[2];
	}
	
	$this->send("Searching and reading org list for org id $orgid...", $sendto);

	$orgmate = new org($orgid);

	if($orgmate->errorCode != 0) {
		$msg = "Error in getting the Org info. Either org does not exist or AO's server was too slow to respond.";
		$this->send($msg, $sendto);
		unset($this->data["ORGLIST_MODULE"]);
		return;
	}
	
	$this->data["ORGLIST_MODULE"]["org"] = $orgmate->orgname;
	
	$this->send("Checking online status for '$orgmate->orgname'...", $sendto);
	
	// Check each name if they are already on the buddylist (and get online status now)
	// Or make note of the name so we can add it to the buddylist later.
	forEach ($orgmate->member as $amember) {
		// Writing the whois info for all names
		// Name (Level 1/1, Sex Breed Profession)
		$thismember  = '<highlight>'.$orgmate->members[$amember]["name"].'<end>';
		$thismember .= ' (Level '.$orgcolor["onlineH"].$orgmate->members[$amember]["level"]."<end>";
		if ($orgmate->members[$amember]["ai_level"] > 0) { $thismember .= "<green>/".$orgmate->members[$amember]["ai_level"]."<end>";}
		$thismember .= ", ".$orgmate->members[$amember]["gender"];
		$thismember .= " ".$orgmate->members[$amember]["breed"];
		$thismember .= " ".$orgcolor["onlineH"].$orgmate->members[$amember]["profession"]."<end>)";
		
		$this->data["ORGLIST_MODULE"]["result"][$amember]["post"] = $thismember;

		$this->data["ORGLIST_MODULE"]["result"][$amember]["name"] = $amember;
		$this->data["ORGLIST_MODULE"]["result"][$amember]["rank_id"] = $orgmate->members[$amember]["rank_id"];

		// If we havent found an org type yet, check this member if they have a unique rank.
		if (!$this->data["ORGLIST_MODULE"]["orgtype"]) {

			if (($orgmate->members[$amember]["rank_id"] == 0 && $orgmate->members[$amember]["rank"] == "President") ||
				($orgmate->members[$amember]["rank_id"] == 3 && $orgmate->members[$amember]["rank"] == "Member") ||
				($orgmate->members[$amember]["rank_id"] == 4 && $orgmate->members[$amember]["rank"] == "Applicant")) {
				// Dont do anything. Can't do a match cause this rank is in multiple orgtypes.
			} else if ($orgmate->members[$amember]["rank"] == $orgrankmap["Anarchism"][$orgmate->members[$amember]["rank_id"]]) {
				$this->data["ORGLIST_MODULE"]["orgtype"]= "Anarchism";
			} else if ($orgmate->members[$amember]["rank"] == $orgrankmap["Monarchy"][$orgmate->members[$amember]["rank_id"]]) {
				$this->data["ORGLIST_MODULE"]["orgtype"]= "Monarchy";
			} else if ($orgmate->members[$amember]["rank"] == $orgrankmap["Feudalism"][$orgmate->members[$amember]["rank_id"]]) {
				$this->data["ORGLIST_MODULE"]["orgtype"]= "Feudalism";
			} else if ($orgmate->members[$amember]["rank"] == $orgrankmap["Republic"][$orgmate->members[$amember]["rank_id"]]) {
				$this->data["ORGLIST_MODULE"]["orgtype"]= "Republic";
			} else if ($orgmate->members[$amember]["rank"] == $orgrankmap["Faction"][$orgmate->members[$amember]["rank_id"]]) {
				$this->data["ORGLIST_MODULE"]["orgtype"]= "Faction";
			} else if ($orgmate->members[$amember]["rank"] == $orgrankmap["Department"][$orgmate->members[$amember]["rank_id"]]) {
				$this->data["ORGLIST_MODULE"]["orgtype"]= "Department";
			}
		}
		
		$buddy_online_status = $this->buddy_online($amember);
		if ($buddy_online_status !== null) {
			$this->data["ORGLIST_MODULE"]["result"][$amember]["online"] = $buddy_online_status;
		} else if ($this->vars["name"] != $amember) { // If the name being checked ISNT the bot.
			// check if they exist
			if (AoChat::get_uid($amember)) {
				$this->data["ORGLIST_MODULE"]["check"][$amember] = 1;
			}
		} else if ($this->vars["name"] == $amember) { // Yes, this bot is online. Don't need a buddylist to tell me.
			$this->data["ORGLIST_MODULE"]["result"][$amember]["online"] = 1;
		}
	}
	
	// add five members to the buddy list to prime the list and get things rolling
	$i = 0;
	forEach ($this->data["ORGLIST_MODULE"]["check"] as $name => $value) {
		$this->add_buddy($name, 'onlineorg');
		if (++$i == 5) {
			break;
		}
	}

	if (!$this->data["ORGLIST_MODULE"]["orgtype"] && !$msg) {
		// If we haven't found the org yet, it can only be
		// Department or Republic with only a president.
		$this->data["ORGLIST_MODULE"]["orgtype"] = "Republic";
	}

	unset($orgmate);

// If we added names to the buddylist, this will kick in to determine if they are online or not.
// If no more names need to be checked, then post results.
} else if (($type == "logOn" || $type == "logOff") && isset($this->data["ORGLIST_MODULE"]["check"][$sender])) {

	if ($type == "logOn") {
		$this->data["ORGLIST_MODULE"]["result"][$sender]["online"] = 1;
	} else if ($type == "logOff") {
		$this->data["ORGLIST_MODULE"]["result"][$sender]["online"] = 0;
	}

	$this->remove_buddy($sender, 'onlineorg');
	unset($this->data["ORGLIST_MODULE"]["check"][$sender]);
	
	forEach ($this->data["ORGLIST_MODULE"]["check"] as $name => $value) {
		$this->add_buddy($name, 'onlineorg');
		break;
	}
}

if (isset($this->data["ORGLIST_MODULE"]) && count($this->data["ORGLIST_MODULE"]["check"]) == 0 || $end) {
	$msg = orgmatesformat($this->data["ORGLIST_MODULE"], $orgrankmap, $orgcolor, $this->data["ORGLIST_MODULE"]["start"],$this->data["ORGLIST_MODULE"]["org"]);
	$msg = bot::makeLink("Orglist for '".$this->data["ORGLIST_MODULE"]["org"]."'", $msg);
	bot::send($msg, $this->data["ORGLIST_MODULE"]["sendto"]);

	// in case it was ended early
	forEach ($this->data["ORGLIST_MODULE"]["check"] as $name => $value) {
		$this->remove_buddy($name, 'onlineorg');
	}
	unset($this->data["ORGLIST_MODULE"]);
}

?>
