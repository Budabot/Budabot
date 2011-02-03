<?php
	$MODULE_NAME = "SETTINGS";

	//Commands
	Command::activate("msg", "$MODULE_NAME/bot_settings.php", "settings", "mod");
	Command::activate("priv", "$MODULE_NAME/bot_settings.php", "settings", "mod");
	Command::activate("guild", "$MODULE_NAME/bot_settings.php", "settings", "mod");

	//Setup
	Event::activate("setup", "$MODULE_NAME/upload_settings.php");

	bot::addsetting($MODULE_NAME, 'default_guild_color', "default guild color", 'edit', "<font color='#84FFFF'>", 'color');
	bot::addsetting($MODULE_NAME, 'default_priv_color', "default private channel color", 'edit', "<font color='#84FFFF'>", 'color');
	bot::addsetting($MODULE_NAME, 'default_window_color', "default window color", 'edit', "<font color='#84FFFF'>", 'color');
	bot::addsetting($MODULE_NAME, 'default_tell_color', "default tell color", 'edit', "<font color='#DDDDDD'>", 'color');
	bot::addsetting($MODULE_NAME, 'default_highlight_color', "default highlight color", 'edit', "<font color='#9CC6E7'>", 'color');
	bot::addsetting($MODULE_NAME, 'default_header_color', "default header color", 'edit', "<font color='#FFFF00'>", 'color');

	bot::addsetting($MODULE_NAME, 'default_clan_color', "default clan color", 'edit', "<font color='#F79410'>", 'color');
	bot::addsetting($MODULE_NAME, 'default_omni_color', "default omni color", 'edit', "<font color='#00FFFF'>", 'color');
	bot::addsetting($MODULE_NAME, 'default_neut_color', "default neut color", 'edit', "<font color='#EEEEEE'>", 'color');
	bot::addsetting($MODULE_NAME, 'default_unknown_color', "default unknown color", 'edit', "<font color='#FF0000'>", 'color');

	bot::addsetting($MODULE_NAME, 'symbol', 'command prefix symbol', 'edit', '!', '!;#;*;@;$;+;-');
	bot::addsetting($MODULE_NAME, 'guild_admin_level', 'guild admin level', 'edit', 3, 'President;General;Squad Commander;Unit Commander;Unit Leader;Unit Member;Applicant', '0;1;2;3;4;5;6', "$MODULE_NAME/guild_admin_level.txt");
	bot::addsetting($MODULE_NAME, 'spam_protection', 'enable/disable spam protection', 'edit', 0, 'ON;OFF', '1;0', "$MODULE_NAME/spam_help.txt");

	//Help Files
	bot::help($MODULE_NAME, "settings", "settings.txt", "mod", "Change Settings of the Bot");
?>