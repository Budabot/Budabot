<?php
	$MODULE_NAME = "SETTINGS";

	//Commands
	Command::activate("msg", "$MODULE_NAME/bot_settings.php", "settings", "mod");
	Command::activate("priv", "$MODULE_NAME/bot_settings.php", "settings", "mod");
	Command::activate("guild", "$MODULE_NAME/bot_settings.php", "settings", "mod");

	//Setup
	Event::activate("setup", "$MODULE_NAME/upload_settings.php");

	Setting::add($MODULE_NAME, 'default_guild_color', "default guild color", 'edit', "color", "<font color='#84FFFF'>");
	Setting::add($MODULE_NAME, 'default_priv_color', "default private channel color", 'edit', "color", "<font color='#84FFFF'>");
	Setting::add($MODULE_NAME, 'default_window_color', "default window color", 'edit', "color", "<font color='#84FFFF'>");
	Setting::add($MODULE_NAME, 'default_tell_color', "default tell color", 'edit', "color", "<font color='#DDDDDD'>");
	Setting::add($MODULE_NAME, 'default_highlight_color', "default highlight color", 'edit', "color", "<font color='#9CC6E7'>");
	Setting::add($MODULE_NAME, 'default_header_color', "default header color", 'edit', "color", "<font color='#FFFF00'>");

	Setting::add($MODULE_NAME, 'default_clan_color', "default clan color", 'edit', "color", "<font color='#F79410'>");
	Setting::add($MODULE_NAME, 'default_omni_color', "default omni color", 'edit', "color", "<font color='#00FFFF'>");
	Setting::add($MODULE_NAME, 'default_neut_color', "default neut color", 'edit', "color", "<font color='#EEEEEE'>");
	Setting::add($MODULE_NAME, 'default_unknown_color', "default unknown color", 'edit', "color", "<font color='#FF0000'>");

	Setting::add($MODULE_NAME, 'symbol', 'command prefix symbol', 'edit', "text", '!', '!;#;*;@;$;+;-');
	Setting::add($MODULE_NAME, 'guild_admin_level', 'guild admin level', 'edit', "number", 1, 'President;General;Squad Commander;Unit Commander;Unit Leader;Unit Member;Applicant', '0;1;2;3;4;5;6', 'mod');
	Setting::add($MODULE_NAME, 'spam_protection', 'Use spam protection', 'edit', "options", 0, "true;false", "1;0", 'mod');

	//Help Files
	Help::register($MODULE_NAME, "settings", "settings.txt", "mod", "Change Settings of the Bot");
	Help::register($MODULE_NAME, "guild_admin_level", "guild_admin_level.txt", "mod", "Change what guild rank and high receives the guild admin level privilege");
	Help::register($MODULE_NAME, "spam_protection", "spam_protection.txt", "mod", "Enable or disable the spam protection");
	Help::register($MODULE_NAME, "max_blob_size", "max_blob_size.txt", "mod", "Set the maximum blob size");
	Help::register($MODULE_NAME, "default_module_status", "default_module_status.txt", "mod", "Set new modules to be enabled or disabled by default");
?>