<?php
// Creating Tables
// Guild Chatlist
$db->query("CREATE TABLE IF NOT EXISTS guild_chatlist_<myname> (`name` CHAR(25) PRIMARY KEY, `profession` CHAR(20), `guild` CHAR(255), `rank` CHAR(25), `breed` CHAR(25), `level` INT, `ai_level` INT, `afk` VARCHAR(255) DEFAULT '0')");

// Alternative Character Table
$db->query("CREATE TABLE IF NOT EXISTS alts (`alt` VARCHAR(25) NOT NULL PRIMARY KEY, `main` VARCHAR(25))");

$db->query("CREATE TABLE IF NOT EXISTS priv_chatlist_<myname> (`name` CHAR(25) PRIMARY KEY, `faction` CHAR(10), `profession` CHAR(20), `guild` CHAR(255), `breed` CHAR(25), `level` INT, `ai_level` INT, `afk` VARCHAR(255) DEFAULT '0', `guest` INT DEFAULT '0')");

// Set Delay for notify on/off(prevent spam from org roster module)
$this->vars["onlinedelay"] = time() + $this->settings["CronDelay"] + 60;
?>
