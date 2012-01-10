<?php
	require_once 'SignupController.class.php';
	
	$chatBot->registerInstance($MODULE_NAME, 'SignupController', new SignupController());
	
	$db->loadSQLFile($MODULE_NAME, 'signup');

	// Commands
	$command->register($MODULE_NAME, "", "waitlist.php", "waitlist", "all", "Show/Set the Waitlist");

	// Helpfile
	$help->register($MODULE_NAME, "waitlist", "waitlist.txt", "all", "How to use waitlist");
?>