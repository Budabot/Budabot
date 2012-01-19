<?php
	$db->loadSQLFile($MODULE_NAME, "feedback");
    
	$command->register($MODULE_NAME, "", "feedback.php", "feedback", "all", "Allows people to add and see feedback", "feedback.txt");
?>