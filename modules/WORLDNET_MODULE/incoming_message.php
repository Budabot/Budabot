<?php

if (ucfirst(strtolower(Setting::get('worldnet_bot'))) == $sender) {
	$message = preg_replace("/<font(.+?)>/s", "", $message);
	$message = preg_replace("/<\/font>/s", "", $message);
	if (!preg_match("/\\[([^ ]+)\\] (.*) \\[([a-z0-9-]+)\\]$/i", $message, $arr)) {
		return;
	}
	$channel = $arr[1];
	$messageText = $arr[2];
	$name = $arr[3];
	
	$channelSetting = 'worldnet_' . $channel . '_status';
	if (Setting::get($channelSetting) === false) {
		Setting::add('WORLDNET_MODULE', $channelSetting, "Channel $channel status", "edit", "options", "1", "true;false", "1;0");
	}

	if (Ban::is_banned($name)) {
		return;
	}

	$channelColor = Setting::get('worldnet_channel_color');
	$messageColor = Setting::get('worldnet_message_color');
	$senderColor = Setting::get('worldnet_sender_color');
	$msg = "$sender: [{$channelColor}$channel<end>] {$messageColor}{$messageText}<end> [{$senderColor}{$name}<end>]";

	if (Setting::get($channelSetting) == 1) {
		// only send to guild or priv if the channel is enabled on the bot,
		// but don't restrict tell subscriptions
		if (Setting::get('broadcast_to_guild') == 1) {
			$chatBot->send($msg, 'guild', true);
		}
		if (Setting::get('broadcast_to_privchan') == 1) {
			$chatBot->send($msg, 'priv', true);
		}
	}

	if (Setting::get('worldnet_allow_tell_subscriptions') == 1) {
		forEach ($chatBot->guildmembers as $name => $rank) {
			if (Buddylist::is_online($name) == 1) {
				$chatBot->send($msg, $name, false, AOC_PRIORITY_LOW);
			}
		}
	}
}

?>