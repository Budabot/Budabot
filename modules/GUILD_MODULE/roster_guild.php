<?php

if ($chatBot->vars["my guild"] != "" && $chatBot->vars["my guild id"] != "") {
	Logger::log('INFO', 'GUILD_MODULE', "Starting Roster update");

	//Get the org infos
	$org = Guild::get_by_id($chatBot->vars["my guild id"], $chatBot->vars["dimension"], true);
	
	//Check if Orgxml file is correct if not abort
	if ($org === null) {
		Logger::log('ERROR', 'GUILD_MODULE', "Error downloading the org roster xml file");
		return;
	}

	//Delete old Memberslist
	unset($chatBot->guildmembers);
	
	//Save the current org_members table in a var
	$db->query("SELECT * FROM org_members_<myname>");
	$data = $db->fObject('all');
	if ($db->numrows() == 0 && (count($org->members) > 0)) {
		$restart = true;
	} else {
		$restart = false;
		forEach ($data as $row) {
			$dbentrys[$row->name]["name"] = $row->name;
			$dbentrys[$row->name]["mode"] = $row->mode;
		}
	}
	
	$db->beginTransaction();
	
	// Going through each member of the org and add or update his/her
	forEach ($org->members as $member) {
		// don't do anything if $member is the bot itself
		if (strtolower($member->name) == strtolower($chatBot->vars["name"])) {
			continue;
		}
	
		//If there exists already data about the player just update him/her
		if (isset($dbentrys[$member->name])) {
			if ($dbentrys[$member->name]["mode"] == "man" || $dbentrys[$member->name]["mode"] == "org") {
				$mode = "org";
				$chatBot->guildmembers[$member->name] = $member->guild_rank_id;
				
				// add org members who are on notify to buddy list
				Buddylist::add($member->name, 'org');
			} else {
				$mode = "del";
				Buddylist::remove($member->name, 'org');
			}
	
			$db->exec("UPDATE org_members_<myname> SET `mode` = '{$mode}' WHERE `name` = '{$member->name}'");	  		
		//Else insert his/her data
		} else {
			// add new org members to buddy list
			Buddylist::add($member->name, 'org');

			$db->exec("INSERT INTO org_members_<myname> (`name`, `mode`) VALUES ('{$member->name}', 'org')");
			$chatBot->guildmembers[$member->name] = $member->guild_rank_id;
		}
		unset($dbentrys[$member->name]);    
	}
	
	$db->Commit();
	
	// remove buddies who are no longer org members
	forEach ($dbentrys as $buddy) {
		$db->exec("DELETE FROM org_members_<myname> WHERE `name` = '{$buddy['name']}'");
		Buddylist::remove($buddy['name'], 'org');
	}

	Logger::log('INFO', 'GUILD_MODULE', "Finished Roster update");
	
	if ($restart == true) {
		$chatBot->send("The bot needs to be restarted to be able to see who is online in your org. Automatically restarting in 10 seconds.", "guild");
		
		// in case some of the org members were already on the friendlist, we need to restart the bot
		// in order to get them to appear on the online list
		Logger::log('INFO', 'GUILD_MODULE', "The bot is restarting");
		
		// wait for all buddy add/remove packets to finish sending
		// not 100% sure this is needed
		sleep(5);
		
		die();
	}
}
?>
