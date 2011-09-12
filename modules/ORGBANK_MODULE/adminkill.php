<?
/*
** Author: Elimeta of Team_Eli (RK2)
** Description: Froob-friendly Shop Module
** Version: 1.0
**
** Developed for: Budabot(http://sourceforge.net/projects/budabot)
**
** Many thanks to Lucier (RK1), without who's bank module, MyShop 
** would not exist.
**
** Date(created): 27.04.2011
** Date(last modified): 20.04.2011
*/

/////////////////////////////////////////////////
// Admin Delete an existing shop. 
/////////////////////////////////////////////////
if (preg_match("/^adminkill (.+)$/i", $message, $arr)) {
	$to_kill = trim($arr[1]);
	$db->query("SELECT * FROM orgbank_<dim> WHERE `bankowner` = '$to_kill' ");
	if ($db->numrows() == 0) { 
		$msg = "Adminkill: $to_kill does not appear to have a bank?\n";
	} else {		
		// Delete the shop and all items.
		$db->query("DELETE FROM orgbank_<dim> WHERE `bankowner` = '$to_kill' ");
		$msg = "<highlight>Adminkill: $username<end> deleted. Shop removed.\n";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
