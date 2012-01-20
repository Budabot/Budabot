<?php

$admin = Registry::getInstance('admin');
$buddyList = Registry::getInstance('buddyList');

//Send Admin(s) a tell that the bot is online
forEach ($admin->admins as $name => $info) {
	if ($info["level"] == 4 && $buddyList->is_online($name) == 1) {
		$chatBot->sendTell("<myname> is <green>online<end>. For updates or help use the Budabot Forums <highlight>http://budabot.com<end>", $name);
	}
}

//Send a message to guild channel
$chatBot->sendGuild("Logon Complete :: All systems ready to use.", true);
$chatBot->sendPrivate("Logon Complete :: All systems ready to use.", true);

?>