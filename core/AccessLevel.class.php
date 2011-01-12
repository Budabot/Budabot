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
			case "2":
			case "3":
			case "4":
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
}

?>
