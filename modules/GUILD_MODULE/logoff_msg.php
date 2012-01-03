<?php

if (preg_match("/^logoff$/i", $message)) {
	$logoff_msg = Preferences::get($sender, 'logoff_msg');

	if ($logoff_msg === false || $logoff_msg == '') {
		$msg = "Your logoff message has not been set.";
	} else {
		$msg = "{$sender} logoff: {$logoff_msg}";
	}
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^logoff (.+)$/i", $message, $arr)) {
	$logoff_msg = $arr[1];
	
	if ($logoff_msg == 'clear') {
		Preferences::save($sender, 'logoff_msg', '');
		$msg = "Your logoff message has been cleared.";
	} else if (strlen($logoff_msg) <= $setting->get('max_logoff_msg_size')) {
		Preferences::save($sender, 'logoff_msg', $logoff_msg);
		$msg = "Your logoff message has been set.";
	} else {
		$msg = "Your logoff message is too large. Your logoff message may contain a maximum of " . $setting->get('max_logoff_msg_size') . " characters.";
	}
    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>


?>
