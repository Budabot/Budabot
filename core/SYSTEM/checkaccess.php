<?php

if (preg_match("/^checkaccess$/i", $message) || preg_match("/^checkaccess (.+)$/i", $message, $arr)) {
	if (isset($arr)) {
		$name = ucfirst(strtolower($arr[1]));
	} else {
		$name = $sender;
	}
	
	$current_access_level = '';
	forEach (AccessLevel::$ACCESS_LEVELS as $level => $access_level) {
		$current_access_level = $access_level;
		if (AccessLevel::check_access($name, $access_level)) {
			break;
		}
	}
	
	$msg = "Access level for $name is <highlight>$current_access_level<end>.";
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
