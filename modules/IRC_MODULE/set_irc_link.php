<?php

global $ircSocket;
if (Setting::get('irc_status') == '1' && !IRC::isConnectionActive($ircSocket)) {
	$result = IRC::connect($ircSocket, Setting::get('irc_nickname'), Setting::get('irc_server'), Setting::get('irc_port'), Setting::get('irc_password'), Setting::get('irc_channel'));
	if ($result == true) {
		Setting::save("irc_status", "1");
	}
}

?>
