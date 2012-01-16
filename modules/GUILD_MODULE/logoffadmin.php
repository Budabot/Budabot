<?php

if (preg_match("/^logoffadmin ([a-zA-Z0-9-]+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$logoff_msg = Preferences::get($name, 'logoff_msg');

	if ($logoff_msg === false || $logoff_msg == '') {
		$msg = "The logoff message for $name has not been set.";
	} else {
		$msg = "{$name} logoff: {$logoff_msg}";
	}
    $sendto->reply($msg);
} else if (preg_match("/^logoffadmin ([a-zA-Z0-9-]+) (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$logoff_msg = $arr[2];
	
	if ($logoff_msg == 'clear') {
		Preferences::save($name, 'logoff_msg', '');
		$msg = "The logoff message for $name has been cleared.";
	} else if (strlen($logoff_msg) <= $setting->get('max_logoff_msg_size')) {
		Preferences::save($name, 'logoff_msg', $logoff_msg);
		$msg = "The logoff message for $name has been set.";
	} else {
		$msg = "The logoff message is too large. The logoff message may contain a maximum of " . $setting->get('max_logoff_msg_size') . " characters.";
	}
    $sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>