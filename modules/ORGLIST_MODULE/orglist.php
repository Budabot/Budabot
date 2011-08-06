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

if ($message && !preg_match("/^orglist (.+)$/i", $message)) {
	$syntax_error = true;
	return;
}

if (!function_exists('orgmatesformat')){
	function orgmatesformat ($memberlist, $map, $color, $timestart, $orgname) {
		global $chatBot;
	
		$totalonline = 0;
		$totalcount = count($memberlist["result"]);
		forEach ($memberlist["result"] as $amember) {
			$newlist[$amember["rank_id"]][] = $amember["name"];
		}
		
		for ($rankid = 0; $rankid < count($map[$memberlist["orgtype"]]); $rankid++) {
			$onlinelist = "";
			$offlinelist = "";
			$rank_online = 0;
			$rank_total = count($newlist[$rankid]);
			
			sort($newlist[$rankid]);
			for ($i = 0; $i < $rank_total; $i++) {
				if ($memberlist["result"][$newlist[$rankid][$i]]["online"]) {
					$rank_online++;
					$onlinelist .= "  " . $memberlist["result"][$newlist[$rankid][$i]]["post"] . "\n";
				} else {
					$offlinelist .= $newlist[$rankid][$i] . ", <pagebreak>";
				}
			}
			
			$totalonline += $rank_online;
							
			$blob .=  "\n" . $color["header"] . $map[$memberlist["orgtype"]][$rankid] . "</font> ";
			$blob .=  "(" . $color["onlineH"] . "{$rank_online}</font> of " . $color["onlineH"] . "{$rank_total}</font>)\n";

			if ($onlinelist) {
				$blob .= $onlinelist;
			}
			if ($offlinelist) {
				$blob .= $color["offline"] . $offlinelist . "</font>\n";
			}
		}
		
		$totaltime = time() - $timestart;
		$header  = $color["onlineH"].$orgname."<end> has ";
		$header .= $color["onlineH"]."$totalonline</font> online out of a total of ".$color["onlineH"]."$totalcount</font> members. ";
		$header .= "(".$color["onlineH"]."$totaltime</font> seconds)\n";
		$blob = $header.$blob;
		
		return $blob;
	}
}

// Some rankings (Will be used to help distinguish which org type is used.)
$orgrankmap["Anarchism"]  = array("Anarchist");
$orgrankmap["Monarchy"]   = array("Monarch",   "Counsel",      "Follower");
$orgrankmap["Feudalism"]  = array("Lord",      "Knight",       "Vassal",          "Peasant");
$orgrankmap["Republic"]   = array("President", "Advisor",      "Veteran",         "Member",         "Applicant");
$orgrankmap["Faction"]    = array("Director",  "Board Member", "Executive",       "Member",         "Applicant");
$orgrankmap["Department"] = array("President", "General",      "Squad Commander", "Unit Commander", "Unit Leader", "Unit Member", "Applicant");

// Don't want to reboot to see changes in color edits, so I'll store them in an array outside the function.
$orgcolor["header"]  = "<font color='#FFFFFF'>";   // Org Rank title
$orgcolor["onlineH"] = "<highlight>";              // Highlights on whois info
$orgcolor["offline"] = "<font color='#555555'>";   // Offline names

