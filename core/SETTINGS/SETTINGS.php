<?php
	$MODULE_NAME = "SETTINGS";

	//Commands
	bot::regcommand("msg", "$MODULE_NAME/bot_settings.php", "settings", "mod");
	bot::regcommand("priv", "$MODULE_NAME/bot_settings.php", "settings", "mod");
	bot::regcommand("guild", "$MODULE_NAME/bot_settings.php", "settings", "mod");

	//Setup
	bot::regevent("setup", "$MODULE_NAME/upload_settings.php");
	
	bot::addsetting($MODULE_NAME, 'default_guild_color', "", 'edit', "<font color='#84FFFF'>", 'color');
	bot::addsetting($MODULE_NAME, 'default_priv_color', "", 'edit', "<font color='#84FFFF'>", 'color');
	bot::addsetting($MODULE_NAME, 'default_window_color', "", 'edit', "<font color='#84FFFF'>", 'color');
	bot::addsetting($MODULE_NAME, 'default_tell_color', "", 'edit', "<font color='#DDDDDD'>", 'color');
	bot::addsetting($MODULE_NAME, 'default_highlight_color', "", 'edit', "<font color='#9CC6E7'>", 'color');
	bot::addsetting($MODULE_NAME, 'default_header_color', "", 'edit', "<font color='#FFFF00'>", 'color');

	bot::addsetting($MODULE_NAME, 'symbol', '', 'edit', '!', '!;#;*;@;$;+;-');
	bot::addsetting($MODULE_NAME, 'guild_admin_level', '', 'edit', 3, 'President;General;Squad Commander;Unit Commander;Unit Leader;Unit Member;Applicant', '0;1;2;3;4;5;6', "$MODULE_NAME/guild_admin_level.txt");
	bot::addsetting($MODULE_NAME, 'spam_protection', '', 'edit', 0, 'ON;OFF', '1;0', "$MODULE_NAME/spam_help.txt");

	//Help Files
	bot::help($MODULE_NAME, "settings", "settings.txt", "mod", "Change Settings of the Bot");
?>