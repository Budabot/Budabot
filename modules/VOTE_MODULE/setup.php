<?php
//Create Vote Table
$table = "vote_<myname>";

//$db->query("DROP TABLE IF EXISTS $table");
$db->query("CREATE TABLE IF NOT EXISTS $table (`question` TEXT(500), `author` TEXT (80), `started` INT (10), `duration` INT (10), `answer` TEXT(500), `status` INT (1))");

if(!isset($this->vars["Vote"])) {
	// Upload votes that are still running to global vars
	
	$db->query("SELECT * FROM $table WHERE `status` < '8' AND `duration` IS NOT NULL");

	while($row = $db->fObject()) {
		$this->vars["Vote"][$row->question] = array("author" => $row->author, "started" => $row->started, "duration" => $row->duration, "answer" => $row->answer, "lockout" => $row->status);
	}
}
?>