<?php

if (Setting::get('irc_status') == '1' && !IRC::isConnectionActive()) {
	IRC::connect();
}

?>
