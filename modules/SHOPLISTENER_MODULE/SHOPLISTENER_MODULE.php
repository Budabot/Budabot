<?php
	DB::loadSQLFile($MODULE_NAME, 'shopping_messages');	
	DB::loadSQLFile($MODULE_NAME, 'shopping_items');
	DB::loadSQLFile($MODULE_NAME, 'aodb_items');

	Event::register($MODULE_NAME, "allpackets", "capture.php", "none", "Capture messages from shopping channel");
	Event::register($MODULE_NAME, "24hrs", "remove_old_messages.php", "none", "Remove old shopping messages from the database");
	
	Setting::add($MODULE_NAME, "shop_message_age", "How long to keep shopping messages", "edit", "time", "10d", "1d;2d;5d;10d;15d;20d");
	
	Command::register($MODULE_NAME, "", "shop.php", "shop", "member", "Searches shopping messages");
?>