<?php

class AccessLevel {
	public static function checkAccess($sender, $accessLevel) {
		global $chatBot;
		
		if (Setting::get('alts_inherit_admin') == 1) {
			$sender = Alts::get_main($sender);
		}
		$charid = $chatBot->get_uid($sender);

		$access = false;
		
		switch ($accessLevel) {
			case "guild":
				if (isset($chatBot->guildmembers[$charid]) || isset($chatBot->admins[$charid])) {
					$access = true;
				}
				break;

			case "guildadmin":
				if ((isset($chatBot->guildmembers[$charid]) && $chatBot->guildmembers[$charid]->guild_rank_id <= Setting::get('guild_admin_level')) || isset($chatBot->admins[$charid])) {
					$access = true;
				}
				break;

			case "1":
			case "leader":
				if ($chatBot->data["leader"] == $sender || isset($chatBot->admins[$charid])) {
					$access = true;
				}
				break;

			case "2":
			case "3":
			case "4":
				if ($chatBot->admins[$charid]->access_level >= $accessLevel) {
					$access = true;
				}
				break;

			case "all":
			default:
				$access = true;
				break;
		}
		
		return $access;
	}
	
	public static function get_admin_level($sender) {
		global $chatBot;
	
		if (Setting::get('alts_inherit_admin') == 1) {
			$sender = Alts::get_main($sender);
		}
		$charid = $chatBot->get_uid($sender);
		
		return (int)$chatBot->admins[$charid]->access_level;
	}
}

?>
