<?php

if ($chatBot->is_ready() && isset($chatBot->guildmembers[$sender])) {
	$msg = getTeamspeak3Status();	
	$chatBot->send($msg, $sender);
}

?>