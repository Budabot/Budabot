<?php
	$db->loadSQLFile($MODULE_NAME, "whereis");
	
	$command->register($MODULE_NAME, "", "whereis.php", "whereis", "all", "Whereis Database");
	
	$help->register($MODULE_NAME, "whereis", "whereis.txt", "all", "Whereis Database");
	
?>