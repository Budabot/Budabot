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
		
		$sender = ucfirst(strtolower($sender));

		if (Setting::get('alts_inherit_admin') == 1) {
			$altInfo = Alts::get_alt_info($sender);
			if ($altInfo->is_validated($sender)) {
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

		switch ($access_level) {
			case "all":
				return true;
			case "member":
				$db = DB::get_instance();
				$sql = "SELECT name FROM members_<myname> WHERE `name` = '$sender'";
				$db->query($sql);
				if ($db->numrows() > 0) {
					return true;
				}
			case "guild":
				if (isset($chatBot->guildmembers[$sender])) {
					return true;
				}
			case "1":
			case "leader":
				if ($chatBot->data["leader"] == $sender) {
					return true;
				}
			case "guildadmin":
				if (isset($chatBot->guildmembers[$sender]) && $chatBot->guildmembers[$sender] <= Setting::get('guild_admin_level')) {
					return true;
				}
			case "2":
				if (isset($chatBot->admins[$sender]) && $chatBot->admins[$sender]["level"] >= 2) {
					return true;
				}
			case "3":
				if (isset($chatBot->admins[$sender]) && $chatBot->admins[$sender]["level"] >= 3) {
					return true;
				}
			case "4":
				if (isset($chatBot->admins[$sender]) && $chatBot->admins[$sender]["level"] >= 4) {
					return true;
				}
			case "superadmin":
				if ($chatBot->vars["SuperAdmin"] == $sender){
					return true;
				}
			default:
				return false;
		}
	}

	/**
	 * @name: get_admin_level
	 * @param: $sender - the name of the person you want to get the admin level for
	 * @return: 4 if administrator, 3 if moderator, 2 if raidleader, otherwise 0
	 */
	public static function get_admin_level($sender) {
		global $chatBot;

		$sender = ucfirst(strtolower($sender));
	
		if (Setting::get('alts_inherit_admin') == 1) {
			$altInfo = Alts::get_alt_info($sender);
			if ($altInfo->is_validated($sender)) {
				$sender = $altInfo->main;
			}
		}
		
		return (int)$chatBot->admins[$sender]["level"];
	}
}

?>
