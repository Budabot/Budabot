<?php
	require_once 'utils.php';

	//Skills module
	Command::register($MODULE_NAME, "", "aggdef.php", "aggdef", "all", "Agg/Def: Calculates weapon inits for your Agg/Def bar.");
	Command::register($MODULE_NAME, "", "as.php", "as", "all", "AS: Calculates Aimed Shot.");
	Command::register($MODULE_NAME, "", "nanoinit.php", "nanoinit", "all", "Nanoinit: Calculates Nano Init.");
	Command::register($MODULE_NAME, "", "fullauto.php", "fullauto", "all", "Fullauto: Calculates Full Auto recharge.");
	Command::register($MODULE_NAME, "", "burst.php", "burst", "all", "Burst: Calculates Burst.");
	Command::register($MODULE_NAME, "", "fling.php", "fling", "all", "Fling: Calculates Fling.");
	Command::register($MODULE_NAME, "", "mafist.php", "mafist", "all", "MA Fist: Calculates your fist speed.");
	Command::register($MODULE_NAME, "", "dimach.php", "dimach", "all", "Dimach: Calculates dimach facts.");
	Command::register($MODULE_NAME, "", "brawl.php", "brawl", "all", "Brawl: Calculates brawl facts.");
	Command::register($MODULE_NAME, "", "fastattack.php", "fastattack", "all", "Fastattack: Calculates Fast Attack recharge.");
	
	//Xyphos' tools
	Command::register($MODULE_NAME, "", "inits.php", "inits", "all", "shows how much inits you need for 1/1");
	Command::register($MODULE_NAME, "", "specials.php", "specials", "all", "shows how much skill you need to cap specials recycle");

	//Helpiles
	Help::register($MODULE_NAME, "aggdef", "aggdef.txt", "all", "How to use aggdef");
	Help::register($MODULE_NAME, "nanoinit", "nanoinit.txt", "all", "How to use nanoinit");
	Help::register($MODULE_NAME, "as", "as.txt", "all", "How to use as");
	Help::register($MODULE_NAME, "fullauto", "fullauto.txt", "all", "How to use fullauto");
	Help::register($MODULE_NAME, "fling", "fling.txt", "all", "How to use fling");
	Help::register($MODULE_NAME, "burst", "burst.txt", "all", "How to use burst");
	Help::register($MODULE_NAME, "mafist", "mafist.txt", "all", "How to use mafist");
	Help::register($MODULE_NAME, "brawl", "brawl.txt", "all", "How to use brawl");
	Help::register($MODULE_NAME, "dimach", "dimach.txt", "all", "How to use dimach");
	Help::register($MODULE_NAME, "fastattack", "fastattack.txt", "all", "How to use fastattack");
	Help::register($MODULE_NAME, "inits", "inits.txt", "all", "How to use inits");
	Help::register($MODULE_NAME, "specials", "specials.txt", "all", "How to use specials");
?>