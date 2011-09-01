<?php
	$MODULE_NAME = "AOU_MODULE";

	Command::register($MODULE_NAME, "", "aou.php", "aou", "all", "Find a guide from AO-Universe");

	// Help files
    Help::register($MODULE_NAME, "aou", "aou.txt", "all", "How to find a guide from AO-Universe");
?>
