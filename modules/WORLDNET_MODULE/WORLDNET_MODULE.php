<?php
	Event::register($MODULE_NAME, "extPriv", "incoming_message.php", 'none', 'Relays incoming messages to the guild/private channel');
	Event::register($MODULE_NAME, "extJoinPrivRequest", "accept_invite.php", 'none', 'Accepts invites from worldnet');
	Event::register($MODULE_NAME, "connect", "connect.php", 'none', 'Adds worldnet to buddylist');
	Event::register($MODULE_NAME, "logOn", "logon.php", 'none', 'Requests to join worldnet private channel');
	
	Setting::add($MODULE_NAME, 'worldnet_bot', 'Name of bot', 'edit', "text", "Worldnet", "Worldnet;Dnet", '', 'mod', 'worldnet');
	Setting::add($MODULE_NAME, "broadcast_to_guild_members", "Send worldnet messages guild members via tells", "edit", "options", "0", "true;false", "1;0");
	
	// channels
	Setting::add($MODULE_NAME, "worldnet_general_status", "Enable General channel messages", "edit", "options", "1", "true;false", "1;0");
	Setting::add($MODULE_NAME, "worldnet_pvm_status", "Enable PVM channel messages", "edit", "options", "1", "true;false", "1;0");
	Setting::add($MODULE_NAME, "worldnet_wtb_status", "Enable WTB channel messages", "edit", "options", "1", "true;false", "1;0");
	Setting::add($MODULE_NAME, "worldnet_wts_status", "Enable WTS channel messages", "edit", "options", "1", "true;false", "1;0");
	
	// colors
	Setting::add($MODULE_NAME, 'worldnet_channel_color', "Color of channel text in worldnet messages", 'edit', "color", "<font color='#FFFFFF'>");
	Setting::add($MODULE_NAME, 'worldnet_message_color', "Color of message text in worldnet messages", 'edit', "color", "<font color='#FFFFFF'>");
	Setting::add($MODULE_NAME, 'worldnet_sender_color', "Color of sender text in worldnet messages", 'edit', "color", "<font color='#FFFFFF'>");

	Help::register($MODULE_NAME, "worldnet", "worldnet.txt", "all", "How to use Worldnet");
?>