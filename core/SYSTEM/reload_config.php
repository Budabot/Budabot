<?php

if (preg_match("/^reloadconfig$/i", $message)) {
	global $configFile;
	$configFile->load();
	$vars = $configFile->getVars();

	// remove variables that shouldn't change without a restart
	unset($vars['name']);
	unset($vars['login']);
	unset($vars['password']);
	unset($vars['dimension']);

	unset($vars["DB Type"]);
	unset($vars["DB Name"]);
	unset($vars["DB Host"]);
	unset($vars["DB username"]);
	unset($vars["DB password"]);

	forEach ($vars as $key => $value) {
		$chatBot->vars[$key] = $value;

		// since the logger accesses the global $vars variable we must change the values there also
		$GLOBALS['vars'][$key] = $value;
	}

	$sendto->reply('Config file has been reloaded.');
}

?>
