<?php

// For people upgrading from 2.2, any command that required 'guildadmin' access now requires 'rl' access.
$db->exec("UPDATE cmdcfg_<myname> SET `admin` = 'rl' WHERE `admin` = 'guildadmin'");
$db->exec("UPDATE hlpcfg_<myname> SET `admin` = 'rl' WHERE `admin` = 'guildadmin'");

// Add validated column to alts table.
$db->query("SELECT `validated` FROM `alts`");
if ($db->errorInfo[0] != "00000") {
	$db->exec("ALTER TABLE `alts` ADD COLUMN `validated` TINYINT(1) NOT NULL DEFAULT 0");
}

// Add logoff_msg column to org_members_<myname>.
$db->query("SELECT `logoff_msg` FROM `org_members_<myname>`");
if ($db->errorInfo[0] != "00000") {
	$db->exec("ALTER TABLE `org_members_<myname>` ADD COLUMN `logoff_msg` VARCHAR(400) DEFAULT ''");
}

// Remove name history for characters that don't actually exist.
$db->exec("DELETE FROM name_history WHERE charid = '-1' OR charid = '4294967295'");

// Update number settings which were changed to time settings.
$db->query("SELECT * FROM settings_<myname> WHERE name = 'online_expire' OR name = 'defaultraffletime' OR name = 'shop_message_age'");
$data = $db->fObject('all');
forEach ($data as $row) {
	if ($row->name == 'online_expire' && $row->value < 60) {
		$newvalue = $row->value * 60;
		$db->exec("UPDATE settings_<myname> SET value = '$newvalue' WHERE name = 'online_expire'");
	}
	if ($row->name == 'defaultraffletime' && $row->value < 60) {
		$newvalue = $row->value * 60;
		$db->exec("UPDATE settings_<myname> SET value = '$newvalue' WHERE name = 'defaultraffletime'");
	}
	if ($row->name == 'shop_message_age' && $row->value < 86400) {
		$newvalue = $row->value * 86400;
		$db->exec("UPDATE settings_<myname> SET value = '$newvalue' WHERE name = 'shop_message_age'");
	}
}

// update admin levels
$db->exec("UPDATE cmdcfg_<myname> SET admin = 'admin' WHERE admin = '4'");
$db->exec("UPDATE cmdcfg_<myname> SET admin = 'mod' WHERE admin = '3'");
$db->exec("UPDATE cmdcfg_<myname> SET admin = 'rl' WHERE admin = '2'");
$db->exec("UPDATE cmdcfg_<myname> SET admin = 'leader' WHERE admin = '1'");

$db->exec("UPDATE settings_<myname> SET admin = 'admin' WHERE admin = '4'");
$db->exec("UPDATE settings_<myname> SET admin = 'mod' WHERE admin = '3'");
$db->exec("UPDATE settings_<myname> SET admin = 'rl' WHERE admin = '2'");
$db->exec("UPDATE settings_<myname> SET admin = 'leader' WHERE admin = '1'");

$db->exec("UPDATE hlpcfg_<myname> SET admin = 'admin' WHERE admin = '4'");
$db->exec("UPDATE hlpcfg_<myname> SET admin = 'mod' WHERE admin = '3'");
$db->exec("UPDATE hlpcfg_<myname> SET admin = 'rl' WHERE admin = '2'");
$db->exec("UPDATE hlpcfg_<myname> SET admin = 'leader' WHERE admin = '1'");

// remove cyclic fast/fastattack alias
$db->exec("DELETE FROM cmd_alias_<myname> WHERE cmd = 'fast' AND alias = 'fastattack'");

?>