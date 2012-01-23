<?php
	$db->loadSQLFile($MODULE_NAME, "private_chat");
    
    $command->register($MODULE_NAME, "", "members.php", "members", "all", "Member list", 'private_channel.txt');
	$command->register($MODULE_NAME, "", "sm.php", "sm", "all", "Shows who is in the private channel", 'private_channel.txt');
    $command->register($MODULE_NAME, "guild msg", "join.php", "join", "member", "Join command for guests", 'private_channel.txt');
	$command->register($MODULE_NAME, "priv msg", "leave.php", "leave", "all", "Enables Privatechat Kick", 'private_channel.txt');
	$command->register($MODULE_NAME, "", "add.php", "adduser", "guild", "Adds a player to the members list", 'private_channel.txt');
	$command->register($MODULE_NAME, "", "rem.php", "remuser", "guild", "Removes a player from the members list", 'private_channel.txt');
	$command->register($MODULE_NAME, "", "invite.php", "inviteuser", "guild", "Invite players to the private channel", 'private_channel.txt');
	$command->register($MODULE_NAME, "", "kick.php", "kickuser", "guild", "Kick players from the private channel", 'private_channel.txt');
	$command->register($MODULE_NAME, "", "autoinvite.php", "autoinvite", "member", "Enable or disable autoinvite", "autoinvite.txt");
	$command->register($MODULE_NAME, "", "count.php", "count", "all", "Shows how many characters are in the private channel", "count.txt");
	$command->register($MODULE_NAME, "", "kickall.php", "kickall", "guild", "Kicks all from the private channel", "kickall.txt");
	$command->register($MODULE_NAME, "", "lock.php", "lock", "rl", "Locks the private channel", "lock.txt");
	$command->register($MODULE_NAME, "", "lock.php", "unlock", "rl", "Unlocks the private channel", "lock.txt");
	$command->register($MODULE_NAME, "", "accept.php", "accept", "mod", "Accept a private channel invitation from another player", "accept.txt");

	$setting->add($MODULE_NAME, "guest_color_channel", "Color for Private Channel relay(ChannelName)", "edit", "color", "<font color=#C3C3C3>");
	$setting->add($MODULE_NAME, "guest_color_guild", "Private Channel relay color in guild channel", "edit", "color", "<font color=#C3C3C3>");
	$setting->add($MODULE_NAME, "guest_color_guest", "Private Channel relay color in private channel", "edit", "color", "<font color=#C3C3C3>");
	$setting->add($MODULE_NAME, "guest_relay", "Relay the Private Channel with the Guild Channel", "edit", "options", "1", "true;false", "1;0");
	$setting->add($MODULE_NAME, "guest_relay_commands", "Relay commands and results from/to Private Channel", "edit", "options", "0", "true;false", "1;0");
	$setting->add($MODULE_NAME, "priv_status", "Private channel status", "edit", "options", "1", "open;closed", "1;0");
	$setting->add($MODULE_NAME, "priv_status_reason", "Reason for private channel status", "edit", "text", "none");

	$event->register($MODULE_NAME, "connect", "connected.php", "Adds all members as buddies who have auto-invite enabled");
	$event->register($MODULE_NAME, "guild", "guest_channel_relay.php", "Private channel relay from guild channel");
	$event->register($MODULE_NAME, "priv", "guest_channel_relay.php", "Private channel relay from priv channel");
	$event->register($MODULE_NAME, "logOn", "logon_autoinvite.php", "Auto-invite members on logon");
	$event->register($MODULE_NAME, "joinPriv", "notify_priv_join.php", "Displays a message when a character joins the private channel");
	$event->register($MODULE_NAME, "leavePriv", "notify_priv_leave.php", "Displays a message when a character leaves the private channel");
	$event->register($MODULE_NAME, "joinPriv", "record_priv_join.php", "Updates the database when a character joins the private channel");
	$event->register($MODULE_NAME, "leavePriv", "record_priv_leave.php", "Updates the database when a character leaves the private channel");
	$event->register($MODULE_NAME, "joinPriv", "send_online_list.php", "Sends the online list to people as they join the private channel");
?>