<?php 
	require_once 'Ban.class.php';
	
	Event::activate("setup", "$MODULE_NAME/setup.php");
	Event::activate("1min", "$MODULE_NAME/check_tempban.php");

	$command->register($MODULE_NAME, "", "unban.php", "unban", "mod", "Unban a player from this bot", 'ban');
	$command->register($MODULE_NAME, "", "unban.php", "unbanorg", "mod", "Unban a player from this bot", 'ban');
	$command->register($MODULE_NAME, "", "ban_player.php", "ban", "mod", "Ban a player from this bot", 'ban');
	$command->register($MODULE_NAME, "", "ban_player.php", "banorg", "mod", "Ban an organization from this bot", 'ban');
	$command->register($MODULE_NAME, "", "banlist.php", "banlist", "mod", "Shows who is on the banlist", 'ban');
	
	Setting::add($MODULE_NAME, "notify_banned_player", "Notify player when banned from bot", "edit", "options", "1", "true;false", "1;0");
	
	Help::register($MODULE_NAME, "ban", "ban.txt", "mod", "Ban a person from the bot");
?>