<?php
	$db->loadSQLFile($MODULE_NAME, "pocketboss");

	$command->register($MODULE_NAME, "", "pb.php", "pb", "all", "Shows what symbs a PB drops", "pb.txt");
	$command->register($MODULE_NAME, "", "symb.php", "symb", "all", "Shows what PB drops a symb", "symb.txt");
?>