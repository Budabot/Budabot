<?php
	$MODULE_NAME = "FEEDBACK_MODULE";
	
	DB::loadSQLFile($MODULE_NAME, "feedback");
    
	bot::command("", "$MODULE_NAME/feedback.php", "feedback", "all", "Allows people to add and see feedback");
	
	Help::register($MODULE_NAME, "feedback", "feedback.txt", "all", "Feedback usage");
?>