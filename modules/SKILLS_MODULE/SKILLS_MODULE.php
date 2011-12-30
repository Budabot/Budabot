<?php
	require_once 'utils.php';

	//Skills module
	$command->register($MODULE_NAME, "", "aggdef.php", "aggdef", "all", "Agg/Def: Calculates weapon inits for your Agg/Def bar.");
	$command->register($MODULE_NAME, "", "as.php", "as", "all", "AS: Calculates Aimed Shot.");
	$command->register($MODULE_NAME, "", "nanoinit.php", "nanoinit", "all", "Nanoinit: Calculates Nano Init.");
	$command->register($MODULE_NAME, "", "fullauto.php", "fullauto", "all", "Fullauto: Calculates Full Auto recharge.");
	$command->register($MODULE_NAME, "", "burst.php", "burst", "all", "Burst: Calculates Burst.");
	$command->register($MODULE_NAME, "", "fling.php", "fling", "all", "Fling: Calculates Fling.");
	$command->register($MODULE_NAME, "", "mafist.php", "mafist", "all", "MA Fist: Calculates your fist speed.");
	$command->register($MODULE_NAME, "", "dimach.php", "dimach", "all", "Dimach: Calculates dimach facts.");
	$command->register($MODULE_NAME, "", "brawl.php", "brawl", "all", "Brawl: Calculates brawl facts.");
	$command->register($MODULE_NAME, "", "fastattack.php", "fastattack", "all", "Fastattack: Calculates Fast Attack recharge.");
	
	//Xyphos' tools
	$command->register($MODULE_NAME, "", "inits.php", "inits", "all", "shows how much inits you need for 1/1");
	$command->register($MODULE_NAME, "", "specials.php", "specials", "all", "shows how much skill you need to cap specials recycle");

	//Helpiles
	$help->register($MODULE_NAME, "aggdef", "aggdef.txt", "all", "How to use aggdef");
	$help->register($MODULE_NAME, "nanoinit", "nanoinit.txt", "all", "How to use nanoinit");
	$help->register($MODULE_NAME, "as", "as.txt", "all", "How to use as");
	$help->register($MODULE_NAME, "fullauto", "fullauto.txt", "all", "How to use fullauto");
	$help->register($MODULE_NAME, "fling", "fling.txt", "all", "How to use fling");
	$help->register($MODULE_NAME, "burst", "burst.txt", "all", "How to use burst");
	$help->register($MODULE_NAME, "mafist", "mafist.txt", "all", "How to use mafist");
	$help->register($MODULE_NAME, "brawl", "brawl.txt", "all", "How to use brawl");
	$help->register($MODULE_NAME, "dimach", "dimach.txt", "all", "How to use dimach");
	$help->register($MODULE_NAME, "fastattack", "fastattack.txt", "all", "How to use fastattack");
	$help->register($MODULE_NAME, "inits", "inits.txt", "all", "How to use inits");
	$help->register($MODULE_NAME, "specials", "specials.txt", "all", "How to use specials");
?>