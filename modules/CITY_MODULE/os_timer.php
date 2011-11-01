<?php

// create a timer for 15m when an OS/AS is launched (so org knows when they can launch again)
// [Org Msg] Blammo! Player has launched an orbital attack!

if (preg_match("/^Blammo! (.+) has launched an orbital attack!$/i", $message, $arr)) {
	$chatBot->send("OS !timer was set for 15 minutes", "guild");
	$orgName = $chatBot->vars["my_guild"];

	$launcher = $arr[1];

	for ($i = 1; $i <= 10; $i++) {
		$name = "$orgName OS/AS $i";
		if (Timer::getTimer($name) == null) {
			$timer = time() + (15*60); // set timer for 15 minutes
			Timer::add_timer($name, $launcher, 'guild', $timer);
			break;
		}
	}
}

?>
