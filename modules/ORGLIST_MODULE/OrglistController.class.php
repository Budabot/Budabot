<?php
/**
 * Authors: 
 *	- Tyrence (RK2)
 *  - Lucier (RK1)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'orglist',
 *		accessLevel = 'guild',
 *		description = 'Check an org roster',
 *		help        = 'orglist.txt'
 *	)
 */
class OrglistController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $db;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $buddylistManager;
	
	/** @Inject */
	public $guildManager;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $playerManager;
	
	/**
	 * @HandlesCommand("orglist")
	 * @Matches("/^orglist end$/i")
	 */
	public function orglistEndCommand($message, $channel, $sender, $sendto, $args) {
		$this->checkOrglistEnd(true);
	}
	
	/**
	 * @HandlesCommand("orglist")
	 * @Matches("/^orglist (.+)$/i")
	 */
	public function orglistCommand($message, $channel, $sender, $sendto, $args) {
		// Check if we are already doing a list.
		if ($this->orglist["start"]) {
			$msg = "I'm already doing a list!";
			$sendto->reply($msg);
			return;
		} else if (990 <= count($this->buddylistManager->buddyList)) {
			// using the ao chatbot proxy this is no longer an issue
			//$msg = "No room on the buddy-list!";
			//$sendto->reply($msg);
			//unset($this->orglist);
			//return;
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

		$this->orglist["start"] = time();
		$this->orglist["sendto"] = $sendto;

		if (preg_match("/^[0-9]+$/", $args[1])) {
			$orgid = $args[1];
		} else {
			// Someone's name.  Doing a whois to get an orgID.
			$name = ucfirst(strtolower($args[1]));
			$whois = $this->playerManager->get_by_name($name);

			if ($whois === null) {
				$msg = "Could not find character info for $name.";
				unset($whois);
				$sendto->reply($msg);
				unset($this->orglist);
				return;
			} else if ($whois->guild_id == 0) {
				$msg = "Character <highlight>$name<end> does not seem to be in an org.";
				unset($whois);
				$sendto->reply($msg);
				unset($this->orglist);
				return;
			} else {
				$orgid = $whois->guild_id;
			}
		}

		$sendto->reply("Downloading org list for org id $orgid...");

		$org = $this->guildManager->get_by_id($orgid);

		if ($org === null) {
			$msg = "Error in getting the Org info. Either org does not exist or AO's server was too slow to respond.";
			$sendto->reply($msg);
			unset($this->orglist);
			return;
		}

		$this->orglist["org"] = $org->orgname;

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

			$this->orglist["result"][$member->name]["post"] = $thismember;

			$this->orglist["result"][$member->name]["name"] = $member->name;
			$this->orglist["result"][$member->name]["rank_id"] = $member->guild_rank_id;

			// If we havent found an org type yet, check this member if they have a unique rank.
			if (!isset($this->orglist["orgtype"])) {
				if (($member->guild_rank_id == 0 && $member->guild_rank == "President") ||
					($member->guild_rank_id == 3 && $member->guild_rank == "Member") ||
					($member->guild_rank_id == 4 && $member->guild_rank == "Applicant")) {
					// Dont do anything. Can't do a match cause this rank is in multiple orgtypes.
				} else if ($member->guild_rank == $orgrankmap["Anarchism"][$member->guild_rank_id]) {
					$this->orglist["orgtype"] = $orgrankmap["Anarchism"];
				} else if ($member->guild_rank == $orgrankmap["Monarchy"][$member->guild_rank_id]) {
					$this->orglist["orgtype"] = $orgrankmap["Monarchy"];
				} else if ($member->guild_rank == $orgrankmap["Feudalism"][$member->guild_rank_id]) {
					$this->orglist["orgtype"] = $orgrankmap["Feudalism"];
				} else if ($member->guild_rank == $orgrankmap["Republic"][$member->guild_rank_id]) {
					$this->orglist["orgtype"] = $orgrankmap["Republic"];
				} else if ($member->guild_rank == $orgrankmap["Faction"][$member->guild_rank_id]) {
					$this->orglist["orgtype"] = $orgrankmap["Faction"];
				} else if ($member->guild_rank == $orgrankmap["Department"][$member->guild_rank_id]) {
					$this->orglist["orgtype"] = $orgrankmap["Department"];
				}
			}

			$buddy_online_status = $this->buddylistManager->is_online($member->name);
			if ($buddy_online_status !== null) {
				$this->orglist["result"][$member->name]["online"] = $buddy_online_status;
			} else if ($this->chatBot->vars["name"] != $member->name) { // If the name being checked ISNT the bot.
				// check if they exist
				if ($this->chatBot->get_uid($member->name)) {
					$this->orglist["check"][$member->name] = 1;
				}
			} else if ($this->chatBot->vars["name"] == $member->name) { // Yes, this bot is online. Don't need a buddylist to tell me.
				$this->orglist["result"][$member->name]["online"] = 1;
			}
		}

		$sendto->reply("Checking online status for " . count($org->members) ." members of '$org->orgname'...");

		// prime the list and get things rolling by adding some buddies
		$i = 0;
		forEach ($this->orglist["check"] as $name => $value) {
			$this->orglist["added"][$name] = 1;
			unset($this->orglist["check"][$name]);
			$this->buddylistManager->add($name, 'onlineorg');
			if (++$i == 10) {
				break;
			}
		}

		if (!isset($this->orglist["orgtype"]) && !$msg) {
			// If we haven't found the org yet, it can only be
			// Department or Republic with only a president.
			$this->orglist["orgtype"] = $orgrankmap["Republic"];
		}

		unset($org);

		// If we added names to the buddylist, this will kick in to determine if they are online or not.
		// If no more names need to be checked, then post results.
		$this->checkOrglistEnd();
	}
	
	public function checkOrglistEnd($forceEnd = false) {
		// Don't want to reboot to see changes in color edits, so I'll store them in an array outside the function.
		$orgcolor["header"]  = "<font color='#FFFFFF'>";   // Org Rank title
		$orgcolor["onlineH"] = "<highlight>";              // Highlights on whois info
		$orgcolor["offline"] = "<font color='#555555'>";   // Offline names

		if (isset($this->orglist) && count($this->orglist["added"]) == 0 || $forceEnd) {
			$blob = $this->orgmatesformat($this->orglist, $orgcolor, $this->orglist["start"], $this->orglist["org"]);
			$msg = $this->text->make_blob("Orglist for '".$this->orglist["org"]."'", $blob);
			$this->orglist["sendto"]->reply($msg);

			// in case it was ended early
			forEach ($this->orglist["added"] as $name => $value) {
				$this->buddylistManager->remove($name, 'onlineorg');
			}
			unset($this->orglist);
		}
	}
	
	function orgmatesformat($memberlist, $color, $timestart, $orgname) {
		$map = $memberlist["orgtype"];

		$totalonline = 0;
		$totalcount = count($memberlist["result"]);
		forEach ($memberlist["result"] as $amember) {
			$newlist[$amember["rank_id"]][] = $amember["name"];
		}

		$blob = '';

		for ($rankid = 0; $rankid < count($map); $rankid++) {
			$onlinelist = "";
			$offlinelist = "";
			$olcount = 0;
			$rank_online = 0;
			$rank_total = count($newlist[$rankid]);

			sort($newlist[$rankid]);
			for ($i = 0; $i < $rank_total; $i++) {
				if ($memberlist["result"][$newlist[$rankid][$i]]["online"]) {
					$rank_online++;
					$onlinelist .= "  " . $memberlist["result"][$newlist[$rankid][$i]]["post"] . "\n";
				} else {
					if ($offlinelist != "") {
						$offlinelist .= ", ";
						if (($olcount % 50) == 0) {
							$offlinelist .= "<end><pagebreak>" . $color["offline"];
						}
					}
					$offlinelist .= $newlist[$rankid][$i];
					$olcount++;
				}
			}

			$totalonline += $rank_online;

			$blob .= "\n" . $color["header"] . $map[$rankid] . "</font> ";
			$blob .= "(" . $color["onlineH"] . "{$rank_online}</font> online of " . $color["onlineH"] . "{$rank_total}</font>)\n";

			if ($onlinelist != "") {
				$blob .= $onlinelist;
			}
			if ($offlinelist != "") {
				$blob .= $color["offline"] . $offlinelist . "<end>\n";
			}
			$blob .= "\n";
		}

		$totaltime = time() - $timestart;
		$header  = $color["onlineH"].$orgname."<end> has ";
		$header .= $color["onlineH"]."$totalonline</font> online out of a total of ".$color["onlineH"]."$totalcount</font> members. ";
		$header .= "(".$color["onlineH"]."$totaltime</font> seconds)\n\n";
		$blob = $header . $blob;

		return $blob;
	}
	
	/**
	 * @Event("logOn")
	 * @Description("Records online status of org members")
	 */
	public function orgMemberLogonEvent($eventObj) {
		$this->updateOrglist($eventObj->sender, $eventObj->type);
	}
	
	/**
	 * @Event("logOff")
	 * @Description("Records offline status of org members")
	 */
	public function orgMemberLogoffEvent($eventObj) {
		$this->updateOrglist($eventObj->sender, $eventObj->type);
	}
	
	public function updateOrglist($sender, $type) {
		if (isset($this->orglist["added"][$sender])) {
			if ($type == "logon") {
				$this->orglist["result"][$sender]["online"] = 1;
			} else if ($type == "logoff") {
				$this->orglist["result"][$sender]["online"] = 0;
			}

			$this->buddylistManager->remove($sender, 'onlineorg');
			unset($this->orglist["added"][$sender]);

			forEach ($this->orglist["check"] as $name => $value) {
				$this->orglist["added"][$name] = 1;
				unset($this->orglist["check"][$name]);
				$this->buddylistManager->add($name, 'onlineorg');
				break;
			}

			$this->checkOrglistEnd();
		}
	}
}