$end = false;
if (preg_match("/^orglist end$/i", $message)) {
	$end = true;
} else if (preg_match("/^orglist (.+)$/i", $message, $arr)) {
	// Check if we are already doing a list.
	if ($chatBot->data["ORGLIST_MODULE"]["start"]) {
		$msg = "I'm already doing a list!";
		$chatBot->send($msg, $sendto);
		return;
	} else if (990 <= count($chatBot->buddyList)) {
		// using the ao chatbot proxy this is no longer an issue
		//$msg = "No room on the buddy-list!";
		//$chatBot->send($msg, $sendto);
		//unset($chatBot->data["ORGLIST_MODULE"]);
		//return;
	}
	
	$chatBot->data["ORGLIST_MODULE"]["start"] = time();
	$chatBot->data["ORGLIST_MODULE"]["sendto"] = $sendto;

	if (!ctype_digit($arr[1])) {
		// Someone's name.  Doing a whois to get an orgID.
		$name = ucfirst(strtolower($arr[1]));
		$whois = Player::get_by_name($name);

		if ($whois === null) {
			$msg = "Could not find character info for $name.";
			unset($whois);
			$chatBot->send($msg, $sendto);
			unset($chatBot->data["ORGLIST_MODULE"]);
			return;
		} else if (!$whois->guild_id) {
			$msg = "Player <highlight>$name<end> does not seem to be in any org?";
			unset($whois);
			$chatBot->send($msg, $sendto);
			unset($chatBot->data["ORGLIST_MODULE"]);
			return;
		} else {
			$orgid = $whois->guild_id;
		}
	} else {
		// assume org id
		$orgid = $arr[1];
	}
	
	$chatBot->send("Downloading org list for org id $orgid...", $sendto);

	$org = Guild::get_by_id($orgid);

	if ($org === null) {
		$msg = "Error in getting the Org info. Either org does not exist or AO's server was too slow to respond.";
		$chatBot->send($msg, $sendto);
		unset($chatBot->data["ORGLIST_MODULE"]);
		return;
	}
	
	$chatBot->data["ORGLIST_MODULE"]["org"] = $org->orgname;
	
	$chatBot->send("Parsing xml for '$org->orgname'...", $sendto);
	
	// Check each name if they are already on the buddylist (and get online status now)
	// Or make note of the name so we can add it to the buddylist later.
	forEach ($org->members as $member) {
		// Writing the whois info for all names
		// Name (Level 1/1, Sex Breed Profession)
		$thismember  = '<highlight>'.$member->name.'<end>';
		$thismember .= ' (Level '.$orgcolor["onlineH"].$member->level."<end>";
		if ($member->ai_level > 0) {
			$thismember .= "<green>/".$member->ai_level."<end>";
		}
		$thismember .= ", ".$member->gender;
		$thismember .= " ".$member->breed;
		$thismember .= " ".$orgcolor["onlineH"].$member->profession."<end>)";
		
		$chatBot->data["ORGLIST_MODULE"]["result"][$member->name]["post"] = $thismember;

		$chatBot->data["ORGLIST_MODULE"]["result"][$member->name]["name"] = $member->name;
		$chatBot->data["ORGLIST_MODULE"]["result"][$member->name]["rank_id"] = $member->guild_rank_id;

		// If we havent found an org type yet, check this member if they have a unique rank.
		if (!$chatBot->data["ORGLIST_MODULE"]["orgtype"]) {
			if (($member->guild_rank_id == 0 && $member->guild_rank == "President") ||
				($member->guild_rank_id == 3 && $member->guild_rank == "Member") ||
				($member->guild_rank_id == 4 && $member->guild_rank == "Applicant")) {
				// Dont do anything. Can't do a match cause this rank is in multiple orgtypes.
			} else if ($member->guild_rank == $orgrankmap["Anarchism"][$member->guild_rank_id]) {
				$chatBot->data["ORGLIST_MODULE"]["orgtype"]= "Anarchism";
			} else if ($member->guild_rank == $orgrankmap["Monarchy"][$member->guild_rank_id]) {
				$chatBot->data["ORGLIST_MODULE"]["orgtype"]= "Monarchy";
			} else if ($member->guild_rank == $orgrankmap["Feudalism"][$member->guild_rank_id]) {
				$chatBot->data["ORGLIST_MODULE"]["orgtype"]= "Feudalism";
			} else if ($member->guild_rank == $orgrankmap["Republic"][$member->guild_rank_id]) {
				$chatBot->data["ORGLIST_MODULE"]["orgtype"]= "Republic";
			} else if ($member->guild_rank == $orgrankmap["Faction"][$member->guild_rank_id]) {
				$chatBot->data["ORGLIST_MODULE"]["orgtype"]= "Faction";
			} else if ($member->guild_rank == $orgrankmap["Department"][$member->guild_rank_id]) {
				$chatBot->data["ORGLIST_MODULE"]["orgtype"]= "Department";
			}
		}
		
		$buddy_online_status = Buddylist::is_online($member->name);
		if ($buddy_online_status !== null) {
			$chatBot->data["ORGLIST_MODULE"]["result"][$member->name]["online"] = $buddy_online_status;
		} else if ($chatBot->vars["name"] != $member->name) { // If the name being checked ISNT the bot.
			// check if they exist
			if ($chatBot->get_uid($member->name)) {
				$chatBot->data["ORGLIST_MODULE"]["check"][$member->name] = 1;
			}
		} else if ($chatBot->vars["name"] == $member->name) { // Yes, this bot is online. Don't need a buddylist to tell me.
			$chatBot->data["ORGLIST_MODULE"]["result"][$member->name]["online"] = 1;
		}
	}
	
	$chatBot->send("Checking online status for " . count($org->members) ." members of '$org->orgname'...", $sendto);

	// prime the list and get things rolling by adding some buddies
	$i = 0;
	forEach ($chatBot->data["ORGLIST_MODULE"]["check"] as $name => $value) {
		$chatBot->data["ORGLIST_MODULE"]["added"][$name] = 1;
		unset($chatBot->data["ORGLIST_MODULE"]["check"][$name]);
		Buddylist::add($name, 'onlineorg');
		if (++$i == 10) {
			break;
		}
	}

	if (!$chatBot->data["ORGLIST_MODULE"]["orgtype"] && !$msg) {
		// If we haven't found the org yet, it can only be
		// Department or Republic with only a president.
		$chatBot->data["ORGLIST_MODULE"]["orgtype"] = "Republic";
	}

	unset($org);

// If we added names to the buddylist, this will kick in to determine if they are online or not.
// If no more names need to be checked, then post results.
} else if (($type == "logOn" || $type == "logOff") && isset($chatBot->data["ORGLIST_MODULE"]["added"][$sender])) {

	if ($type == "logOn") {
		$chatBot->data["ORGLIST_MODULE"]["result"][$sender]["online"] = 1;
	} else if ($type == "logOff") {
		$chatBot->data["ORGLIST_MODULE"]["result"][$sender]["online"] = 0;
	}

	Buddylist::remove($sender, 'onlineorg');
	unset($chatBot->data["ORGLIST_MODULE"]["added"][$sender]);
	
	forEach ($chatBot->data["ORGLIST_MODULE"]["check"] as $name => $value) {
		$chatBot->data["ORGLIST_MODULE"]["added"][$name] = 1;
		unset($chatBot->data["ORGLIST_MODULE"]["check"][$name]);
		Buddylist::add($name, 'onlineorg');
		break;
	}
}

if (isset($chatBot->data["ORGLIST_MODULE"]) && count($chatBot->data["ORGLIST_MODULE"]["added"]) == 0 || $end) {
	$blob = orgmatesformat($chatBot->data["ORGLIST_MODULE"], $orgrankmap, $orgcolor, $chatBot->data["ORGLIST_MODULE"]["start"], $chatBot->data["ORGLIST_MODULE"]["org"]);
	$msg = Text::make_link("Orglist for '".$chatBot->data["ORGLIST_MODULE"]["org"]."'", $blob, 'blob');
	$chatBot->send($msg, $chatBot->data["ORGLIST_MODULE"]["sendto"]);

	// in case it was ended early
	forEach ($chatBot->data["ORGLIST_MODULE"]["added"] as $name => $value) {
		Buddylist::remove($name, 'onlineorg');
	}
	unset($chatBot->data["ORGLIST_MODULE"]);
}

?>
