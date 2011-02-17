<?php

if (preg_match("/^reloadconfig$/i", $message)) {
	global $config_file;

	require $config_file;

	// remove variables that shouldn't change without a restart
	unset($vars['name']);
	unset($vars['login']);
	unset($vars['password']);
	unset($vars['dimension']);

	forEach ($vars as $key => $value) {
		$chatBot->vars[$key] = $value;
		
		// since the logger accesses the global $vars variable we must change the values there also
		$GLOBALS['vars'][$key] = $value;
	}
	
	$chatBot->send('Config file has been reloaded.', $sendto);
}

?>
