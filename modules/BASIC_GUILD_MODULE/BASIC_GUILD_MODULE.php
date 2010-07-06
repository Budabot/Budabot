<?php
	$MODULE_NAME = "BASIC_GUILD_MODULE";
	$PLUGIN_VERSION = 1.0;

	//Setup of the Basic Guild Modules
	$this->event("setup", "$MODULE_NAME/setup.php");
    
	// Logon Handling
	$this->command("", "$MODULE_NAME/logon_msg.php", "logon", ALL, "Sets a Logon Msg");

    // Afk Check
	$this->event("guild", "$MODULE_NAME/afk_check.php", "afk");
	$this->command("guild", "$MODULE_NAME/afk.php", "afk", ALL, "Sets a member afk");
	$this->command("guild", "$MODULE_NAME/kiting.php", "kiting", ALL, "Sets a member afk kiting");

	//Verifies the Onlinelist every 1hour
	$this->event("1hour", "$MODULE_NAME/online_check.php", "online");

    // Alternative Characters
	$this->command("", "$MODULE_NAME/alts.php", "alts", ALL, "Alt Char handling");
	$this->command("", "$MODULE_NAME/alts.php", "altsadmin", MODERATOR, "Alt Char handling (admin)");

    // Show orgmembers
	$this->command("", "$MODULE_NAME/orgmembers.php", "orgmembers", ALL, "Show the Members(sorted by name) of the org");
	$this->command("", "$MODULE_NAME/orgranks.php", "orgranks", ALL, "Show the Members(sorted by rank) of the org");

	//Force an update of the org roster
	$this->command("msg", "$MODULE_NAME/updateorg.php", "updateorg", MODERATOR, "Forcing an update of the org roster");
	
	//Tell and Tellall
	$this->command("guild msg", "$MODULE_NAME/tell.php", "tell", LEADER, "Repeats an message 3 times in Orgchat");
	$this->command("guild msg", "$MODULE_NAME/tell.php", "tellall", LEADER, "Sends a tell to all online guildmembers");
		
	//Helpfile
    $this->help("afk_kiting", "$MODULE_NAME/afk_kiting.txt", GUILDMEMBER, "Set yourself AFK/Kiting");
    $this->help("alts", "$MODULE_NAME/alts.txt", GUILDMEMBER, "How to set alts");
	$this->help("altsadmin", "$MODULE_NAME/altsadmin.txt", GUILDMEMBER, "How to set alts (admins)");
    $this->help("LogOnMsg", "$MODULE_NAME/logonmsg.txt", GUILDMEMBER, "Changing your logon message");
    $this->help("OrgMembers", "$MODULE_NAME/orgmembers_orgranks.txt", GUILDMEMBER, "Show current OrgMembers");
    $this->help("tell_guild", "$MODULE_NAME/tell.txt", GUILDMEMBER, "Repeat a msg 3times/Send a tell to online members");
    $this->help("updateorg", "$MODULE_NAME/updateorg.txt", MODERATOR, "Force an update of orgrooster");
?>