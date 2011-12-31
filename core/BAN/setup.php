<?php

$db->query("CREATE TABLE IF NOT EXISTS banlist_<myname> (name VARCHAR(25) NOT NULL PRIMARY KEY, admin VARCHAR(25), time INT, reason TEXT, banend INT)");

$ban = Registry::getInstance('ban');
$ban->upload_banlist();

?>