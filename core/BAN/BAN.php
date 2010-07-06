<?php 
$MODULE_NAME = "BAN";

	//Commands
	bot::regcommand("msg", "$MODULE_NAME/ban_player.php", "ban", MODERATOR);
	bot::regcommand("msg", "$MODULE_NAME/unban.php", "unban", MODERATOR);
	bot::regcommand("msg", "$MODULE_NAME/banlist.php", "banlist", MODERATOR);
	bot::regcommand("priv", "$MODULE_NAME/ban_player.php", "ban", MODERATOR);
	bot::regcommand("priv", "$MODULE_NAME/unban.php", "unban", MODERATOR);
	bot::regcommand("priv", "$MODULE_NAME/banlist.php", "banlist", MODERATOR);
	bot::regcommand("guild", "$MODULE_NAME/ban_player.php", "ban", MODERATOR);
	bot::regcommand("guild", "$MODULE_NAME/unban.php", "unban", MODERATOR);
	bot::regcommand("guild", "$MODULE_NAME/banlist.php", "banlist", MODERATOR);

	//Events
	bot::regevent("1hour", "$MODULE_NAME/check_tempban.php");

	//Setup
	bot::regevent("setup", "$MODULE_NAME/upload_banlist.php");
	
	//Help Files
	bot::help("banhelp", "$MODULE_NAME/banhelp.txt", MODERATOR, "Ban a person from the bot.");
?>