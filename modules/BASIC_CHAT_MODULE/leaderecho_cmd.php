<?php
   
if (preg_match("/^leaderecho on$/i", $message)) {
	$setting->save("leaderecho", "1");
	$chatBot->send("Leader echo has been <green>enabled<end>", 'priv');
} else if (preg_match("/^leaderecho off$/i", $message)) {
	$setting->save("leaderecho", "0");
	$chatBot->send("Leader echo has been <green>disabled<end>", 'priv');	
} else if (preg_match("/^leaderecho$/i", $message)) {
	if ($setting->get("leaderecho") == 1) {
		$msg = "Leader echo is currently <green>enabled<end>";
	} else {
		$msg = "Leader echo is currently <red>disabled<end>";
	}
	$chatBot->send($msg, 'priv');
} else {
	$syntax_error = true;
}

?>