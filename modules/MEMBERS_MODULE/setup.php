<?
$db->query("CREATE TABLE IF NOT EXISTS members_<myname> (`name` VARCHAR(25), `autoinv` INT DEFAULT '0')");

$db->query("SELECT * FROM members_<myname>");
if($db->numrows() != 0)
	while($row = $db->fObject())
		$this->members[$row->name] = true;
?>