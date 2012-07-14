<?php
	require_once 'Whompah.class.php';

	$db->loadSQLFile($MODULE_NAME, "whompah_cities");

	$command->register($MODULE_NAME, "", "whompah.php", "whompah", "all", "Shows the whompah route from one city to another", "whompah.txt");
	$commandAlias->register($MODULE_NAME, 'whompah', 'whompahs');
	$commandAlias->register($MODULE_NAME, 'whompah', 'whompa');
	$commandAlias->register($MODULE_NAME, 'whompah', 'whompas');
?>
