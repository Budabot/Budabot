<?php

if (preg_match("/^macro (.+)$/i", $message, $arr)) {
	$command = Registry::getInstance('command');
	$commands = explode("|", $arr[1]);
	forEach ($commands as $commandString) {
		$command->process($type, $commandString, $sender, $sendto);
	}
} else {
	$syntax_error = true;
}

?>