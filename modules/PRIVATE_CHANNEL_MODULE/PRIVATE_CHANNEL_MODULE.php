<?php
	$MODULE_NAME = "PRIVATE_CHANNEL_MODULE";
	
	DB::loadSQLFile($MODULE_NAME, "members");
    
    Command::register($MODULE_NAME, "", "members.php", "members", "all", "Member list");
	Command::register($MODULE_NAME, "", "sm.php", "sm", "all", "Shows who is in the private channel");
	Command::register($MODULE_NAME, "", "autoinvite.php", "autoinvite", "all", "Allows member to set whether he should be auto-invited to private channel on logon or not");
    Command::register($MODULE_NAME, "guild msg", "join.php", "join", "all", "Join command for guests");
	Command::register($MODULE_NAME, "priv msg", "leave.php", "leave", "all", "Enables Privatechat Kick");
	
	Command::register($MODULE_NAME, "priv", "count.php", "count", "all", "Shows who is in the private channel");
	Command::register($MODULE_NAME, "priv", "count.php", "count", "all", "Shows who is the private channel");
	Command::register($MODULE_NAME, "priv", "count.php", "adv", "all", "Shows Adventurers in private channel");
	Command::register($MODULE_NAME, "priv", "count.php", "agent", "all", "Shows Agents in private channel");
	Command::register($MODULE_NAME, "priv", "count.php", "crat", "all", "Shows Bureaucrats in private channel");
	Command::register($MODULE_NAME, "priv", "count.php", "doc", "all", "Shows Doctors in private channel");
	Command::register($MODULE_NAME, "priv", "count.php", "enf", "all", "Shows Enforcers in private channel");
	Command::register($MODULE_NAME, "priv", "count.php", "eng", "all", "Shows Engineers in private channel");
	Command::register($MODULE_NAME, "priv", "count.php", "fix", "all", "Shows Fixers in private channel");
	Command::register($MODULE_NAME, "priv", "count.php", "keep", "all", "Shows Keepers in private channel");
	Command::register($MODULE_NAME, "priv", "count.php", "ma", "all", "Shows Martial-Artists in private channel");
	Command::register($MODULE_NAME, "priv", "count.php", "mp", "all", "Shows Meta-Physicists in private channel");
	Command::register($MODULE_NAME, "priv", "count.php", "nt", "all", "Shows Nano-Technicians in private channel");
	Command::register($MODULE_NAME, "priv", "count.php", "sol", "all", "Shows Soldiers in private channel");
	Command::register($MODULE_NAME, "priv", "count.php", "shade", "all", "Shows Shades in private channel");
	Command::register($MODULE_NAME, "priv", "count.php", "trader", "all", "Shows Traders in private channel");

	Command::register($MODULE_NAME, "", "kickall.php", "kickall", "guild", "Kicks all from the privgroup");
	Command::register($MODULE_NAME, "", "lock.php", "lock", "rl", "Locks the privgroup");
	Command::register($MODULE_NAME, "", "lock.php", "unlock", "rl", "Unlocks the privgroup");
	
	Command::register($MODULE_NAME, "", "invite.php", "invite", "guild", "Enables Privatechat Join");
	CommandAlias::register($MODULE_NAME, "invite", "inviteuser");
	
	Command::register($MODULE_NAME, "", "kick.php", "kick", "guild", "kick command for guests");
	CommandAlias::register($MODULE_NAME, "kick", "kickuser");
	
	Command::register($MODULE_NAME, "", "add.php", "adduser", "guild", "Adds a player to the members list");
	Command::register($MODULE_NAME, "", "rem.php", "remuser", "guild", "Removes a player from the members list");
	
	Command::register($MODULE_NAME, "", "accept.php", "accept", "all", "Accept a private channel invitation from another player");
	
	Setting::add($MODULE_NAME, "guest_man_join", "Mode of manual private channel join", "edit", "options", "1", "Only for members of guestlist;Everyone", "1;0");
	Setting::add($MODULE_NAME, "guest_color_channel", "Color for Private Channel relay(ChannelName)", "edit", "color", "<font color=#C3C3C3>");
	Setting::add($MODULE_NAME, "guest_color_username", "Color for Private Channel relay(UserName)", "edit", "color", "<font color=#C3C3C3>");
	Setting::add($MODULE_NAME, "guest_color_guild", "Color for Private Channel relay(Text in Guild)", "edit", "color", "<font color=#C3C3C3>");
	Setting::add($MODULE_NAME, "guest_color_guest", "Color for Private Channel relay(Text in Private Channel)", "edit", "color", "<font color=#C3C3C3>");
	Setting::add($MODULE_NAME, "guest_relay", "Relay the Private Channel with the Guild Channel", "edit", "options", "1", "true;false", "1;0");
	Setting::add($MODULE_NAME, "guest_relay_commands", "Relay commands and results from/to Private Channel", "edit", "options", "0", "true;false", "1;0");
	
	//Autoreinvite Players after a botrestart or crash
	Event::register($MODULE_NAME, "connect", "connected.php", "none", "Adds all members as buddies who have auto-invite enabled");
	
	Event::register($MODULE_NAME, "guild", "guest_channel_relay.php", "none", "Private channel relay from guild channel");
	Event::register($MODULE_NAME, "priv", "guest_channel_relay.php", "none", "Private channel relay from priv channel");
	Event::register($MODULE_NAME, "logOn", "logon_autoinvite.php", "none", "Auto-invite members on logon");
	
	//Show Char infos on privjoin
	Event::register($MODULE_NAME, "joinPriv", "notify_priv_join.php", "none", "Displays a message when a character joins the private channel");
	Event::register($MODULE_NAME, "leavePriv", "notify_priv_leave.php", "none", "Displays a message when a character leaves the private channel");
	
	Event::register($MODULE_NAME, "joinPriv", "record_priv_join.php", "none", "Updates the database when a character joins the private channel");
	Event::register($MODULE_NAME, "leavePriv", "record_priv_leave.php", "none", "Updates the database when a character leaves the private channel");
	
	Event::register($MODULE_NAME, "joinPriv", "send_online_list.php", "none", "Sends the online list to people as they join the private channel");

    Help::register($MODULE_NAME, "private_channel", "private_channel.txt", "guild", "Private channel commands");
	Help::register($MODULE_NAME, "join_leave", "joinleave.txt", "all", "Joining and leaving the bot");
	Help::register($MODULE_NAME, "kickall", "kickall.txt", "raidleader", "Kick all players from the Bot");
	Help::register($MODULE_NAME, "lock", "lock.txt", "raidleader", "Lock the private channel");
?>