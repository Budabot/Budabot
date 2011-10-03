<?php

class AccessLevel {
	public static $ACCESS_LEVELS = array('superadmin' => 0,  'admin' => 1, 'mod' => 2, 'rl' => 3, 'leader' => 4, 'guild' => 5, 'member' => 6, 'all' => 7);

	/**
	 * @name: check_access
	 * @param: $sender - the name of the person you want to check access on
	 * @param: $access_level - can be one of: superadmin, admininistrator|4, moderator|3, raidleader|2, leader, guild, member, all
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
	
	public static function checkGuildAdmin($sender, $accessLevel) {
		if ($chatBot->guildmembers[$sender] <= Setting::get('guild_admin_rank')) {
			if (compareAccessLevels(Setting::get('guild_admin_access_level'), $accessLevel) > 0) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * @description: Returns a positive number if $accessLevel1 is a greater access level than $accessLevel2,
	 *               a negative number if $accessLevel1 is a lesser access level than $accessLevel2,
	 *               and 0 if the access levels are equal
	 */
	public static function compareAccessLevels($accessLevel1, $accessLevel2) {
		return AccessLevel::$ACCESS_LEVELS[$accessLevel2] - AccessLevel::$ACCESS_LEVELS[$accessLevel1];
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
