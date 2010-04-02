<?
/*
Table Description
name = Name of the setting and also index of $this-vars
mode = if this setting is editable or not
		edit = This setting is editable
		hide = This setting is not shown on !settings list
		noedit = Not changable
setting = Current Setting
options = Allowed Options for this setting
		text = any text(upto 50chars)
		text;maxLength = any text(up to max Length)
		number = any number
		number;start-end = any number between start and end
		color = any HMTL Color code
		option1;option2 = List of Options seperated by a ;
intoptions = Internal Version of options
		ONLY usable for a list of options
descriptions = Description of this setting, this is shown on !settings
source = From where is this setting coming
		db = Added by a module
		cfg = added by thy config.php
admin = Rank that is needed for this setting (admin or mod)
help = Helpfile for this setting
*/

$db->query("CREATE TABLE IF NOT EXISTS settings_<myname> (`name` VARCHAR(30) NOT NULL, `mod` VARCHAR(50), `mode` VARCHAR(10), `setting` VARCHAR(50) Default '0', `options` VARCHAR(50) Default '0', `intoptions` VARCHAR(50) DEFAULT '0', `description` VARCHAR(50), `source` VARCHAR(5), `admin` VARCHAR(25), `help` VARCHAR(60))");
?>