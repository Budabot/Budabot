<?php
	$MODULE_NAME = "SHOPLISTENER_MODULE";

	DB::loadSQLFile($MODULE_NAME, 'shopping_messages');	
	DB::loadSQLFile($MODULE_NAME, 'shopping_items');

	Event::register($MODULE_NAME, "allpackets", "capture.php", "none", "Capture messages from shopping channel");
	
	Command::register($MODULE_NAME, "", "shop.php", "shop", "member", "Searches shopping messages");
?>