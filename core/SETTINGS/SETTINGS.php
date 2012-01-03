<?php
	$command->activate("msg", "$MODULE_NAME/bot_settings.php", "settings", "mod");
	$command->activate("priv", "$MODULE_NAME/bot_settings.php", "settings", "mod");
	$command->activate("guild", "$MODULE_NAME/bot_settings.php", "settings", "mod");

	$event->activate("setup", "Setting.upload");

	$setting->add($MODULE_NAME, 'default_guild_color', "default guild color", 'edit', "color", "<font color='#84FFFF'>");
	$setting->add($MODULE_NAME, 'default_priv_color', "default private channel color", 'edit', "color", "<font color='#84FFFF'>");
	$setting->add($MODULE_NAME, 'default_window_color', "default window color", 'edit', "color", "<font color='#84FFFF'>");
	$setting->add($MODULE_NAME, 'default_tell_color', "default tell color", 'edit', "color", "<font color='#DDDDDD'>");
	$setting->add($MODULE_NAME, 'default_highlight_color', "default highlight color", 'edit', "color", "<font color='#9CC6E7'>");
	$setting->add($MODULE_NAME, 'default_header_color', "default header color", 'edit', "color", "<font color='#FFFF00'>");

	$setting->add($MODULE_NAME, 'default_clan_color', "default clan color", 'edit', "color", "<font color='#F79410'>");
	$setting->add($MODULE_NAME, 'default_omni_color', "default omni color", 'edit', "color", "<font color='#00FFFF'>");
	$setting->add($MODULE_NAME, 'default_neut_color', "default neut color", 'edit', "color", "<font color='#EEEEEE'>");
	$setting->add($MODULE_NAME, 'default_unknown_color', "default unknown color", 'edit', "color", "<font color='#FF0000'>");

	$help->register($MODULE_NAME, "settings", "settings.txt", "mod", "Change Settings of the Bot");
?>