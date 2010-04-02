<?
//Create Points Database
$db->query("CREATE TABLE IF NOT EXISTS points_db_<myname> (`name` VARCHAR(25), `points` int(5))");

//Create Raidhistory Table
$db->query("CREATE TABLE IF NOT EXISTS raids_history_<myname> (`winner` TEXT (80), `points` TEXT(10), `time` INT (10), `item` TEXT(50), `icon` INT (10), `aoid` INT(7), `ql` INT (5))");

//Create Table for raid settings
$db->query("CREATE TABLE IF NOT EXISTS raids_settings_<myname> (`raid_name` TEXT(50), `pts` TEXT(10), `bid_cost` INT(3), `shortform` TEXT(20), `spawntime` INT(15), `next_spawn` INT(15), `status` INT(1), `flat_multiroll` INT(1))");

//Create Table for raid items
$db->query("CREATE TABLE IF NOT EXISTS raids_items_<myname> (`shortform` TEXT(20), `cat` TEXT(20), `item_name` TEXT(30), `item_aoid` INT(15), `item_ql` INT(3), `item_icon` INT(10), `item_amount` INT(10), `item_minlvl` INT(3), `item_mode` TEXT(4), `item_multiloot` INT(2))");
?>