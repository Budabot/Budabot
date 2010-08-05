<?php
	$MODULE_NAME = "BOTCHANNEL_MODULE";
	
	bot::loadSQLFile($MODULE_NAME, "private_chat");
    
    bot::command("", "$MODULE_NAME/members.php", "members", "all", "Guest Channel Auto-Invitelist");
    bot::command("", "$MODULE_NAME/onlineguests.php", "onlineguests", "all", "Guest Channellist");
	bot::command("", "$MODULE_NAME/autoinvite.php", "autoinvite", "all", "Allows member to set whether he should be auto-invited to guest channel on logon or not");
    bot::command("guild msg", "$MODULE_NAME/join.php", "join", "all", "Join command for guests");
	bot::command("priv msg", "$MODULE_NAME/leave.php", "leave", "all", "Enables Privatechat Kick");

	bot::command("", "$MODULE_NAME/kickall.php", "kickall", "mod", "Kicks all from the privgroup");
	bot::command("", "$MODULE_NAME/lock.php", "lock", "rl", "Locks the privgroup");
	bot::command("", "$MODULE_NAME/lock.php", "unlock", "rl", "Unlocks the privgroup");
	
	bot::command("", "$MODULE_NAME/invite.php", "inviteuser", "all", "Enables Privatechat Join");
	bot::command("", "$MODULE_NAME/kick.php", "kickuser", "all", "kick command for guests");
	bot::command("", "$MODULE_NAME/add.php", "adduser", "all", "Enables Privatechat Join");
	bot::command("", "$MODULE_NAME/rem.php", "remuser", "all", "Enables Privatechat Join");
	
	bot::addsetting("guest_man_join", "Mode of manual guestchannel join", "edit", "1", "Only for members of guestlist;Everyone", "1;0");
	bot::addsetting("guest_color_channel", "Color for Guestchannelrelay(ChannelName)", "edit", "<font color=#C3C3C3>", "color");
	bot::addsetting("guest_color_username", "Color for Guestchannelrelay(UserName)", "edit", "<font color=#C3C3C3>", "color");
	bot::addsetting("guest_color_guild", "Color for Guestchannelrelay(Text in Guild)", "edit", "<font color=#C3C3C3>", "color");
	bot::addsetting("guest_color_guest", "Color for Guestchannelrelay(Text in Guestchannel)", "edit", "<font color=#C3C3C3>", "color");
	bot::addsetting("guest_relay", "Relay of the Guestchannel", "edit", "1", "ON;OFF", "1;0");
	bot::addsetting("guest_relay_commands", "Relay commands and results from/to guestchannel", "edit", "0", "ON;OFF", "1;0");
	
	//Autoreinvite Players after a botrestart or crash
	bot::event("setup", "$MODULE_NAME/autoreinvite.php", "none", "Reinvites the players that were in the privgrp before restart/crash");
	
	bot::event("guild", "$MODULE_NAME/guest_channel_relay.php", "none", "Guest channel relay from guild channel");
	bot::event("priv", "$MODULE_NAME/guest_channel_relay.php", "none", "Guest channel relay from priv channel");
	bot::event("logOn", "$MODULE_NAME/logon_autoinvite.php", "none", "Auto-invite members on logon");
	
	//Show Char infos on privjoin
	bot::event("joinPriv", "$MODULE_NAME/notify.php", "none", "Show Infos about a Char when he joins the channel");
	bot::event("leavePriv", "$MODULE_NAME/notify.php", "none", "Show a msg when someone leaves the channel");
	
	//Verifies the Guestchannellist every 1hour
	bot::event("1hour", "$MODULE_NAME/guest_channel_check.php", "guest", "Guest channel check");

    bot::help("GuestChannel", "$MODULE_NAME/guestchannel.txt", "guild", "Guestchannel", "Basic Guild Commands");
	bot::help("join_leave", "$MODULE_NAME/joinleave.txt", "all", "Joining and leaving the bot", "Raidbot");
	bot::help("kickall", "$MODULE_NAME/kickall.txt", "raidleader", "Kick all players from the Bot", "Raidbot");
	bot::help("lock", "$MODULE_NAME/lock.txt", "raidleader", "Lock the privategroup", "Raidbot");
?>