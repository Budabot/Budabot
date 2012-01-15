<?php

if ($setting->get("relaybot") != "Off" && $type == 'guild') {
	$msg = "grc [<myguild>] $message<end>";
    send_message_to_relay($msg);
}

?>
