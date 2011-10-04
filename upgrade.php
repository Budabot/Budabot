<?php

// for people upgrading from 2.2, any command that required 'guildadmin' access now requires 'rl' access
$db->exec("UPDATE cmdcfg_<myname> SET `admin` = 'rl' WHERE `admin` = 'guildadmin'");
$db->exec("UPDATE hlpcfg_<myname> SET `admin` = 'rl' WHERE `admin` = 'guildadmin'");

// add validated column to alts table
$db->query("SELECT `validated` FROM `alts`");
if ($db->errorInfo[0] != "00000") {
	$db->exec("ALTER TABLE `alts` ADD COLUMN `validated` TINYINT(1) NOT NULL DEFAULT 0"); //Add the validated column
}

// TODO update settings which were changed to time settings

?>