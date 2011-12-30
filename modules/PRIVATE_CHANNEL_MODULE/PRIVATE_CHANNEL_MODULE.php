<?php
	$db->loadSQLFile($MODULE_NAME, "private_chat");
    
    $command->register($MODULE_NAME, "", "members.php", "members", "all", "Member list", 'private_channel');
	$command->register($MODULE_NAME, "", "sm.php", "sm", "all", "Shows who is in the private channel", 'private_channel');
	$command->register($MODULE_NAME, "", "autoinvite.php", "autoinvite", "member", "Allows members to set whether he should be auto-invited to private channel on logon or not");
    $command->register($MODULE_NAME, "guild msg", "join.php", "join", "member", "Join command for guests", 'private_channel');
	$command->register($MODULE_NAME, "priv msg", "leave.php", "leave", "all", "Enables Privatechat Kick", 'private_channel');
	$command->register($MODULE_NAME, "", "count.php", "count", "all", "Shows how many characters are in the private channel");
	$command->register($MODULE_NAME, "", "kickall.php", "kickall", "guild", "Kicks all from the private channel");
	$command->register($MODULE_NAME, "", "lock.php", "lock", "rl", "Locks the private channel");
	$command->register($MODULE_NAME, "", "lock.php", "unlock", "rl", "Unlocks the private channel", 'lock');
	$command->register($MODULE_NAME, "", "add.php", "adduser", "guild", "Adds a player to the members list", 'private_channel');
	$command->register($MODULE_NAME, "", "rem.php", "remuser", "guild", "Removes a player from the members list", 'private_channel');
	$command->register($MODULE_NAME, "", "accept.php", "accept", "mod", "Accept a private channel invitation from another player");
	
	$command->register($MODULE_NAME, "api msg priv guild", "invite.php", "invite", "guild", "Invite players to the private channel", 'private_channel');
	CommandAlias::register($MODULE_NAME, "invite", "inviteuser");
	
	$command->register($MODULE_NAME, "api msg priv guild", "kick.php", "kick", "guild", "Kick players from private channel", 'private_channel');
	CommandAlias::register($MODULE_NAME, "kick", "kickuser");

	Setting::add($MODULE_NAME, "guest_color_channel", "Color for Private Channel relay(ChannelName)", "edit", "color", "<font color=#C3C3C3>");
	Setting::add($MODULE_NAME, "guest_color_guild", "Private Channel relay color in guild channel", "edit", "color", "<font color=#C3C3C3>");
	Setting::add($MODULE_NAME, "guest_color_guest", "Private Channel relay color in private channel", "edit", "color", "<font color=#C3C3C3>");
	Setting::add($MODULE_NAME, "guest_relay", "Relay the Private Channel with the Guild Channel", "edit", "options", "1", "true;false", "1;0");
	Setting::add($MODULE_NAME, "guest_relay_commands", "Relay commands and results from/to Private Channel", "edit", "options", "0", "true;false", "1;0");
	Setting::add($MODULE_NAME, "priv_status", "Private channel status", "edit", "options", "1", "open;closed", "1;0");
	Setting::add($MODULE_NAME, "priv_status_reason", "Reason for private channel status", "edit", "text", "none");

	Event::register($MODULE_NAME, "connect", "connected.php", "Adds all members as buddies who have auto-invite enabled");
	Event::register($MODULE_NAME, "guild", "guest_channel_relay.php", "Private channel relay from guild channel");
	Event::register($MODULE_NAME, "priv", "guest_channel_relay.php", "Private channel relay from priv channel");
	Event::register($MODULE_NAME, "logOn", "logon_autoinvite.php", "Auto-invite members on logon");
	Event::register($MODULE_NAME, "joinPriv", "notify_priv_join.php", "Displays a message when a character joins the private channel");
	Event::register($MODULE_NAME, "leavePriv", "notify_priv_leave.php", "Displays a message when a character leaves the private channel");
	Event::register($MODULE_NAME, "joinPriv", "record_priv_join.php", "Updates the database when a character joins the private channel");
	Event::register($MODULE_NAME, "leavePriv", "record_priv_leave.php", "Updates the database when a character leaves the private channel");
	Event::register($MODULE_NAME, "joinPriv", "send_online_list.php", "Sends the online list to people as they join the private channel");

    Help::register($MODULE_NAME, "private_channel", "private_channel.txt", "guild", "Private channel commands");
	Help::register($MODULE_NAME, "kickall", "kickall.txt", "raidleader", "Kick all players from the Bot");
	Help::register($MODULE_NAME, "lock", "lock.txt", "raidleader", "Lock and unlock the private channel");
	Help::register($MODULE_NAME, "count", "count.txt", "all", "How to use count");
	Help::register($MODULE_NAME, "accept", "accept.txt", "mod", "How to accept a private channel invitation from another character");
	Help::register($MODULE_NAME, "autoinvite", "autoinvite.txt", "member", "How to change your autoinvite preference");
?>