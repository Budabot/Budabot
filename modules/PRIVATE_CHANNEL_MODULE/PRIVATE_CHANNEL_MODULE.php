<?php
	$MODULE_NAME = "PRIVATE_CHANNEL_MODULE";
	
	bot::loadSQLFile($MODULE_NAME, "private_chat");
    
    bot::command("", "$MODULE_NAME/members.php", "members", "all", "Member list");
	bot::command("", "$MODULE_NAME/sm.php", "sm", "all", "Shows who is in the private channel");
	bot::command("", "$MODULE_NAME/count.php", "count", "all", "Shows who is in the private channel");
	bot::command("", "$MODULE_NAME/autoinvite.php", "autoinvite", "all", "Allows member to set whether he should be auto-invited to private channel on logon or not");
    bot::command("guild msg", "$MODULE_NAME/join.php", "join", "all", "Join command for guests");
	bot::command("priv msg", "$MODULE_NAME/leave.php", "leave", "all", "Enables Privatechat Kick");

	bot::command("", "$MODULE_NAME/kickall.php", "kickall", "mod", "Kicks all from the privgroup");
	bot::command("", "$MODULE_NAME/lock.php", "lock", "rl", "Locks the privgroup");
	bot::command("", "$MODULE_NAME/lock.php", "unlock", "rl", "Unlocks the privgroup");
	
	bot::command("", "$MODULE_NAME/invite.php", "inviteuser", "all", "Enables Privatechat Join");
	bot::command("", "$MODULE_NAME/kick.php", "kickuser", "all", "kick command for guests");
	bot::command("", "$MODULE_NAME/invite.php", "invite", "all", "Enables Privatechat Join");
	bot::command("", "$MODULE_NAME/kick.php", "kick", "all", "kick command for guests");
	bot::command("", "$MODULE_NAME/add.php", "adduser", "all", "Enables Privatechat Join");
	bot::command("", "$MODULE_NAME/rem.php", "remuser", "all", "Enables Privatechat Join");
	
	bot::addsetting($MODULE_NAME, "guest_man_join", "Mode of manual private channel join", "edit", "1", "Only for members of guestlist;Everyone", "1;0");
	bot::addsetting($MODULE_NAME, "guest_color_channel", "Color for Private Channel relay(ChannelName)", "edit", "<font color=#C3C3C3>", "color");
	bot::addsetting($MODULE_NAME, "guest_color_username", "Color for Private Channel relay(UserName)", "edit", "<font color=#C3C3C3>", "color");
	bot::addsetting($MODULE_NAME, "guest_color_guild", "Color for Private Channel relay(Text in Guild)", "edit", "<font color=#C3C3C3>", "color");
	bot::addsetting($MODULE_NAME, "guest_color_guest", "Color for Private Channel relay(Text in Private Channel)", "edit", "<font color=#C3C3C3>", "color");
	bot::addsetting($MODULE_NAME, "guest_relay", "Relay of the Private Channel", "edit", "1", "ON;OFF", "1;0");
	bot::addsetting($MODULE_NAME, "guest_relay_commands", "Relay commands and results from/to Private Channel", "edit", "0", "ON;OFF", "1;0");
	
	//Autoreinvite Players after a botrestart or crash
	bot::event($MODULE_NAME, "setup", "setup.php", "none", "Clears the private channel list of players");
	bot::event($MODULE_NAME, "connected", "connected.php", "none", "Adds all members as buddies who have auto-invite enabled");
	
	bot::event($MODULE_NAME, "guild", "guest_channel_relay.php", "none", "Private channel relay from guild channel");
	bot::event($MODULE_NAME, "priv", "guest_channel_relay.php", "none", "Private channel relay from priv channel");
	bot::event($MODULE_NAME, "logOn", "logon_autoinvite.php", "none", "Auto-invite members on logon");
	
	//Show Char infos on privjoin
	bot::event($MODULE_NAME, "joinPriv", "notify_priv_join.php", "none", "Displays a message when a character joins the private channel");
	bot::event($MODULE_NAME, "leavePriv", "notify_priv_leave.php", "none", "Displays a message when a character leaves the private channel");
	
	bot::event($MODULE_NAME, "joinPriv", "record_priv_join.php", "none", "Updates the database when a character joins the private channel");
	bot::event($MODULE_NAME, "leavePriv", "record_priv_leave.php", "none", "Updates the database when a character leaves the private channel");
	
	bot::event($MODULE_NAME, "joinPriv", "send_online_list.php", "none", "Sends the online list to people as they join the private channel");
	
	//Verifies the Private Channel list every 1hour
	bot::event($MODULE_NAME, "1hour", "guest_channel_check.php", "guest", "Private channel check");

    bot::help($MODULE_NAME, "private_channel", "private_channel.txt", "guild", "Private channel commands");
	bot::help($MODULE_NAME, "join_leave", "joinleave.txt", "all", "Joining and leaving the bot");
	bot::help($MODULE_NAME, "kickall", "kickall.txt", "raidleader", "Kick all players from the Bot");
	bot::help($MODULE_NAME, "lock", "lock.txt", "raidleader", "Lock the private channel");
?>