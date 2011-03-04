<?php

if (preg_match("/^assist$/i", $message)) {
  	if (!isset($chatBot->data['assist'])) {
		$msg = "No assist set atm.";
		$chatBot->send($msg, $sendto);
		return;
	} else {
		$chatBot->send($chatBot->data['assist'], $sendto);
	
		// send message 2 more times (3 total) if used in private channel
		if ($type == "priv") {
			$chatBot->send($chatBot->data['assist'], $sendto);
			$chatBot->send($chatBot->data['assist'], $sendto);
		}
	}
} else {
	$syntax_error = true;
}

?>