<?php

if (preg_match("/^leaderecho on$/i", $message)) {
	$setting->save("leaderecho", "1");
	$chatBot->sendPrivate("Leader echo has been <green>enabled<end>");
} else if (preg_match("/^leaderecho off$/i", $message)) {
	$setting->save("leaderecho", "0");
	$chatBot->sendPrivate("Leader echo has been <green>disabled<end>");
} else if (preg_match("/^leaderecho$/i", $message)) {
	if ($setting->get("leaderecho") == 1) {
		$msg = "Leader echo is currently <green>enabled<end>";
	} else {
		$msg = "Leader echo is currently <red>disabled<end>";
	}
	$chatBot->sendPrivate($msg);
} else {
	$syntax_error = true;
}

?>
