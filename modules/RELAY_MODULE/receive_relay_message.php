<?php

if (($sender == ucfirst(strtolower($setting->get('relaybot'))) || $channel == ucfirst(strtolower($setting->get('relaybot')))) && preg_match("/^grc (.+)$/s", $message, $arr)) {
	$msg = $arr[1];
    $chatBot->send($setting->get('relay_color_guild') . $msg, "guild", true);

	if ($setting->get("guest_relay") == 1) {
		$chatBot->send($setting->get('relay_color_priv') . $msg, "priv", true);
	}
} else {
	$syntax_error = true;
}

?>