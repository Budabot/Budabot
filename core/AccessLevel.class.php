<?php

class AccessLevel {
	public static function checkAccess($sender, $accessLevel) {
		global $chatBot;

		$access = false;
		
		switch ($accessLevel) {
			case "guild":
				if (isset($chatBot->guildmembers[$sender]) || isset($chatBot->admins[$sender])) {
					$access = true;
				}
				break;

			case "guildadmin":
				if ($chatBot->guildmembers[$sender] <= $chatBot->settings['guild_admin_level'] || isset($chatBot->admins[$sender])) {
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
				if (Setting::get('alts_inherit_admin') == 1) {
					$sender = Alts::get_main($sender);
				}
				
				if ($chatBot->admins[$sender]["level"] >= $accessLevel) {
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
		
		return (int)$chatBot->admins[$sender]["level"];
	}
}

?>
