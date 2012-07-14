<?php

if (preg_match("/^logonadmin ([a-zA-Z0-9-]+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$logon_msg = Preferences::get($name, 'logon_msg');

	if ($logon_msg === false || $logon_msg == '') {
		$msg = "The logon message for $name has not been set.";
	} else {
		$msg = "{$name} logon: {$logon_msg}";
	}
    $sendto->reply($msg);
} else if (preg_match("/^logonadmin ([a-zA-Z0-9-]+) (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$logon_msg = $arr[2];

	if ($logon_msg == 'clear') {
		Preferences::save($name, 'logon_msg', '');
		$msg = "The logon message for $name has been cleared.";
	} else if (strlen($logon_msg) <= $setting->get('max_logon_msg_size')) {
		Preferences::save($name, 'logon_msg', $logon_msg);
		$msg = "The logon message for $name has been set.";
	} else {
		$msg = "The logon message is too large. The logon message may contain a maximum of " . $setting->get('max_logon_msg_size') . " characters.";
	}
    $sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
