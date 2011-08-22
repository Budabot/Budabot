<?php

$db = DB::get_instance();

$db->query("SELECT `validated` FROM `alts`");

if ($db->errorInfo[0] != "00000")
{
	//No column
	$db->exec("ALTER TABLE `alts` ADD COLUMN `validated` TINYINT(1) NOT NULL"); //Add the validated column
}

?>