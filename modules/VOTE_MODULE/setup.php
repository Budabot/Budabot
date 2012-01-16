<?php

$db->exec("CREATE TABLE IF NOT EXISTS vote_<myname> (`question` TEXT(500), `author` TEXT (80), `started` INT (10), `duration` INT (10), `answer` TEXT(500), `status` INT (1))");

if (!isset($chatBot->data["Vote"])) {
	// Upload to memory votes that are still running
	
	$data = $db->query("SELECT * FROM vote_<myname> WHERE `status` < '8' AND `duration` IS NOT NULL");
	forEach ($data as $row) {
		$chatBot->data["Vote"][$row->question] = array("author" => $row->author, "started" => $row->started, "duration" => $row->duration, "answer" => $row->answer, "lockout" => $row->status);
	}
}
?>