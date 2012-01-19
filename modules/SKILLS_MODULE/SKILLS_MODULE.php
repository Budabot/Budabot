<?php
	require_once 'utils.php';

	//Skills module
	$command->register($MODULE_NAME, "", "aggdef.php", "aggdef", "all", "Agg/Def: Calculates weapon inits for your Agg/Def bar", "aggdef.txt");
	$command->register($MODULE_NAME, "", "as.php", "as", "all", "AS: Calculates Aimed Shot", "as.txt");
	$command->register($MODULE_NAME, "", "nanoinit.php", "nanoinit", "all", "Nanoinit: Calculates Nano Init", "nanoinit.txt");
	$command->register($MODULE_NAME, "", "fullauto.php", "fullauto", "all", "Fullauto: Calculates Full Auto recharge", "fullauto.txt");
	$command->register($MODULE_NAME, "", "burst.php", "burst", "all", "Burst: Calculates Burst", "burst.txt");
	$command->register($MODULE_NAME, "", "fling.php", "fling", "all", "Fling: Calculates Fling", "fling.txt");
	$command->register($MODULE_NAME, "", "mafist.php", "mafist", "all", "MA Fist: Calculates your fist speed", "mafist.txt");
	$command->register($MODULE_NAME, "", "dimach.php", "dimach", "all", "Dimach: Calculates dimach facts", "dimach.txt");
	$command->register($MODULE_NAME, "", "brawl.php", "brawl", "all", "Brawl: Calculates brawl facts", "brawl.txt");
	$command->register($MODULE_NAME, "", "fastattack.php", "fastattack", "all", "Fastattack: Calculates Fast Attack recharge", "fastattack.txt");
	
	//Xyphos' tools
	$command->register($MODULE_NAME, "", "inits.php", "inits", "all", "shows how much inits you need for 1/1", "inits.txt");
	$command->register($MODULE_NAME, "", "specials.php", "specials", "all", "shows how much skill you need to cap specials recycle", "specials.txt");
?>