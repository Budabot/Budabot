<?php

if (ucfirst(strtolower(Setting::get('worldnet_bot'))) == $sender) {
	preg_match("/\\[([^ ]+)\\] (.*) \\[([a-z0-9-]+)\\]$/i", $message, $arr);
	$channel = $arr[1];
	$messageText = $arr[2];
	$name = $arr[3];

	if (Setting::get('worldnet_' . strtolower($channel) . '_status') != 1) {
		return;
	}

	if (Ban::is_banned($name)) {
		return;
	}
	$channelColor = Setting::get('worldnet_channel_color');
	$messageColor = Setting::get('worldnet_message_color');
	$senderColor = Setting::get('worldnet_sender_color');
	$msg = "WorldNet: [{$channelColor}$channel<end>] {$messageColor}{$messageText}<end> [{$senderColor}{$name}<end>]";

	if (Setting::get('broadcast_to_guild') == 1) {
		$chatBot->send($msg, 'guild', true);
	}
	if (Setting::get('broadcast_to_privchan') == 1) {
		$chatBot->send($msg, 'priv', true);
	}
	if (Setting::get('broadcast_to_guild_members') == 1) {
		forEach ($chatBot->guildmembers as $name => $rank) {
			if (Buddylist::is_online($name) == 1) {
				$chatBot->send($msg, $name, false, AOC_PRIORITY_LOW);
			}
		}
	}
}

?>