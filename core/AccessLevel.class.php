<?php

class AccessLevel {
	public static function checkAccess($sender, $access_level) {
		global $chatBot;
		
		if (Setting::get('alts_inherit_admin') == 1) {
			$sender = Alts::get_main($sender);
		}

		$access = false;
		switch ($access_level) {
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
	
	public static function get_admin_level($sender) {
		global $chatBot;
	
		if (Setting::get('alts_inherit_admin') == 1) {
			$sender = Alts::get_main($sender);
		}
		
		return (int)$chatBot->admins[$sender]["level"];
	}
}

?>
