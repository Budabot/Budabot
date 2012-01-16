<?php
   
if (preg_match("/^updateorg$/i", $message)) {
 	$force_update = true;
 	$sendto->reply("Starting Roster update");
	include './modules/GUILD_MODULE/roster_guild.php';
	$sendto->reply("Finished Roster update");
} else {
	$syntax_error = true;
}

?>