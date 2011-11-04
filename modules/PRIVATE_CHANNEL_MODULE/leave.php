<?php

if (preg_match("/^leave$/i", $message)) {
	$chatBot->privategroup_kick($sender);
} else {
	$syntax_error = true;
}

?>
