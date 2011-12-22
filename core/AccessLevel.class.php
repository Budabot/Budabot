<?php

class AccessLevel {
	public static $ACCESS_LEVELS = array('none' => 0, 'superadmin' => 1,  'admin' => 2, 'mod' => 3, 'rl' => 4, 'leader' => 5, 'guild' => 6, 'member' => 7, 'all' => 8);
	
	/**
	 * @deprecated: use checkAccess() instead
	 */
	public static function check_access($sender, $accessLevel) {
		return AccessLevel::checkAccess($sender, $accessLevel);
	}

	/**
	 * @name: checkAccess
	 * @param: $sender - the name of the person you want to check access on
	 * @param: $accessLevel - can be one of: superadmin, admininistrator, moderator, raidleader, leader, guild, member, all
	 * @returns: true if $sender has at least $accessLevel, false otherwise
	 */
	public static function checkAccess($sender, $accessLevel) {
		Logger::log("DEBUG", "AccessLevel", "Checking access level '$accessLevel' against character '$sender'");
	
		$returnVal = AccessLevel::checkSingleAccess($sender, $accessLevel);
		
		if ($returnVal === false && Setting::get('alts_inherit_admin') == 1) {
			// if current character doesn't have access,
			// and if alts_inherit_admin is enabled,
			// and if the current character is not a main character,
			// and if the current character is validated,
			// then check access against the main character,
			// otherwise just return the result
			$altInfo = Alts::get_alt_info($sender);
			if ($sender != $altInfo->main && $altInfo->is_validated($sender)) {
				Logger::log("DEBUG", "AccessLevel", "Checking access level '$accessLevel' against the main of '$sender' which is '$altInfo->main'");
				$returnVal = AccessLevel::checkSingleAccess($altInfo->main, $accessLevel);
			}
		}
		
		return $returnVal;
	}
	
	public static function checkSingleAccess($sender, $accessLevel) {
		$sender = ucfirst(strtolower($sender));
		$accessLevel = AccessLevel::normalizeAccessLevel($accessLevel);

		$charAccessLevel = AccessLevel::getSingleAccessLevel($sender);
		return (AccessLevel::compareAccessLevels($charAccessLevel, $accessLevel) >= 0);
	}
	
	public static function normalizeAccessLevel($accessLevel) {
		$accessLevel = strtolower($accessLevel);
		switch ($accessLevel) {
			case "raidleader":
				$accessLevel = "rl";
				break;
			case "moderator":
				$accessLevel = "mod";
				break;
			case "administrator":
				$accessLevel = "admin";
				break;
		}
		
		return $accessLevel;
	}
	
	public static function getDisplayName($accessLevel) {
		$displayName = strtolower($accessLevel);
		switch ($displayName) {
			case "rl":
				$displayName = "raidleader";
				break;
			case "mod":
				$displayName = "moderator";
				break;
			case "administrator":
				$displayName = "admin";
				break;
		}

		return $displayName;
	}
	
	public static function getSingleAccessLevel($sender) {
		global $chatBot;
		$db = DB::get_instance();
		
		if ($chatBot->vars["SuperAdmin"] == $sender){
			return "superadmin";
		}
		if (isset($chatBot->admins[$sender])) {
			$level = $chatBot->admins[$sender]["level"];
			if ($level >= 4 || AccessLevel::checkGuildAdmin($sender, 'admin')) {
				return "admin";
			}
			if ($level >= 3 || AccessLevel::checkGuildAdmin($sender, 'mod')) {
				return "mod";
			}
			if ($level >= 2 || AccessLevel::checkGuildAdmin($sender, 'rl')) {
				return "rl";
			}
		}
		if (isset($chatBot->data["leader"]) && $chatBot->data["leader"] == $sender) {
			return "leader";
		}
		if (isset($chatBot->guildmembers[$sender])) {
			return "guild";
		}
		
		$sql = "SELECT name FROM members_<myname> WHERE `name` = ?";
		$row = $db->queryRow($sql, $sender);
		if ($row !== null) {
			return "member";
		}
		return "all";
	}
	
	public static function getAccessLevelForCharacter($sender) {
		$sender = ucfirst(strtolower($sender));

		$accessLevel = AccessLevel::getSingleAccessLevel($sender);
		
		if (Setting::get('alts_inherit_admin') == 1) {
			$altInfo = Alts::get_alt_info($sender);
			if ($sender != $altInfo->main && $altInfo->is_validated($sender)) {
				$mainAccessLevel = AccessLevel::getSingleAccessLevel($altInfo->main);
				if (AccessLevel::compareAccessLevels($mainAccessLevel, $accessLevel) > 0) {
					$accessLevel = $mainAccessLevel;
				}
			}
		}
		
		return $accessLevel;
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
	 *               and 0 if the access levels are equal.
	 */
	public static function compareAccessLevels($accessLevel1, $accessLevel2) {
		$accessLevel1 = AccessLevel::normalizeAccessLevel($accessLevel1);
		$accessLevel2 = AccessLevel::normalizeAccessLevel($accessLevel2);
	
		return AccessLevel::$ACCESS_LEVELS[$accessLevel2] - AccessLevel::$ACCESS_LEVELS[$accessLevel1];
	}
	
	public static function compareCharacterAccessLevels($char1, $char2) {
		$char1 = ucfirst(strtolower($char1));
		$char2 = ucfirst(strtolower($char2));
		
		$char1AccessLevel = AccessLevel::getAccessLevelForCharacter($char1);
		$char2AccessLevel = AccessLevel::getAccessLevelForCharacter($char2);
		
		return AccessLevel::compareAccessLevels($char1AccessLevel, $char2AccessLevel);
	}
}

?>
