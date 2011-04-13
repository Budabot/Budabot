<?php

if (($sender == ucfirst(strtolower($chatBot->settings['relaybot'])) || $channel == ucfirst(strtolower($chatBot->settings['relaybot']))) && preg_match("/^grc (.+)$/s", $message, $arr)) {
	$msg = $arr[1];
    $chatBot->send(Setting::get('relay_color_guild') . $msg, "guild", true);

	if ($chatBot->settings["guest_relay"] == 1) {
		$chatBot->send(Setting::get('relay_color_priv') . $msg, "priv", true);
	}
} else {
	$syntax_error = true;
}

?>