<?php

class AccessLevel {
	/**
	 * @name: check_access
	 * @param: $sender - the name of the person you want to check access on
	 * @param: $access_level - can be one of: superadmin, admininistrator|4, moderator|3, raidleader|2, guildadmin, leader, guild, member, all
	 * @returns: true if $sender has at least $access_level, false otherwise
	 */
	public static function check_access($sender, $access_level) {
		global $chatBot;
		
		if (Setting::get('alts_inherit_admin') == 1) {
			$altInfo = Alts::get_alt_info($sender);
			if ($altInfo->currentValidated) {
				$sender = $altInfo->main;
			}
		}
		
		// convert admin level names to numbers
		switch ($access_level) {
			case "rl":
			case "raidleader":
				$access_level = 2;
				break;
			case "mod":
			case "moderator":
				$access_level = 3;
				break;
			case "admin":
			case "administrator":
				$access_level = 4;
				break;
		}

		$access = false;
		switch ($access_level) {
			case "superadmin":
				if ($chatBot->vars["SuperAdmin"] == $sender){
					$access = true;
				}
				break;
			case "guild":
				if (isset($chatBot->guildmembers[$sender]) || isset($chatBot->admins[$sender])) {
					$access = true;
				}
				break;

			case "guildadmin":
				if (isset($chatBot->admins[$sender]) || (isset($chatBot->guildmembers[$sender]) && $chatBot->guildmembers[$sender] <= $chatBot->settings['guild_admin_level'])) {
					$access = true;
				}
				break;

			case "1":
			case "leader":
				if ($chatBot->data["leader"] == $sender || isset($chatBot->admins[$sender])) {
					$access = true;
				}
				break;

			case "2":
			case "3":
			case "4":
				if ($chatBot->admins[$sender]["level"] >= $access_level) {
					$access = true;
				}
				break;
				
			case "member":
				if (isset($chatBot->guildmembers[$sender]) || isset($chatBot->admins[$sender])) {
					$access = true;
				} else {
					$db = DB::get_instance();
					$sql = "SELECT name FROM members_<myname> WHERE `name` = '$sender'";
					$db->query($sql);
					if ($db->numrows() > 0) {
						$access = true;
					}
				}
				break;

			case "all":
				$access = true;
				break;

			default:
				$access = false;
				break;
		}
		
		return $access;
	}

	/**
	 * @name: get_admin_level
	 * @param: $sender - the name of the person you want to get the admin level for
	 * @return: 4 if administrator, 3 if moderator, 2 if raidleader, otherwise null
	 */
	public static function get_admin_level($sender) {
		global $chatBot;
	
		if (Setting::get('alts_inherit_admin') == 1) {
			$altInfo = Alts::get_alt_info($sender);
			if ($altInfo->currentValidated) {
				$sender = $altInfo->main;
			}
		}
		
		return (int)$chatBot->admins[$sender]["level"];
	}
}

?>
