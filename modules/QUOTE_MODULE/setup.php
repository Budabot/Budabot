<?php

// In case we need to delete all the quotes
//$db->query("DROP TABLE IF EXISTS quote");

// Create quote table
$db->query("CREATE TABLE IF NOT EXISTS quote (`IDNumber` INTEGER NOT NULL PRIMARY KEY, `Who` VARCHAR(25), `OfWho` VARCHAR(25), `When` VARCHAR(25), `What` VARCHAR(1000))");

// Auto update quote table (in case its an older version with a smaller quote size limit.)
// I don't think there would be any probs if there are 0 quotes in the table.
$mydir = "./modules/QUOTE_MODULE/";
if (file_exists($mydir."quoteupdate.php")) {
	include $mydir."quoteupdate.php";
	// rename file so we dont run it on every !newplugins or !reboot
	rename($mydir."quoteupdate.php",$mydir."quoteupdate-finished.php");
}
?>
