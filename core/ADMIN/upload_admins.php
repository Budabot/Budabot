<?php

$db->exec("CREATE TABLE IF NOT EXISTS admin_<myname> (`name` VARCHAR(25) NOT NULL PRIMARY KEY, `adminlevel` INT)");

$chatBot->vars["SuperAdmin"] = ucfirst(strtolower($chatBot->vars["SuperAdmin"]));

$data = $db->query("SELECT * FROM admin_<myname> WHERE `name` = ?", $chatBot->vars["SuperAdmin"]););
if (count($data) == 0) {
	$db->exec("INSERT INTO admin_<myname> (`adminlevel`, `name`) VALUES (?, ?)", '4', $chatBot->vars["SuperAdmin"]);
} else {
	$db->exec("UPDATE admin_<myname> SET `adminlevel` = ? WHERE `name` = ?", '4', $chatBot->vars["SuperAdmin"]);
}

$data = $db->query("SELECT * FROM admin_<myname>");
forEach ($data as $row) {
	$chatBot->admins[$row->name]["level"] = $row->adminlevel;
}

?>