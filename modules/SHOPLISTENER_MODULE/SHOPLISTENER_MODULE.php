<?php
	$MODULE_NAME = "SHOPLISTENER_MODULE";

	DB::loadSQLFile($MODULE_NAME, 'shopping_messages');	
	DB::loadSQLFile($MODULE_NAME, 'shopping_items');

	Event::register($MODULE_NAME, "allpackets", "capture.php", "none", "Capture messages from shopping channel");
	Event::register($MODULE_NAME, "24hrs", "remove_old_messages.php", "none", "Remove old shopping messages from the database");
	
	Setting::add($MODULE_NAME, "shop_message_age", "The number of days to keep shopping messages", "edit", "options", "10", "1;2;5;10;15;20");
	
	Command::register($MODULE_NAME, "", "shop.php", "shop", "member", "Searches shopping messages");
?>