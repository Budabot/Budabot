<?php

if (preg_match("/^macro (.+)$/i", $message, $arr)) {
	$commands = explode("|", $arr[1]);
	forEach ($commands as $command) {
		$chatBot->process_command($type, $command, $sender, $sendto);
	}
} else {
	$syntax_error = true;
}

?>