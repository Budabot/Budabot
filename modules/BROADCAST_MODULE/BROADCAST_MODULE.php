<?php
	$db->loadSQLFile($MODULE_NAME, 'broadcast');

	$event->register($MODULE_NAME, "setup", "setup.php");

	$event->register($MODULE_NAME, "msg", "incoming_broadcast.php", 'Relays incoming messages to the guild/private channel');
	$event->register($MODULE_NAME, "extPriv", "incoming_broadcast.php", 'Relays incoming messages to the guild/private channel');

	$command->register($MODULE_NAME, "", "broadcast.php", "broadcast", "mod", "View/edit the broadcast bots list", "broadcast.txt");
	$command->register($MODULE_NAME, "", "dnet.php", "dnet", "mod", "Enable/disable Dnet support (RK 1 only)", "dnet.txt");

	$setting->add($MODULE_NAME, "broadcast_to_guild", "Send broadcast message to guild channel", "edit", "options", "1", "true;false", "1;0");
	$setting->add($MODULE_NAME, "broadcast_to_privchan", "Send broadcast message to private channel", "edit", "options", "0", "true;false", "1;0");
	$setting->add($MODULE_NAME, "dnet_status", "Enable Dnet support", "noedit", "options", "0", "true;false", "1;0");
?>
