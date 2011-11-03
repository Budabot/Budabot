<?php
/*
** Author: Mindrila (RK1)
** Credits: Legendadv (RK2)
** BUDABOT IRC NETWORK MODULE
** Version = 0.1
** Developed for: Budabot(http://budabot.com)
**
*/

global $bbinSocket;
if (Setting::get('bbin_status') == '1' && !IRC::isConnectionActive($bbinSocket)) {
	$result = IRC::connect($bbinSocket, Setting::get('bbin_nickname'), Setting::get('bbin_server'), Setting::get('bbin_port'), Setting::get('bbin_password'), Setting::get('bbin_channel'));
}

?>
