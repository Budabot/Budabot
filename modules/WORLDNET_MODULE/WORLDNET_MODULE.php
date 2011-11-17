<?php
	// since settings for channels are added dynamically, we need to add them manually
	$db->query("SELECT * FROM settings_<myname> WHERE module = '$MODULE_NAME' AND name LIKE 'worldnet%status'");
	$data = $db->fObject('all');
	forEach ($data as $row) {
		Setting::add($row->module, $row->name, $row->description, $row->mode, $row->type, $row->value, $row->options, $row->intoptions, $row->admin, $row->help);
	}

	Event::register($MODULE_NAME, "extPriv", "incoming_message.php", 'none', 'Relays incoming messages to the guild/private channel');
	Event::register($MODULE_NAME, "extJoinPrivRequest", "accept_invite.php", 'none', 'Accepts invites from worldnet');
	Event::register($MODULE_NAME, "connect", "connect.php", 'none', 'Adds worldnet to buddylist');
	Event::register($MODULE_NAME, "logOn", "logon.php", 'none', 'Requests to join worldnet private channel');
	
	Setting::add($MODULE_NAME, 'worldnet_bot', 'Name of bot', 'edit', "text", "Worldnet", "Worldnet;Dnet", '', 'mod', 'worldnet');
	Setting::add($MODULE_NAME, "broadcast_to_guild_members", "Send worldnet messages guild members via tells", "edit", "options", "0", "true;false", "1;0");
	
	// colors
	Setting::add($MODULE_NAME, 'worldnet_channel_color', "Color of channel text in worldnet messages", 'edit', "color", "<font color='#FFFFFF'>");
	Setting::add($MODULE_NAME, 'worldnet_message_color', "Color of message text in worldnet messages", 'edit', "color", "<font color='#FFFFFF'>");
	Setting::add($MODULE_NAME, 'worldnet_sender_color', "Color of sender text in worldnet messages", 'edit', "color", "<font color='#FFFFFF'>");

	Help::register($MODULE_NAME, "worldnet", "worldnet.txt", "all", "How to use Worldnet");
?>