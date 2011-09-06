<?php

if (($sender == ucfirst(strtolower(Setting::get('relaybot'))) || $channel == ucfirst(strtolower(Setting::get('relaybot')))) && preg_match("/^grc (.+)$/s", $message, $arr)) {
	$msg = $arr[1];
    $chatBot->send(Setting::get('relay_color_guild') . $msg, "guild", true);

	if (Setting::get("guest_relay") == 1) {
		$chatBot->send(Setting::get('relay_color_priv') . $msg, "priv", true);
	}
} else {
	$syntax_error = true;
}

?>