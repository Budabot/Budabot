<?php
	$MODULE_NAME = "WHEREIS_MODULE";
	
	$this->loadSQLFile($MODULE_NAME, "whereis");
	
	$this->command("", "$MODULE_NAME/whereis.php", "whereis", ALL, "Whereis Database");
	
	$this->help("whereis", "$MODULE_NAME/whereis.txt", ALL, "Whereis Database");
	
?>