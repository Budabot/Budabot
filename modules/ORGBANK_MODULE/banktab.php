<?
/*
** Author: Elimeta of Team_Eli (RK2)
** Description: Orgbank
** Version: 1.0
**
** Developed for: Budabot(http://sourceforge.net/projects/budabot)
**
** Many thanks to Lucier (RK1), without who's bank module, Orgbank
** would not exist.
**
** Date(created): 27.04.2011
** Date(last modified): 20.04.2011
*/

/////////////////////////////////////////////////
// Toggle banktab between choices
/////////////////////////////////////////////////
if (preg_match("/^banktab (.+)?$/i", $message, $arr)) {
	$slot = "0";
	$command = trim($arr[1]);
	$db->query("SELECT * FROM orgbank_<dim> WHERE `bankowner` = '$sender' ");
	if ($db->numrows() < 1) { // No bank to find
		$msg .= "You need to create a bank first. Type <symbol>bank for help.";	
	} else if ($db->numrows() > 0) {// Found it
		$newtab = ucfirst($command);
		if ($command == "other" || $command == "armour" || $command == "nano" || $command == "weapon") {
			$db->query("UPDATE orgbank_<dim> SET `banktab` = '$command' WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
			$msg .= "Your bank will now open at the: <white>$newtab<end> tab.\n";
		} else {
			$msg = "Sorry, what was that? You want how many Banana's?";
		}
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>