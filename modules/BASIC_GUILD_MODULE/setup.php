<?php

// Alternative Character Table
$db->query("CREATE TABLE IF NOT EXISTS alts (`alt` VARCHAR(25) NOT NULL PRIMARY KEY, `main` VARCHAR(25))");

?>
