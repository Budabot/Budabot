<?php
   
if (preg_match("/^updateorg$/i", $message)) {
 	$force_update = true;
 	$chatBot->send("Starting Roster update", $sendto);
	include './modules/GUILD_MODULE/roster_guild.php';
	$chatBot->send("Finished Roster update", $sendto);
} else {
	$syntax_error = true;
}

?>