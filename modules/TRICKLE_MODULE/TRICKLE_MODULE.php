<?php
	require_once 'trickle_functions.php';

	$db->loadSQLFile($MODULE_NAME, "trickle");

	$command->register($MODULE_NAME, "", "trickle.php", "trickle", "all", "Shows how much skills you will gain by increasing an ability");

	Help::register($MODULE_NAME, "trickle", "trickle.txt", "all", "How to use trickle");
?>
