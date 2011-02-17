<?php

if ($chatBot->settings['relaybot'] != 'Off' && $type == "leavePriv") {
	$msg = "<highlight>{$sender}<end> has left the private channel.";
	send_message_to_relay("grc <grey>[".$chatBot->vars['my guild']."] ".$msg);
}

?>