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
	
	private $orglist = null;
	private $orgrankmap = array();
	
	public function __construct() {
		$this->orgrankmap["Anarchism"]  = array("Anarchist");
		$this->orgrankmap["Monarchy"]   = array("Monarch",   "Counsel",      "Follower");
		$this->orgrankmap["Feudalism"]  = array("Lord",      "Knight",       "Vassal",          "Peasant");
		$this->orgrankmap["Republic"]   = array("President", "Advisor",      "Veteran",         "Member",         "Applicant");
		$this->orgrankmap["Faction"]    = array("Director",  "Board Member", "Executive",       "Member",         "Applicant");
		$this->orgrankmap["Department"] = array("President", "General",      "Squad Commander", "Unit Commander", "Unit Leader", "Unit Member", "Applicant");
	}
	
	/**
	 * @HandlesCommand("orglist")
	 * @Matches("/^orglist end$/i")
	 */
	public function orglistEndCommand($message, $channel, $sender, $sendto, $args) {
		if (isset($this->orglist)) {
			$this->orglistEnd();
		} else {
			$sendto->reply("There is no orglist currently running.");
		}
	}
	
	/**
	 * @HandlesCommand("orglist")
	 * @Matches("/^orglist (.+)$/i")
	 */
	public function orglistCommand($message, $channel, $sender, $sendto, $args) {
		// Check if we are already doing a list.
		if (isset($this->orglist)) {
			$msg = "There is already an orglist running.";
			$sendto->reply($msg);
			return;
		}

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
				$msg = "Could not find character info for <highlight>$name<end>.";
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

		$sendto->reply("Downloading org roster for org id $orgid...");

		$org = $this->guildManager->get_by_id($orgid);

		if ($org === null) {
			$msg = "Error in getting the Org info. Either org does not exist or AO's server was too slow to respond.";
			$sendto->reply($msg);
			unset($this->orglist);
			return;
		}

		$this->orglist["org"] = $org->orgname;
		$this->orglist["orgtype"] = $this->getOrgGoverningForm($org->members);

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
		}

		$sendto->reply("Checking online status for " . count($org->members) ." members of '$org->orgname'...");
		
		$this->checkOnline($org->members);
		$this->addOrgMembersToBuddylist();

		unset($org);
		
		if (count($this->orglist["added"]) == 0) {
			$this->orglistEnd();
		}
	}
	
	public function getOrgGoverningForm($members) {
		$governingForm = '';
		$forms = $this->orgrankmap;
		forEach ($members as $member) {
			forEach ($forms as $name => $ranks) {
				if ($ranks[$member->guild_rank_id] != $member->guild_rank) {
					unset($forms[$name]);
				}
			}
			if (count($forms) == 1) {
				break;
			}
		}
		
		// it's possible we haven't narrowed it down to 1 at this point
		// If we haven't found the org yet, it can only be
		// Republic or Department with only a president.
		// choose the first one
		return array_shift($forms);
	}
	
	public function checkOnline($members, $callback) {
		// round to nearest thousand and then subtract 5
		$this->orglist["maxsize"] = ceil(count($this->buddylistManager->buddyList) / 1000) * 1000 - count($this->buddylistManager->buddyList) - 5;
	
		forEach ($members as $member) {
			$buddy_online_status = $this->buddylistManager->is_online($member->name);
			if ($buddy_online_status !== null) {
				$this->orglist["result"][$member->name]["online"] = $buddy_online_status;
			} else if ($this->chatBot->vars["name"] == $member->name) {
				$this->orglist["result"][$member->name]["online"] = 1;
			} else {
				// check if they exist
				if ($this->chatBot->get_uid($member->name)) {
					$this->orglist["check"][$member->name] = 1;
				}
			}
		}
	}
	
	public function addOrgMembersToBuddylist() {
		forEach ($this->orglist["check"] as $name => $value) {
			if (!$this->checkBuddylistSize()) {
				break;
			}

			$this->orglist["added"][$name] = 1;
			unset($this->orglist["check"][$name]);
			$this->buddylistManager->add($name, 'onlineorg');
		}
	}
	
	public function orglistEnd() {
		// Don't want to reboot to see changes in color edits, so I'll store them in an array outside the function.
		$orgcolor["header"]  = "<font color='#FFFFFF'>";   // Org Rank title
		$orgcolor["onlineH"] = "<highlight>";              // Highlights on whois info
		$orgcolor["offline"] = "<font color='#555555'>";   // Offline names

		$msg = $this->orgmatesformat($this->orglist, $orgcolor, $this->orglist["start"], $this->orglist["org"]);
		$this->orglist["sendto"]->reply($msg);

		// in case it was ended early
		forEach ($this->orglist["added"] as $name => $value) {
			$this->buddylistManager->remove($name, 'onlineorg');
		}
		unset($this->orglist);
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
		
		return $this->text->make_blob("Orglist for '".$this->orglist["org"]."' ($totalonline / $totalcount)", $blob);
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

			$this->addOrgMembersToBuddylist();

			if (count($this->orglist["added"]) == 0) {
				$this->orglistEnd();
			}
		}
	}
	
	public function checkBuddylistSize() {
		return count($this->buddylistManager->buddyList) < $this->orglist["maxsize"];
	}
}

