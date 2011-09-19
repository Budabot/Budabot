<?php

//Send Admin(s) a tell that the bot is online
forEach ($chatBot->admins as $name => $info) {
	if ($name != "" && $info["level"] == 4 && $info["online"] == "online") {
		$chatBot->send("<myname> is <green>online<end>. For updates or help use the Budabot Forums <highlight>http://budabot.com<end>", $name);
	}
}

//Send a message to guild channel
$chatBot->send("Logon Complete :: All systems ready to use.", "guild", true);
$chatBot->send("Logon Complete :: All systems ready to use.", "priv", true);

?>