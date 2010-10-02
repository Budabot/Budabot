<?php 
	$MODULE_NAME = "BAN";

	//Commands
	bot::regcommand("msg", "$MODULE_NAME/ban_player.php", "ban","mod");
	bot::regcommand("msg", "$MODULE_NAME/unban.php", "unban","mod");
	bot::regcommand("msg", "$MODULE_NAME/banlist.php", "banlist");
	bot::regcommand("priv", "$MODULE_NAME/ban_player.php", "ban","mod");
	bot::regcommand("priv", "$MODULE_NAME/unban.php", "unban","mod");
	bot::regcommand("priv", "$MODULE_NAME/banlist.php", "banlist");
	bot::regcommand("guild", "$MODULE_NAME/ban_player.php", "ban","mod");
	bot::regcommand("guild", "$MODULE_NAME/unban.php", "unban","mod");
	bot::regcommand("guild", "$MODULE_NAME/banlist.php", "banlist");

	//Events
	bot::regevent("1hour", "$MODULE_NAME/check_tempban.php");

	//Setup
	bot::regevent("setup", "$MODULE_NAME/upload_banlist.php");
	
	//Help Files
	bot::help("ban", "$MODULE_NAME/ban.txt", "mod", "Ban a person from the bot");
?>