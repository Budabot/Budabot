<?php
	if ($this->vars['dimension'] == 2) {
		$MODULE_NAME = "NEUTNET_MODULE";
		
		// add neutnet bots to whitelist
		$NUM_BOTS = 14;
		for ($i = 1; $i <= $NUM_BOTS; $i++) {
			Whitelist::add("Neutnet$i", $MODULE_NAME);
		}
		
		bot::event("msg", "$MODULE_NAME/neutnet.php", 'none', 'Relays neutnet shopping messages to a channel/player');
		
		bot::help("neutnet", "$MODULE_NAME/neutnet.txt", "all", "Shows the commands needed to register a bot with Neutnet", "Neutnet");
	}
?>
