<?php

if (preg_match("/^ts$/i", $message)) {
	$msg = getTeamspeak3Status();	
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>