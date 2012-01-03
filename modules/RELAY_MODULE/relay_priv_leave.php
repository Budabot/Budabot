<?php

if ($setting->get('relaybot') != 'Off' && $type == "leavepriv") {
	$msg = "<highlight>{$sender}<end> has left the private channel.";
	send_message_to_relay("grc <grey>[<myguild>] ".$msg);
}

?>