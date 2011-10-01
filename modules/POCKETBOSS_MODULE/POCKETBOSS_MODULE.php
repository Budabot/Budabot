<?php
	DB::loadSQLFile($MODULE_NAME, "pocketboss");

	Command::register($MODULE_NAME, "", "pb.php", "pb", "all", "Shows what symbs a PB drops");
	Command::register($MODULE_NAME, "", "symb.php", "symb", "all", "Shows what PB drops a symb");

    Help::register($MODULE_NAME, "pb", "pb.txt", "all", "See what drops which Pocketboss");
	Help::register($MODULE_NAME, "symb", "symb.txt", "all", "How to find symbs");
?>