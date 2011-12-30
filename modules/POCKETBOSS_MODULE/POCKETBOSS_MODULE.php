<?php
	$db->loadSQLFile($MODULE_NAME, "pocketboss");

	$command->register($MODULE_NAME, "", "pb.php", "pb", "all", "Shows what symbs a PB drops");
	$command->register($MODULE_NAME, "", "symb.php", "symb", "all", "Shows what PB drops a symb");

    $help->register($MODULE_NAME, "pb", "pb.txt", "all", "See what drops which Pocketboss");
	$help->register($MODULE_NAME, "symb", "symb.txt", "all", "How to find symbs");
?>