<?php
	$db->add_table_replace('#__quote', 'quote');
	$db->loadSQLFile($MODULE_NAME, "quote");

	//Commands
	$command->register($MODULE_NAME, "", "quote.php", "quote", "guild", "Add/Remove/View Quotes", "quote.txt");

	$setting->add($MODULE_NAME, "quote_stat_count", "Number of users shown in stats", "edit", "number", "10");
?>