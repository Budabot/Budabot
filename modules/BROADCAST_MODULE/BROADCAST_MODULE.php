<?php
	$MODULE_NAME = "BROADCAST_MODULE";
	
	DB::loadSQLFile($MODULE_NAME, 'broadcast');
	
	Event::register($MODULE_NAME, "setup", "setup.php");
	
	Event::register($MODULE_NAME, "msg", "incoming_broadcast.php", 'none', 'Relays incoming messages to the guild/private channel');
	
	Command::register($MODULE_NAME, "", "broadcast.php", "broadcast", "mod", "View/edit the broadcast bots list");
	
	Setting::add($MODULE_NAME, "broadcast_to_guild", "Send broadcast message to guild channel", "edit", "options", "1", "true;false", "1;0");
	Setting::add($MODULE_NAME, "broadcast_to_privchan", "Send broadcast message to private channel", "edit", "options", "0", "true;false", "1;0");
	
	Help::register($MODULE_NAME, "neutnet", "neutnet.txt", "all", "Shows the commands needed to register a bot with Neutnet");
	Help::register($MODULE_NAME, "broadcast", "broadcast.txt", "all", "How to manage the broadcast list");
?>
