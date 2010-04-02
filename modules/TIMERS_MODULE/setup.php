<?
// Timer Table
$db->query("CREATE TABLE IF NOT EXISTS timers_<myname> (`name` VARCHAR(255), `owner` VARCHAR(25), `mode` VARCHAR(10), `timer` int, `settime` int)");

if(!isset($this->vars["Timers"])) {
	//Upload timers to global vars
	$db->query("SELECT * FROM timers_<myname>");
	while($row = $db->fObject())
	  	$this->vars["Timers"][] = array("name" => $row->name, "owner" => $row->owner, "mode" => $row->mode, "timer" => $row->timer, "settime" => $row->settime);
}
?> 