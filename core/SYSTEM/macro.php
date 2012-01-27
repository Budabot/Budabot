<?php

if (preg_match("/^macro (.+)$/i", $message, $arr)) {
	$commandManager = Registry::getInstance('commandManager');
	$commands = explode("|", $arr[1]);
	forEach ($commands as $commandString) {
		$commandManager->process($type, $commandString, $sender, $sendto);
	}
} else {
	$syntax_error = true;
}

?>