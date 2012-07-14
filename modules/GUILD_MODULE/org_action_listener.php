<?php

if (preg_match("/^(.+) just left your organization.$/", $message, $arr)) {
	$actor = $arr[1];
	$actee = "";
	$action = "left";
	$time = time();

	$sql = "INSERT INTO `#__org_history` (actor, actee, action, organization, time) VALUES (?, ?, ?, '<myguild>', ?) ";
	$db->exec($sql, $actor, $actee, $action, $time);
} else if (preg_match("/^(.+) kicked (.+) from your organization.$/", $message, $arr)) {
	$actor = $arr[1];
	$actee = $arr[2];
	$action = "kicked";
	$time = time();

	$sql = "INSERT INTO `#__org_history` (actor, actee, action, organization, time) VALUES (?, ?, ?, '<myguild>', ?) ";
	$db->exec($sql, $actor, $actee, $action, $time);
} else if (preg_match("/^(.+) invited (.+) to your organization.$/", $message, $arr)) {
	$actor = $arr[1];
	$actee = $arr[2];
	$action = "invited";
	$time = time();

	$sql = "INSERT INTO `#__org_history` (actor, actee, action, organization, time) VALUES (?, ?, ?, '<myguild>', ?) ";
	$db->exec($sql, $actor, $actee, $action, $time);
} else if (preg_match("/^(.+) removed inactive character (.+) from your organization.$/", $message, $arr)) {
	$actor = $arr[1];
	$actee = $arr[2];
	$action = "removed";
	$time = time();

	$sql = "INSERT INTO `#__org_history` (actor, actee, action, organization, time) VALUES (?, ?, ?, '<myguild>', ?) ";
	$db->exec($sql, $actor, $actee, $action, $time);
}

?>
