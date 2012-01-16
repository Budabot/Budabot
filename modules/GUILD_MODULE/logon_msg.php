<?php

if (preg_match("/^logon$/i", $message)) {
	$logon_msg = Preferences::get($sender, 'logon_msg');

	if ($logon_msg === false || $logon_msg == '') {
		$msg = "Your logon message has not been set.";
	} else {
		$msg = "{$sender} logon: {$logon_msg}";
	}
    $sendto->reply($msg);
} else if (preg_match("/^logon (.+)$/i", $message, $arr)) {
	$logon_msg = $arr[1];
	
	if ($logon_msg == 'clear') {
		Preferences::save($sender, 'logon_msg', '');
		$msg = "Your logon message has been cleared.";
	} else if (strlen($logon_msg) <= $setting->get('max_logon_msg_size')) {
		Preferences::save($sender, 'logon_msg', $logon_msg);
		$msg = "Your logon message has been set.";
	} else {
		$msg = "Your logon message is too large. Your logon message may contain a maximum of " . $setting->get('max_logon_msg_size') . " characters.";
	}
    $sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
