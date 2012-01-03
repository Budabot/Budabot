<?php

global $ircSocket;
if ($setting->get('irc_status') == '1' && !IRC::isConnectionActive($ircSocket)) {
	IRC::connect($ircSocket, $setting->get('irc_nickname'), $setting->get('irc_server'), $setting->get('irc_port'), $setting->get('irc_password'), $setting->get('irc_channel'));
}

?>
