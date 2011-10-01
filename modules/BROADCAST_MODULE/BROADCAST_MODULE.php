<?php
	DB::loadSQLFile($MODULE_NAME, 'broadcast');
	
	Event::register($MODULE_NAME, "setup", "setup.php");
	
	Event::register($MODULE_NAME, "msg", "incoming_broadcast.php", 'none', 'Relays incoming messages to the guild/private channel');
	Event::register($MODULE_NAME, "extPriv", "incoming_broadcast.php", 'none', 'Relays incoming messages to the guild/private channel');
	Event::register($MODULE_NAME, "extJoinPrivRequest", "accept_invite.php", 'none', 'Accepts invites from bots on the broadcast list');
	Event::register($MODULE_NAME, "connect", "dnet_request_invite.php", 'none', 'Requests invite from Dnet');
	
	Command::register($MODULE_NAME, "", "broadcast.php", "broadcast", "mod", "View/edit the broadcast bots list");
	Command::register($MODULE_NAME, "", "neutnet.php", "neutnet", "mod", "Enable/disable Neutnet support (RK 2 only)");
	Command::register($MODULE_NAME, "", "dnet.php", "dnet", "mod", "Enable/disable Dnet support (RK 1 only)");
	
	Setting::add($MODULE_NAME, "broadcast_to_guild", "Send broadcast message to guild channel", "edit", "options", "1", "true;false", "1;0");
	Setting::add($MODULE_NAME, "broadcast_to_privchan", "Send broadcast message to private channel", "edit", "options", "0", "true;false", "1;0");
	Setting::add($MODULE_NAME, "dnet_status", "Enable Dnet support", "noedit", "options", "0", "true;false", "1;0");
	
	Help::register($MODULE_NAME, "neutnet", "neutnet.txt", "mod", "How to enable Neutnet support");
	Help::register($MODULE_NAME, "dnet", "dnet.txt", "mod", "How to enable Dnet support");
	Help::register($MODULE_NAME, "broadcast", "broadcast.txt", "all", "How to manage the broadcast list");
?>
