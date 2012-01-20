<?php

if (($sender == ucfirst(strtolower($setting->get('relaybot'))) || $channel == ucfirst(strtolower($setting->get('relaybot')))) && preg_match("/^grc (.+)$/s", $message, $arr)) {
	$msg = $arr[1];
    $chatBot->sendGuild($setting->get('relay_color_guild') . $msg, true);

	if ($setting->get("guest_relay") == 1) {
		$chatBot->sendPrivate($setting->get('relay_color_priv') . $msg, true);
	}
} else {
	$syntax_error = true;
}

?>