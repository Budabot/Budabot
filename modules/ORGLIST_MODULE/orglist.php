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
// $this->vars["orglist_module"]["check"][page]	// list of each name that still needs to be checked. (in groups)
// $this->vars["orglist_module"]["result"] 	// list of names that have completed thier check.
// $this->vars["orglist_module"]["target"]	// who gets this info?  org, prv, or a user?
// $this->vars["orglist_module"]["org"]		// org name
// $this->vars["orglist_module"]["start"]     	// time when the search started
// $this->vars["orglist_module"]["markpage"]  	// current page being read. (names are sent out in blocks to buddylist)

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
if(preg_match("/^(orglist|onlineorg)$/i", $message, $arr)) {$message = "orglist $sender";}

// Now we hopefully have either an org memeber, or org ID.
if(preg_match("/^(orglist|onlineorg) (.+)$/i", $message, $arr)) {

	// Check if we are already doing a list.
	if ($this->vars["orglist_module"]["start"]) {
		$msg = "I'm already doing a list!";
	} else {
		$this->vars["orglist_module"]["start"] = time();
	}

	if (!ctype_digit($arr[2]) && !$msg) {
		// Someone's name.  Doing a whois to get an orgID.
		$name = ucfirst(strtolower($arr[2]));
		$whois = new whois($name);
		$orgid = $whois->org_id;

		if (!$whois->name) {
			$msg = "Player <highlight>$name<end> does not exist on this dimension.";
		} elseif (!$orgid) {
			$msg = "Player <highlight>$name<end> does not seem to be in any org?";
		}
		unset($whois);

	} elseif (!$msg) {
		// We got only numbers, can't be a name.  Maybe org id?
		$orgid = $arr[1];
	}
	
	if (!$msg) {  // Checking if we can get info on this org.
        bot::send("Searching and reading org list....", $sendto);
	
		$orgmate = new org($orgid);

		if($orgmate->errorCode != 0) {
			$msg = "Error in getting the Org info. Either org does not exist or AO's server was too slow to respond.";
		}
	}
	
	if (!$msg) {  // Org ID checked out ok, continue
	
		$this->vars["orglist_module"]["org"]	= $orgmate->orgname;
		
		// Figure out max room on buddylist now
		$pagemax = 995-count($this->buddyList); // (adding some wiggle room)
		$thispage = 0;
		$thisname = 0;
		
		// Check each name if they are already on the buddylist (and get online status now)
		// Or make note of the name so we can add it to the buddylist later.
		foreach($orgmate->member as $amember) {
			if(bot::send("isbuddy", $amember)) {
				$this->vars["orglist_module"]["result"][$amember]["online"] = $this->buddyList[$amember];
			} elseif ($this->vars["name"] != $amember) { // If the name being checked ISNT the bot.
				// check if they exist, (They might be deleted)
				if (AoChat::get_uid($amember)) {
					if ($pagemax < 5) {
						$msg = "No room on the buddy-list!";
						break;
					}
					
					$this->vars["orglist_module"]["check"][$thispage][$thisname]=$amember;
					$thisname++;
					if ($thisname >= $pagemax) { $thisname = 0; $thispage++;}
				}
				
			} elseif ($this->vars["name"] == $amember) { // Yes, this bot is online. Don't need a buddylist to tell me.
				$this->vars["orglist_module"]["result"][$amember]["online"] = 1;
			}

			// Writing the whois info for all names
			// Name (Level 1/1, Sex Breed Profession)
			$thismember  = '<highlight>'.$orgmate->members[$amember]["name"].'<end>';
			$thismember .= ' (Level '.$orgcolor["onlineH"].$orgmate->members[$amember]["level"]."<end>";
			if ($orgmate->members[$amember]["ai_level"] > 0) { $thismember .= "<green>/".$orgmate->members[$amember]["ai_level"]."<end>";}
			$thismember .= ", ".$orgmate->members[$amember]["gender"];
			$thismember .= " ".$orgmate->members[$amember]["breed"];
			$thismember .= " ".$orgcolor["onlineH"].$orgmate->members[$amember]["profession"]."<end>)";
			
			$this->vars["orglist_module"]["result"][$amember]["post"] = $thismember;

			$this->vars["orglist_module"]["result"][$amember]["name"] = $amember;
			$this->vars["orglist_module"]["result"][$amember]["rank_id"] = $orgmate->members[$amember]["rank_id"];

			// If we havent found an org type yet, check this member if they have a unique rank.
			if (!$this->vars["orglist_module"]["orgtype"]) {

				if (($orgmate->members[$amember]["rank_id"] == 0 && $orgmate->members[$amember]["rank"] == "President") ||
				    ($orgmate->members[$amember]["rank_id"] == 3 && $orgmate->members[$amember]["rank"] == "Member") ||
				    ($orgmate->members[$amember]["rank_id"] == 4 && $orgmate->members[$amember]["rank"] == "Applicant")) {
					// Dont do anything. Can't do a match cause this rank is in multiple orgtypes.
				} elseif ($orgmate->members[$amember]["rank"] == $orgrankmap["Anarchism"][$orgmate->members[$amember]["rank_id"]]) {
					$this->vars["orglist_module"]["orgtype"]= "Anarchism";
				} elseif ($orgmate->members[$amember]["rank"] == $orgrankmap["Monarchy"][$orgmate->members[$amember]["rank_id"]]) {
					$this->vars["orglist_module"]["orgtype"]= "Monarchy";
				} elseif ($orgmate->members[$amember]["rank"] == $orgrankmap["Feudalism"][$orgmate->members[$amember]["rank_id"]]) {
					$this->vars["orglist_module"]["orgtype"]= "Feudalism";
				} elseif ($orgmate->members[$amember]["rank"] == $orgrankmap["Republic"][$orgmate->members[$amember]["rank_id"]]) {
					$this->vars["orglist_module"]["orgtype"]= "Republic";
				} elseif ($orgmate->members[$amember]["rank"] == $orgrankmap["Faction"][$orgmate->members[$amember]["rank_id"]]) {
					$this->vars["orglist_module"]["orgtype"]= "Faction";
				} elseif ($orgmate->members[$amember]["rank"] == $orgrankmap["Department"][$orgmate->members[$amember]["rank_id"]]) {
					$this->vars["orglist_module"]["orgtype"]= "Department";
				}
			}
		}

		if (!$this->vars["orglist_module"]["orgtype"] && !$msg) {
			// If we haven't found the org yet, it can only be
			// Department or Republic with only a president.
			$this->vars["orglist_module"]["orgtype"] = "Republic";
		}

		unset($orgmate);

		// If we didn't have to add people to the buddylist, then post results now
		if (!$this->vars["orglist_module"]["check"] && !$msg) {
			// Everyone was already on the buddylist, so we are done.
			$msg = orgmatesformat($this->vars["orglist_module"], $orgrankmap, $orgcolor, $this->vars["orglist_module"]["start"],$this->vars["orglist_module"]["org"]);
			$msg = bot::makeLink("Orglist for '".$this->vars["orglist_module"]["org"]."'", $msg);
		} elseif (!$msg) {

			// We have people we need to plug into the buddylist,
			// then remove after we get thier online status.
			$msg = "Now checking online status....";
			if      ($type == "msg")   {$this->vars["orglist_module"]["target"] = $sender;}
			elseif  ($type == "guild") {$this->vars["orglist_module"]["target"] = "org";}
			elseif  ($type == "priv")  {$this->vars["orglist_module"]["target"] = "prv";}
			
			$i = 0;
			$this->vars["orglist_module"]["markpage"] = 0;

			while ($this->vars["orglist_module"]["check"][0][$i]) {
				bot::send("addbuddy", $this->vars["orglist_module"]["check"][0][$i]);
				bot::send("rembuddy", $sender);
				$i++;
			}

			$this->vars["orglist_module"]["marker"] = $i;
		}
	}



	if($msg) {
		// Send info back
		bot::send($msg, $sendto);
	}

	// If we arent plugging names into the buddylist, then we are done.
	if (!$this->vars["orglist_module"]["check"]) {unset($this->vars["orglist_module"]);}






// If we added names to the buddylist, this will kick in to determine if they are online or not.
// If no more names need to be checked, then post results.
} elseif (($type == "logOn") || ($type == "logOff")) {

	$page = $this->vars["orglist_module"]["markpage"];
	
	//If $sender is marked in the list, get status and remove from buddylist.
	if (($key = array_search($sender, $this->vars["orglist_module"]["check"][$page])) !== false) {
		$this->vars["orglist_module"]["result"][$sender]["online"] = $this->buddyList[$sender];
		unset($this->vars["orglist_module"]["check"][$page][$key]);
		if (current($this->vars["orglist_module"]["check"][$page]) === false) {
			$page++; $this->vars["orglist_module"]["markpage"]++;
			
			if (current($this->vars["orglist_module"]["check"][$page]) !== false) {
				
				$i = 0;
				while ($this->vars["orglist_module"]["check"][$page][$i]) {
					bot::send("addbuddy", $this->vars["orglist_module"]["check"][$page][$i]);
					bot::send("rembuddy", $sender);
					$i++;
				}
			} else {
				$msg = orgmatesformat($this->vars["orglist_module"], $orgrankmap, $orgcolor, $this->vars["orglist_module"]["start"],$this->vars["orglist_module"]["org"]);
				$msg = bot::makeLink("Orglist for '".$this->vars["orglist_module"]["org"]."'", $msg);
			
				if     ($this->vars["orglist_module"]["target"] == "org") {bot::send($msg, "guild");}
				elseif ($this->vars["orglist_module"]["target"] == "prv") {bot::send($msg);}
				else   {bot::send($msg, $this->vars["orglist_module"]["target"]);}
			
				unset($this->vars["orglist_module"]);
			}
		}
	}
}
?>
