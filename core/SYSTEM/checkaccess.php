<?php

if (preg_match("/^checkaccess$/i", $message) || preg_match("/^checkaccess (.+)$/i", $message, $arr)) {
	if ($arr) {
		$name = $arr[1];
	} else {
		$name = $sender;
	}
	
	$access_levels = array ('superadmin', 'administrator', 'moderator', 'raidleader', 'guildadmin', 'leader', 'guild', 'member', 'all');
	
	$current_access_level = '';
	forEach ($access_levels as $access_level) {
		$current_access_level = $access_level;
		if (AccessLevel::check_access($name, $access_level)) {
			break;
		}
	}
	
	$msg = "Access level for $name is: $current_access_level";
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
