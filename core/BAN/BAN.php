<?php 
	$MODULE_NAME = "BAN";

	//Commands
	Command::activate("msg", "$MODULE_NAME/ban_player.php", "ban", "mod");
	Command::activate("msg", "$MODULE_NAME/unban.php", "unban", "mod");
	Command::activate("msg", "$MODULE_NAME/banlist.php", "banlist");
	Command::activate("priv", "$MODULE_NAME/ban_player.php", "ban", "mod");
	Command::activate("priv", "$MODULE_NAME/unban.php", "unban", "mod");
	Command::activate("priv", "$MODULE_NAME/banlist.php", "banlist");
	Command::activate("guild", "$MODULE_NAME/ban_player.php", "ban", "mod");
	Command::activate("guild", "$MODULE_NAME/unban.php", "unban", "mod");
	Command::activate("guild", "$MODULE_NAME/banlist.php", "banlist");

	//Events
	Event::activate("1hour", "$MODULE_NAME/check_tempban.php");

	//Setup
	Event::activate("setup", "$MODULE_NAME/upload_banlist.php");
	
	//Help Files
	bot::help($MODULE_NAME, "ban", "ban.txt", "mod", "Ban a person from the bot");
?>