<?php 
$MODULE_NAME = "BASIC_CHAT_MODULE";
$PLUGIN_VERSION = 0.1;

	//Setup
	bot::event("setup", "$MODULE_NAME/setup.php");

	//Invite/Leave/lock commands
	bot::command("priv", "$MODULE_NAME/leave.php", "leave", "all", "Enables Privatechat Kick");
	bot::command("priv", "$MODULE_NAME/leave.php", "kick", "all", "Enables Privatechat Kick");
	bot::command("msg", "$MODULE_NAME/leave.php", "leave", "all", "Enables Privatechat Kick");
	bot::command("msg", "$MODULE_NAME/leave.php", "kick", "all", "Enables Privatechat Kick");
	bot::command("msg", "$MODULE_NAME/join.php", "join", "all", "Enables Privatechat Join");
	bot::command("msg", "$MODULE_NAME/join.php", "invite", "all", "Enables Privatechat Join");
	bot::command("guild", "$MODULE_NAME/join.php", "join", "all", "Enables Privatechat Join");
	bot::command("guild", "$MODULE_NAME/join.php", "invite", "all", "Enables Privatechat Join");
	bot::command("priv", "$MODULE_NAME/kickall.php", "kickall", "mod", "Kicks all from the privgroup");
	bot::command("msg", "$MODULE_NAME/kickall.php", "kickall", "mod", "Kicks all from the privgroup");
	bot::command("priv", "$MODULE_NAME/lock.php", "lock", "rl", "Locks the privgroup");
	bot::command("priv", "$MODULE_NAME/lock.php", "unlock", "rl", "Unlocks the privgroup");	
	bot::command("msg", "$MODULE_NAME/lock.php", "lock", "rl", "Locks the privgroup");
	bot::command("msg", "$MODULE_NAME/lock.php", "unlock", "rl", "Unlocks the privgroup");	
	bot::regGroup("priv_invite_kick", $MODULE_NAME, "Enable Privatechat invite/kick/locking", "kick", "leave", "join", "kickall", "lock", "unlock", "invite");
	bot::addsetting("topic_guild_join", "Show Topic in guild on join", "edit", "0", "ON;OFF", "1;0", "mod", "$MODULE_NAME/topic_show_guild.txt");
	bot::addsetting("priv_status", "no", "hide", "open");
	bot::addsetting("priv_status_reason", "no", "hide", "not set");	

	//Handles joined and left chat
	bot::event("joinPriv", "$MODULE_NAME/joined_chat.php", "join");
	bot::event("leavePriv", "$MODULE_NAME/left_chat.php", "join");

	//Check macros
	bot::command("priv", "$MODULE_NAME/check.php", "check", "rl", "Checks who of the raidgroup is in the area");	
	
	//Topic set/show
	bot::event("joinPriv", "$MODULE_NAME/Topic.php", "topic", "Show Topic when someone joins PrivChat");
	bot::event("logOn", "$MODULE_NAME/Topic_logon.php", "none", "Show Topic on logon of members");
	bot::command("priv", "$MODULE_NAME/Topic.php", "topic", "all", "Show Topic");
	bot::command("guild", "$MODULE_NAME/Topic.php", "topic", "all", "Show Topic");
	bot::command("msg", "$MODULE_NAME/Topic.php", "topic", "all", "Show Topic");
	bot::subcommand("msg", "$MODULE_NAME/Topic.php", "topic (.+)", "leader", "topic", "Change Topic");
	bot::addsetting("topic", "Topic for Priv Channel", "noedit", "No Topic set.");	
	bot::addsetting("topic_setby", "no", "hide", "none");
	bot::addsetting("topic_time", "no", "hide", time());

    // Afk Check
	bot::event("priv", "$MODULE_NAME/AFK_Check.php", "afk");
	bot::command("priv", "$MODULE_NAME/AFK.php", "afk", "all", "Sets a member afk");

	//Show Char infos on privjoin
	bot::event("joinPriv", "$MODULE_NAME/Notify.php", "none", "Show Infos about a Char when he joins the channel");
	bot::event("leavePriv", "$MODULE_NAME/Notify.php", "none", "Show a msg when someone leaves the channel");
	
	//Leader
	bot::command("priv", "$MODULE_NAME/Leader.php", "leader", "all", "Sets the Leader of the raid");
	bot::subcommand("priv", "$MODULE_NAME/Leader.php", "leader (.+)", "raidleader", "leader", "Set a specific Leader");
	bot::event("priv", "$MODULE_NAME/LeaderEcho.php", "leader");
	
	//Assist
	bot::command("priv", "$MODULE_NAME/Assist.php", "assist", "all", "Creates/showes an Assist macro");
	bot::subcommand("priv", "$MODULE_NAME/Assist.php", "assist (.+)", "leader", "assist", "Set a new assist");
	bot::command("priv", "$MODULE_NAME/Heal_Assist.php", "heal", "all", "Creates/showes an Doc Assist macro");
	bot::subcommand("priv", "$MODULE_NAME/Heal_Assist.php", "heal (.+)", "leader", "heal", "Set a new Doc assist");

	//Tell
	bot::command("priv", "$MODULE_NAME/Tell.php", "tell", "all", "Repeats a Message 3times");
	
	//updateme
	bot::command("msg", "$MODULE_NAME/updateme.php", "updateme", "all", "Updates Charinfos from a player");
		
	//Kick/Invite User
	bot::command("priv", "$MODULE_NAME/user_invite.php", "inviteuser", "all", "Invites a User to the PrivChan");
	bot::command("priv", "$MODULE_NAME/user_kick.php", "kickuser", "all", "Kicks a User from the PrivChan");

	//Autoreinvite Players after a botrestart or crash
	bot::event("connect", "$MODULE_NAME/autoreinvite.php", "none", "Reinvites the players that were in the privgrp before restart/crash");
	
	//Set admin and user news
	bot::command("msg", "$MODULE_NAME/set_news.php", "news", "rl", "Set news that are shown on privjoin");
	bot::command("msg", "$MODULE_NAME/set_news.php", "adminnews", "mod", "Set adminnews that are shown on privjoin");
	bot::addsetting("news", "no", "hide", "Not set.");
	bot::addsetting("adminnews", "no", "hide", "Not set.");	
	
	//Helpfiles
	bot::help("assist", "$MODULE_NAME/assist.txt", "all", "Creating an Assist Macro", "Raidbot");
	bot::help("check", "$MODULE_NAME/check.txt", "all", "See of the ppls are in the area", "Raidbot");
	bot::help("heal_assist", "$MODULE_NAME/healassist.txt", "all", "Creating an Healassist Macro", "Raidbot");
	bot::help("join_leave", "$MODULE_NAME/joinleave.txt", "all", "Joining and leaving the bot", "Raidbot");
	bot::help("kickall", "$MODULE_NAME/kickall.txt", "raidleader", "Kick all players from the Bot", "Raidbot");	
	bot::help("leader", "$MODULE_NAME/leader.txt", "all", "Set a leader of the Raid", "Raidbot");	
	bot::help("lock", "$MODULE_NAME/lock.txt", "raidleader", "Lock the privategroup", "Raidbot");			
	bot::help("tell", "$MODULE_NAME/tell.txt", "leader", "Repeating of a msg 3times", "Raidbot");
	bot::help("updateme", "$MODULE_NAME/updateme.txt", "all", "Update your character infos", "Raidbot");
	bot::help("userinvitekick", "$MODULE_NAME/userinvitekick.txt", "leader", "Invite or kick a player from the channel", "Raidbot");					
?>
