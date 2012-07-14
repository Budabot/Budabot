<?php

if (preg_match("/^assist$/i", $message)) {
	if (!isset($chatBot->data['assist'])) {
		$msg = "No assist set.";
		$sendto->reply($msg);
		return;
	} else {
		$sendto->reply($chatBot->data['assist']);

		// send message 2 more times (3 total) if used in private channel
		if ($type == "priv") {
			$sendto->reply($chatBot->data['assist']);
			$sendto->reply($chatBot->data['assist']);
		}
	}
} else {
	$syntax_error = true;
}

?>
