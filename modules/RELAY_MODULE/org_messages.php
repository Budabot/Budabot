<?php

if ($chatBot->settings["relaybot"] != "Off" && $type == 'guild') {
	$msg = "grc <grey>[<myguild>] $message<end>";
    send_message_to_relay($msg);
}

?>
