<?php

// for people upgrading from 2.2, any command that required 'guildadmin' access now requires 'rl' access
$db->exec("UPDATE cmdcfg_<myname> SET `admin` = 'rl' WHERE `admin` = 'guildadmin'");
$db->exec("UPDATE hlpcfg_<myname> SET `admin` = 'rl' WHERE `admin` = 'guildadmin'");

// TODO update settings which were changed to time settings

?>