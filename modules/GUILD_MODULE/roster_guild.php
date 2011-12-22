<?php

if ($chatBot->vars["my_guild_id"] != "") {
	Logger::log('INFO', 'GUILD_MODULE', "Starting Roster update");

	// Get the guild info
	$org = Guild::get_by_id($chatBot->vars["my_guild_id"], $chatBot->vars["dimension"], true);
	
	// Check if guild xml file is correct if not abort
	if ($org === null) {
		Logger::log('ERROR', 'GUILD_MODULE', "Error downloading the guild roster xml file");
		return;
	}
	
	if (count($org->members) == 0) {
		Logger::log('ERROR', 'GUILD_MODULE', "Guild xml file has no members! Aborting roster update.");
		return;
	}
	
	$chatBot->vars["logondelay"] = time() + 100000;

	// Save the current org_members table in a var
	$data = $db->query("SELECT * FROM org_members_<myname>");
	if (count($data) == 0 && (count($org->members) > 0)) {
		$restart = true;
	} else {
		$restart = false;
		forEach ($data as $row) {
			$dbentrys[$row->name]["name"] = $row->name;
			$dbentrys[$row->name]["mode"] = $row->mode;
		}
	}
	
	$db->begin_transaction();
	
	// Going through each member of the org and add or update his/her
	forEach ($org->members as $member) {
		// don't do anything if $member is the bot itself
		if (strtolower($member->name) == strtolower($chatBot->vars["name"])) {
			continue;
		}
	
		//If there exists already data about the player just update him/her
		if (isset($dbentrys[$member->name])) {
			if ($dbentrys[$member->name]["mode"] == "del") {
				// members who are not on notify should not be on the buddy list but should remain in the database
				Buddylist::remove($member->name, 'org');
				unset($chatBot->guildmembers[$name]);
			} else {
				// add org members who are on notify to buddy list
				Buddylist::add($member->name, 'org');
				$chatBot->guildmembers[$member->name] = $member->guild_rank_id;

				// if member was added to notify list manually, switch mode to org and let guild roster update from now on
				if ($dbentrys[$member->name]["mode"] == "add") {
					$db->exec("UPDATE org_members_<myname> SET `mode` = 'org' WHERE `name` = ?", $member->name);
				}
			}
		//Else insert his/her data
		} else {
			// add new org members to buddy list
			Buddylist::add($member->name, 'org');
			$chatBot->guildmembers[$member->name] = $member->guild_rank_id;

			$db->exec("INSERT INTO org_members_<myname> (`name`, `mode`) VALUES (?, 'org')", $member->name);
		}
		unset($dbentrys[$member->name]);
	}
	
	$db->commit();
	
	// remove buddies who are no longer org members
	forEach ($dbentrys as $buddy) {
		if ($buddy['mode'] != 'add') {
			$db->exec("DELETE FROM online WHERE `name` = ? AND `channel_type` = 'guild' AND added_by = '<myname>'", $buddy['name']);
			$db->exec("DELETE FROM org_members_<myname> WHERE `name` = ?", $buddy['name']);
			Buddylist::remove($buddy['name'], 'org');
			unset($chatBot->guildmembers[$buddy['name']]);
		}
	}

	Logger::log('INFO', 'GUILD_MODULE', "Finished Roster update");

	if ($restart == true) {
		$chatBot->send("Guild roster has been loaded for the first time. Restarting...", "guild");

		Logger::log('INFO', 'GUILD_MODULE', "The bot is restarting");

		sleep(5);

		// in case some of the org members were already on the friendlist, we need to restart the bot
		// in order to get them to appear on the online list
		die();
	}

	$chatBot->vars["logondelay"] = time() + 5;
}

?>
