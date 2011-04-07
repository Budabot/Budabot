<?php
$db->query("CREATE TABLE IF NOT EXISTS waitlist_<myname> (`owner` VARCHAR(25), `name` VARCHAR(25), `position` INT, `time` INT)");

global $listbot_waitlist;
if (!is_array($listbot_waitlist)) {
	$db->query("SELECT * FROM waitlist_<myname>");
	$data = $db->fObject('all');
	forEach ($data as $row) {
		$listbot_waitlist[$row->owner][] = array("name" => $row->name, "position" => $row->position);
	}
}
?>