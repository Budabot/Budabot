<?php

if (preg_match("/^heal$/i", $message)) {
  	if (!isset($chatBot->data['heal_assist'])) {
		$msg = "No heal assist set.";
		$sendto->reply($msg);
		return;
	} else {
		$sendto->reply($chatBot->data['heal_assist']);
	
		// send message 2 more times (3 total) if used in private channel
		if ($type == "priv") {
			$sendto->reply($chatBot->data['heal_assist']);
			$sendto->reply($chatBot->data['heal_assist']);
		}
	}
} else {
	$syntax_error = true;
}

?>