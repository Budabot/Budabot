<?php

function send_message_to_relay($message) {
	global $chatBot;
	
	// we use the aochat methods so the bot doesn't prepend default colors
	
	// since we are using the aochat methods, we have to call format_message manually to handle colors and bot name replacement
	$message = Text::format_message($message);
	$relayBot = Setting::get('relaybot');
	$guild = $chatBot->vars["my_guild"];
	if (Setting::get('relay_guild_abbreviation') != 'none') {
		$guild = Setting::get('relay_guild_abbreviation');
	}
	$message = str_ireplace("<myguild>", $guild, $message);

	if (Setting::get('relaytype') == 2) {
		$chatBot->send_privgroup($relayBot, $message);
	} else if (Setting::get('relaytype') == 1) {
		$chatBot->send_tell($relayBot, $message);
		
		// manual logging is only needed for tell relay
		Logger::log_chat("Out. Msg.", $relayBot, $message);
	}
}

?>