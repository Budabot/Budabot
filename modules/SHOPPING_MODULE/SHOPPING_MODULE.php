<?php
	$command->register($MODULE_NAME, "", "shopping.php", "wtb", "all", "Search for items people want to sell on the shopping channels", 'shopping');
	$command->register($MODULE_NAME, "", "shopping.php", "wts", "all", "Search for items people want to buy on the shopping channels", 'shopping');
	
	$setting->add($MODULE_NAME, 'max_shopping_results', 'Number of Items shown for wtb/wts', 'edit', "number", '20', '10;15;20;25', "", "mod");
	
	$help->register($MODULE_NAME, "shopping", "shopping.txt", "guild", "How to use wtb/wts");

?>