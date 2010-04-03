<?php
$MODULE_NAME = "BASIC_GUILD_MODULE";
$PLUGIN_VERSION = 0.1;

	//Setup of the Basic Guild Modules
	bot::event("setup", "$MODULE_NAME/setup.php");

    // Guest Channel Part
	bot::event("guild", "$MODULE_NAME/guest_channel_relay.php", "guest");
	bot::event("priv", "$MODULE_NAME/guest_channel_relay.php", "guest");
	bot::event("joinPriv", "$MODULE_NAME/guest_channel_notify.php", "guest");
	bot::event("leavePriv", "$MODULE_NAME/guest_channel_notify.php", "guest");
	bot::event("priv", "$MODULE_NAME/guest_channel_cmd.php", "guest");
	bot::event("logOn", "$MODULE_NAME/guest_channel_autoinv.php", "guest");
    bot::command("guild", "$MODULE_NAME/guest_channel.php", "guest", "all", "Guest Channel invite/kick");
    bot::command("guild", "$MODULE_NAME/guest_channel.php", "guestlist", "all", "Guest Channel Auto-Invitelist");
    bot::command("guild", "$MODULE_NAME/guest_channel.php", "guests", "all", "Guest Channellist");
    bot::command("msg", "$MODULE_NAME/guest_channel_telljoin.php", "guestjoin", "all", "Join command for guests");
	bot::regGroup("guest", $MODULE_NAME, "Guest Channel", "guest", "guests", "guestlist", "guestjoin");
	bot::addsetting("guest_cmd", "Enable Organisation commands for guests", "edit", "0", "ON;OFF", "1;0");
	bot::addsetting("guest_man_join", "Mode of manual guestchannel join", "edit", "1", "Only for members of guestlist;Everyone", "1;0");
	bot::addsetting("guest_color_channel", "Color for Guestchannelrelay(ChannelName)", "edit", "<font color=#C3C3C3>", "color");
	bot::addsetting("guest_color_username", "Color for Guestchannelrelay(UserName)", "edit", "<font color=#C3C3C3>", "color");
	bot::addsetting("guest_color_guild", "Color for Guestchannelrelay(Text in Guild)", "edit", "<font color=#C3C3C3>", "color");
	bot::addsetting("guest_color_guest", "Color for Guestchannelrelay(Text in Guestchannel)", "edit", "<font color=#C3C3C3>", "color");		
	bot::addsetting("guest_relay", "Relay of the Guestchannel", "edit", "2", "Automatic;Always on;Always off", "2;1;0");
	bot::addsetting("guest_relay_commands", "Relay commands and results from/to guestchannel", "edit", "0", "ON;OFF", "1;0");
	
	// Logon Handling
	bot::event("logOn", "$MODULE_NAME/logon_guild.php", "none", "Shows a logon from a member");
	bot::event("logOff", "$MODULE_NAME/logoff_guild.php", "none", "Shows a logoff from a member");
	bot::command("guild", "$MODULE_NAME/logon_msg.php", "logon", "all", "Sets a Logon Msg");
	bot::command("msg", "$MODULE_NAME/logon_msg.php", "logon", "guild", "Sets a Logon Msg");
	bot::command("priv", "$MODULE_NAME/logon_msg.php", "logon", "all", "Sets a Logon Msg");

    // Afk Check
	bot::event("guild", "$MODULE_NAME/afk_check.php", "afk");
	bot::command("guild", "$MODULE_NAME/afk.php", "afk", "all", "Sets a member afk");
	bot::command("guild", "$MODULE_NAME/kiting.php", "kiting", "all", "Sets a member afk kiting");

	//Verifies the Onlinelist every 1hour
	bot::event("1hour", "$MODULE_NAME/online_check.php", "online");

	//Verifies the Guestchannellist every 1hour
	bot::event("1hour", "$MODULE_NAME/guest_channel_check.php", "guest");

    // Alternative Characters
	bot::command("guild", "$MODULE_NAME/alts.php", "alts", "all", "Alt Char handling");
	bot::command("msg", "$MODULE_NAME/alts.php", "alts", "guild", "Alt Char handling");
	bot::command("priv", "$MODULE_NAME/alts.php", "alts", "all", "Alt Char handling");

    // Checks if a player is online
	bot::command("guild", "$MODULE_NAME/is_online.php", "is", "all", "Checks if a player is online");
	bot::command("priv", "$MODULE_NAME/is_online.php", "is", "all", "Checks if a player is online");
	bot::command("msg", "$MODULE_NAME/is_online.php", "is", "guild", "Checks if a player is online");
	bot::event("logOn", "$MODULE_NAME/is_online.php", "is");
	bot::event("logOff", "$MODULE_NAME/is_online.php", "is");

    // Show orgmembers
	bot::command("guild", "$MODULE_NAME/orgmembers.php", "orgmembers", "all", "Show the Members(sorted by name) of the org");
	bot::command("msg", "$MODULE_NAME/orgmembers.php", "orgmembers", "guild", "Show the Members(sorted by name) of the org");
	bot::command("priv", "$MODULE_NAME/orgmembers.php", "orgmembers", "all", "Show the Members(sorted by name) of the org");
	bot::command("guild", "$MODULE_NAME/orgranks.php", "orgranks", "all", "Show the Members(sorted by rank) of the org");
	bot::command("msg", "$MODULE_NAME/orgranks.php", "orgranks", "guild", "Show the Members(sorted by rank) of the org");
	bot::command("priv", "$MODULE_NAME/orgranks.php", "orgranks", "all", "Show the Members(sorted by rank) of the org");

	//Force an update of the org rooster
	bot::command("msg", "$MODULE_NAME/updateorg.php", "updateorg", "mod", "Forcing an update of the org roster");
	
	//Tell and Tellall
	bot::command("guild", "$MODULE_NAME/tell.php", "tell", "rl", "Repeats an message 3times in Orgchat");
	bot::command("guild", "$MODULE_NAME/tell.php", "tellall", "rl", "Sends a tell to all online guildmembers");
	
	//Lastseen
	bot::command("msg", "$MODULE_NAME/lastseen.php", "lastseen", "guild", "Shows the logoff time of a player");
	bot::command("guild", "$MODULE_NAME/lastseen.php", "lastseen", "all", "Shows the logoff time of a player");
		
	//Helpfile
    bot::help("afk_kiting", "$MODULE_NAME/afk_kiting.txt", "guild", "Set yourself AFK/Kiting", "Basic Guild Commands");
    bot::help("alts", "$MODULE_NAME/alts.txt", "guild", "How to set alts", "Basic Guild Commands");
    bot::help("GuestChannel", "$MODULE_NAME/guestchannel.txt", "guild", "Guestchannel", "Basic Guild Commands");
    bot::help("IsOnline", "$MODULE_NAME/isonline.txt", "guild", "Checking if a player is online", "Basic Guild Commands");
    bot::help("lastseen", "$MODULE_NAME/lastseen.txt", "guild", "Check when an orgmember was online", "Basic Guild Commands");
    bot::help("LogOnMsg", "$MODULE_NAME/logonmsg.txt", "guild", "Changing your logon message", "Basic Guild Commands");
    bot::help("OrgMembers", "$MODULE_NAME/orgmembers_orgranks.txt", "guild", "Show current OrgMembers", "Basic Guild Commands");    
    bot::help("tell_guild", "$MODULE_NAME/tell.txt", "guild", "Repeat a msg 3times/Send a tell to online members", "Basic Guild Commands");
    bot::help("updateorg", "$MODULE_NAME/updateorg.txt", "mod", "Force an update of orgrooster", "Basic Guild Commands");    
?>