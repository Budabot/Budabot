<?php

if (preg_match("/^ts$/i", $message)) {
	$msg = getTeamspeak3Status();	
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>