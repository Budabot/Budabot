<?php

	$MODULE_NAME = "SHOPPING_MODULE";

	Command::register($MODULE_NAME, "", "shopping.php", "wtb", "all", "Search for items people want to sell on the shopping channels", 'shopping');
	Command::register($MODULE_NAME, "", "shopping.php", "wts", "all", "Search for items people want to buy on the shopping channels", 'shopping');
	
	Help::register($MODULE_NAME, "shopping", "shopping.txt", "guild", "How to use wtb/wts");

?>