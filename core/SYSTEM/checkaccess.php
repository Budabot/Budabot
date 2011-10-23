<?php

if (preg_match("/^checkaccess$/i", $message) || preg_match("/^checkaccess (.+)$/i", $message, $arr)) {
	if (isset($arr)) {
		$name = ucfirst(strtolower($arr[1]));
	} else {
		$name = $sender;
	}
	
	$accessLevel = AccessLevel::getDisplayName(AccessLevel::getAccessLevelForCharacter($name));
	
	$msg = "Access level for $name is <highlight>$accessLevel<end>.";
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
