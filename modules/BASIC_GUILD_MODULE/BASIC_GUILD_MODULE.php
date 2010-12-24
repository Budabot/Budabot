<?php
	$MODULE_NAME = "BASIC_GUILD_MODULE";
	
	// Afk Check
	bot::event($MODULE_NAME, "guild", "afk_check.php", "none", "Afk check");
	bot::command("guild", "$MODULE_NAME/afk.php", "afk", "all", "Sets a member afk");
	bot::command("guild", "$MODULE_NAME/kiting.php", "kiting", "all", "Sets a member afk kiting");
	
	//Tell and Tellall
	bot::command("guild msg", "$MODULE_NAME/tell.php", "tell", "leader", "Repeats an message 3 times in Orgchat");
	bot::command("guild msg", "$MODULE_NAME/tell.php", "tellall", "leader", "Sends a tell to all online guildmembers");
	
	//Helpfile
	bot::help($MODULE_NAME, "afk_kiting", "afk_kiting.txt", "guild", "Set yourself AFK/Kiting");
	bot::help($MODULE_NAME, "tell", "tell.txt", "guild", "How to use tell and tellall");
?>