<?php
	$MODULE_NAME = "PANDE_MODULE";
	
	// Pande loot manager
	bot::command("", "$MODULE_NAME/pandeloot.php", "beastarmor", "all", "Shows Possible Beast Armor Loot");
	bot::command("", "$MODULE_NAME/pandeloot.php", "beastweaps", "all", "Shows Possible Beast Weapons Loot");
	bot::command("", "$MODULE_NAME/pandeloot.php", "beaststars", "all", "Shows Possible Beast Stars Loot");
	bot::command("", "$MODULE_NAME/pandeloot.php", "tnh", "all", "Shows Possible The Night Heart Loot");
	bot::command("", "$MODULE_NAME/pandeloot.php", "sb", "all", "Shows Possible Shadowbreeds Loot");
	bot::command("", "$MODULE_NAME/pandeloot.php", "aries", "all", "Shows Possible Aries Zodiac Loot");
	bot::command("", "$MODULE_NAME/pandeloot.php", "leo", "all", "Shows Possible Leo Zodiac Loot");
	bot::command("", "$MODULE_NAME/pandeloot.php", "virgo", "all", "Shows Possible Virgo Zodiac Loot");
	bot::command("", "$MODULE_NAME/pandeloot.php", "aquarius", "all", "Shows Possible Aquarius Zodiac Loot");
	bot::command("", "$MODULE_NAME/pandeloot.php", "cancer", "all", "Shows Possible Cancer Zodiac Loot");
	bot::command("", "$MODULE_NAME/pandeloot.php", "gemini", "all", "Shows Possible Gemini Zodiac Loot");
	bot::command("", "$MODULE_NAME/pandeloot.php", "libra", "all", "Shows Possible Libra Zodiac Loot");
	bot::command("", "$MODULE_NAME/pandeloot.php", "pisces", "all", "Shows Possible Pisces Zodiac Loot");
	bot::command("", "$MODULE_NAME/pandeloot.php", "taurus", "all", "Shows Possible Taurus Zodiac Loot");
	bot::command("", "$MODULE_NAME/pandeloot.php", "capricorn", "all", "Shows Possible Capricorn Zodiac Loot");
	bot::command("", "$MODULE_NAME/pandeloot.php", "sagittarius", "all", "Shows Possible Sagittarius Zodiac Loot");
	bot::command("", "$MODULE_NAME/pandeloot.php", "scorpio", "all", "Shows Possible Scorpio Zodiac Loot");
	bot::command("", "$MODULE_NAME/pandeloot.php", "pandeloot", "leader", "used to add pande loot to the loot list");
	bot::command("", "$MODULE_NAME/pandeloot.php", "pande", "all", "shows Initial list of pande bosses");

	//Help files
	bot::help("pande", "$MODULE_NAME/pande.txt", "all", "Loot manager for Pandemonium Raid loot");
?>
