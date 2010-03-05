<?
$db->query("CREATE TABLE IF NOT EXISTS roll_<myname> (`id` INTEGER PRIMARY KEY AUTO_INCREMENT, `time` INT, `name` VARCHAR(25), `type` INT, `start` INT, `end` INT, `result` INT)");
$db->query("SELECT * FROM roll_<myname>");
if($db->numrows() >= 200)
	$db->query("DELETE FROM roll_<myname>");
	
$db->query("CREATE TABLE IF NOT EXISTS koslist_<myname> (`time` INT PRIMARY KEY NOT NULL, `name` VARCHAR(25), `sender` VARCHAR(25), `reason` VARCHAR(50) DEFAULT '0')");
?>