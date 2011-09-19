<?php 
	require_once 'Ban.class.php';

	$MODULE_NAME = "BAN";
	
	Event::activate("setup", "$MODULE_NAME/setup.php");
	Event::activate("1min", "$MODULE_NAME/check_tempban.php");

	Command::activate("msg", "$MODULE_NAME/ban_player.php", "ban", "mod");
	Command::activate("msg", "$MODULE_NAME/unban.php", "unban", "mod");
	Command::activate("msg", "$MODULE_NAME/banlist.php", "banlist");
	Command::activate("priv", "$MODULE_NAME/ban_player.php", "ban", "mod");
	Command::activate("priv", "$MODULE_NAME/unban.php", "unban", "mod");
	Command::activate("priv", "$MODULE_NAME/banlist.php", "banlist");
	Command::activate("guild", "$MODULE_NAME/ban_player.php", "ban", "mod");
	Command::activate("guild", "$MODULE_NAME/unban.php", "unban", "mod");
	Command::activate("guild", "$MODULE_NAME/banlist.php", "banlist");
	
	Setting::add($MODULE_NAME, "notify_banned_player", "Notify player when banned from bot", "edit", "options", "1", "true;false", "1;0");
	
	Help::register($MODULE_NAME, "ban", "ban.txt", "mod", "Ban a person from the bot");
?>