<?php

function send_message_to_relay($message) {
	$chatBot = Registry::getInstance('chatBot');
	
	$relayBot = Setting::get('relaybot');
	$message = str_ireplace("<myguild>", getGuildAbbreviation(), $message);

	// since we are using the aochat methods, we have to call format_message manually to handle colors and bot name replacement	
	$message = Text::format_message($message);

	// we use the aochat methods so the bot doesn't prepend default colors
	if (Setting::get('relaytype') == 2) {
		$chatBot->send_privgroup($relayBot, $message);
	} else if (Setting::get('relaytype') == 1) {
		$chatBot->send_tell($relayBot, $message);
		
		// manual logging is only needed for tell relay
		Logger::log_chat("Out. Msg.", $relayBot, $message);
	}
}

function getGuildAbbreviation() {
	$chatBot = Registry::getInstance('chatBot');

	if (Setting::get('relay_guild_abbreviation') != 'none') {
		return Setting::get('relay_guild_abbreviation');
	} else {
		return $chatBot->vars["my_guild"];
	}
}

?>