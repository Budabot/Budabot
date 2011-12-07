<?php

if (preg_match("/^logonadmin ([a-zA-Z0-9-]+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$logon_msg = Preferences::get($name, 'logon_msg');

	if ($logon_msg === false || $logon_msg == '') {
		$msg = "The logon message for $name has not been set.";
	} else {
		$msg = "{$name} logon: {$logon_msg}";
	}
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^logonadmin ([a-zA-Z0-9-]+) (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$logon_msg = $arr[2];
	
	if ($logon_msg == 'clear') {
		Preferences::save($name, 'logon_msg', '');
		$msg = "The logon message for $name has been cleared.";
	} else if (strlen($logon_msg) <= Setting::get('max_logon_msg_size')) {
		Preferences::save($name, 'logon_msg', $logon_msg);
		$msg = "The logon message for $name has been set.";
	} else {
		$msg = "The logon message is too large. The logon message may contain a maximum of " . Setting::get('max_logon_msg_size') . " characters.";
	}
    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>