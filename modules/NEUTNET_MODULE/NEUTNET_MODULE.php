<?php
	$MODULE_NAME = "NEUTNET_MODULE";

	// add neutnet bots to whitelist
	$NUM_BOTS = 14;
	for ($i = 1; $i <= $NUM_BOTS; $i++) {
		Whitelist::add("Neutnet$i", $MODULE_NAME);
	}
	
	Event::register($MODULE_NAME, "msg", "neutnet.php", 'none', 'Relays neutnet messages to a channel/player');
	
	bot::help($MODULE_NAME, "neutnet", "neutnet.txt", "all", "Shows the commands needed to register a bot with Neutnet");
?>
