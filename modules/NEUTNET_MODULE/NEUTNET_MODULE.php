<?php

	$MODULE_NAME = "NEUTNET_MODULE";
	$PLUGIN_VERSION = 0.1;

	bot::event("msg", "$MODULE_NAME/neutnet.php", 'none', 'Relays neutnet shopping messages to a channel/player');
	
	bot::help("neutnet", "$MODULE_NAME/neutnet.txt", "all", "Shows the commands needed to register a bot with Neutnet", "Neutnet");
?>
