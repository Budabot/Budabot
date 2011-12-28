<?php

class Worldnet {
	/**
	 * @Event("logOn")
	 * @Description("Requests invite from worldnet bot")
	 */
	function logon($chatBot, $type, $sender) {
		if (strtolower(Setting::get('worldnet_bot')) == strtolower($sender)) {
			$msg = "!join";
			Logger::log_chat("Out. Msg.", $sender, $msg);
			$chatBot->send_tell($sender, $msg);
		}
	}
	
	/**
	 * @Event("connect")
	 * @Description("Adds worldnet bot to buddylist")
	 */
	function connect($chatbot, $type) {
		Buddylist::add(Setting::get('worldnet_bot'), 'worldnet');
	}
	
	/**
	 * @Event("extJoinPrivRequest")
	 * @Description("Accepts invites from worldnet bot")
	 */
	function acceptInvite($chatBot, $type, $sender) {
		if (strtolower(Setting::get('worldnet_bot')) == strtolower($sender)) {
			$chatBot->privategroup_join($sender);
		}
	}
	
	/**
	 * @Event("extPriv")
	 * @Description("Relays incoming messages to the guild/private channel")
	 */
	function incomingMessage($chatBot, $type, $sender, $channel, $message) {
		if (strtolower(Setting::get('worldnet_bot')) != strtolower($sender)) {
			return;
		}

		$message = preg_replace("/<font(.+?)>/s", "", $message);
		$message = preg_replace("/<\/font>/s", "", $message);

		if (!preg_match("/\\[([^ ]+)\\] (.*) \\[([a-z0-9-]+)\\]$/i", $message, $arr)) {
			return;
		}

		$worldnetChannel = $arr[1];
		$messageText = $arr[2];
		$name = $arr[3];
		
		$channelSetting = strtolower($sender . '_' . $worldnetChannel . '_channel');
		if (Setting::get($channelSetting) === false) {
			Setting::add('WORLDNET_MODULE', $channelSetting, "Channel $worldnetChannel status", "edit", "options", "1", "true;false", "1;0");
		}

		if (Ban::is_banned($name)) {
			return;
		}

		$channelColor = Setting::get('worldnet_channel_color');
		$messageColor = Setting::get('worldnet_message_color');
		$senderColor = Setting::get('worldnet_sender_color');
		$msg = "$sender: [{$channelColor}$worldnetChannel<end>] {$messageColor}{$messageText}<end> [{$senderColor}{$name}<end>]";

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
	}
}

?>