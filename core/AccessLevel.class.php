<?php

class AccessLevel {
	public static $ACCESS_LEVELS = array('none' => 0, 'superadmin' => 1,  'admin' => 2, 'mod' => 3, 'rl' => 4, 'leader' => 5, 'guild' => 6, 'member' => 7, 'all' => 8);

	/**
	 * @name: check_access
	 * @param: $sender - the name of the person you want to check access on
	 * @param: $accessLevel - can be one of: superadmin, admininistrator|4, moderator|3, raidleader|2, leader, guild, member, all
	 * @returns: true if $sender has at least $accessLevel, false otherwise
	 */
	public static function check_access($sender, $accessLevel) {
		global $chatBot;
		
		$sender = ucfirst(strtolower($sender));

		if (Setting::get('alts_inherit_admin') == 1) {
			$altInfo = Alts::get_alt_info($sender);
			if ($altInfo->is_validated($sender)) {
				$sender = $altInfo->main;
			}
		}

		switch ($accessLevel) {
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
			case "leader":
				if (isset($chatBot->data["leader"]) && $chatBot->data["leader"] == $sender) {
					return true;
				}
			case "rl":
			case "raidleader":
				if ((isset($chatBot->admins[$sender]) && $chatBot->admins[$sender]["level"] >= 2) || AccessLevel::checkGuildAdmin($sender, 'rl')) {
					return true;
				}
			case "mod":
			case "moderator":
				if ((isset($chatBot->admins[$sender]) && $chatBot->admins[$sender]["level"] >= 3) || AccessLevel::checkGuildAdmin($sender, 'mod')) {
					return true;
				}
			case "admin":
			case "administrator":
				if ((isset($chatBot->admins[$sender]) && $chatBot->admins[$sender]["level"] >= 4) || AccessLevel::checkGuildAdmin($sender, 'admin')) {
					return true;
				}
			case "superadmin":
				if ($chatBot->vars["SuperAdmin"] == $sender){
					return true;
				}
			case 'none':
				return false;
			default:
				Logger::log('ERROR', 'AccessLevel', "Invalid access level: '$accessLevel' checked against: '$sender'");
				return false;
		}
	}
	
	public static function checkGuildAdmin($sender, $accessLevel) {
		global $chatBot;

		if (isset($chatBot->guildmembers[$sender]) && $chatBot->guildmembers[$sender] <= Setting::get('guild_admin_rank')) {
			if (AccessLevel::compareAccessLevels(Setting::get('guild_admin_access_level'), $accessLevel) >= 0) {
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
