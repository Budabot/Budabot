<?php
	$MODULE_NAME = "BASIC_GUILD_MODULE";
	
	//Setup of the Basic Guild Modules
	bot::event("setup", "$MODULE_NAME/setup.php");
	
	// Afk Check
	bot::event("guild", "$MODULE_NAME/afk_check.php", "none", "Afk check");
	bot::command("guild", "$MODULE_NAME/afk.php", "afk", "all", "Sets a member afk");
	bot::command("guild", "$MODULE_NAME/kiting.php", "kiting", "all", "Sets a member afk kiting");
	
	// Alternative Characters
	bot::command("", "$MODULE_NAME/alts.php", "alts", "all", "Alt Char handling");
	bot::command("", "$MODULE_NAME/altsadmin.php", "altsadmin", "mod", "Alt Char handling (admin)");
	
	//Tell and Tellall
	bot::command("guild msg", "$MODULE_NAME/tell.php", "tell", "rl", "Repeats an message 3 times in Orgchat");
	bot::command("guild msg", "$MODULE_NAME/tell.php", "tellall", "rl", "Sends a tell to all online guildmembers");
	
	//Helpfile
	bot::help("afk_kiting", "$MODULE_NAME/afk_kiting.txt", "guild", "Set yourself AFK/Kiting");
	bot::help("alts", "$MODULE_NAME/alts.txt", "guild", "How to set alts");
	bot::help("altsadmin", "$MODULE_NAME/altsadmin.txt", "guild", "How to set alts (admins)");
	bot::help("tell", "$MODULE_NAME/tell.txt", "guild", "How to use tell and tellall");
?>