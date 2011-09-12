<?
/*
** Author: Elimeta of Team_Eli (RK2)
** Description: Org Bank
** Version: 1.0
**
** Developed for: Budabot(http://sourceforge.net/projects/budabot)
**
** Many thanks to Lucier (RK1), without who's bank module, Org Bank 
** would not exist.
**
** Date(created): 27.04.2011
** Date(last modified): 20.04.2011
*/

/////////////////////////////////////////////////
// Toggle shopmenu open & closed 
/////////////////////////////////////////////////
if (preg_match("/^bankmenu (.+)?$/i", $message, $arr)) {
	$slot = "0";
	$command = trim("$arr[1]");
	$db->query("SELECT * FROM orgbank_<dim> WHERE `bankowner` = '$sender' ");
	if ($db->numrows() < 1) { // No Shop to find
		$msg .= "You need to create a shop first. Type bank for help. ";	
	} else if ($db->numrows() > 0) {// Found it
		if ($command == "open") {
			$bankmenu = "open";
			$setstr = "`bankmenu` = '$bankmenu'";
			$db->query("UPDATE orgbank_<dim> SET $setstr WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
			$msg .= "<green>Your bankmenu is now: <white>$bankmenu<end><green>.<end>";
		} 
		if ($command == "closed") {
			$bankmenu = "closed";
			$setstr = "`bankmenu` = '$bankmenu'";
			$db->query("UPDATE orgbank_<dim> SET $setstr WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
			$msg .= "<green>Your bankmenu is now: <white>$bankmenu<end><green> .<end>";
		} 
	}
}

if ($msg) {
	$chatBot->send($msg, $sendto);
}

?>