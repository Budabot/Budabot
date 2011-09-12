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
// Create a new bank. 
/////////////////////////////////////////////////
if (preg_match("/^bankmake$/i", $message)) {
	$db->query("SELECT * FROM orgbank_<dim> WHERE `bankowner` = '$sender' ");
	if ($db->numrows() > 0) { 
		$msg = "Don't be greedy, you already have a bank!";
	} else {
		// Make a default placeholder for the bank
		$slot = 0;
		$timestamp =time();
		$lowid = 0;
		$highid = 0;
		$ql = 0 ;
		$itemname = 0;
		$quantity = 0;
		$comment = "";
		$banktitle = "Orgbank";
		$bankmenu = "open";
		$db->query("INSERT INTO orgbank_<dim> VALUES ( '$sender', '$slot', '$timestamp', '$lowID', '$highID', '$ql', '$itemname', '$quantity', '$comment', '$banktitle', '$bankmenu' )");
		$msg = "Created an empty bank for you!";
	}
	$chatBot->send($msg, $sendto);
}		

/////////////////////////////////////////////////
// Delete an existing bank. 
/////////////////////////////////////////////////
else if (preg_match("/^bankkill$/i", $message)) {
	$db->query("SELECT * FROM orgbank_<dim> WHERE `bankowner` = '$sender' ");
	if ($db->numrows() < 1) { 
		$msg = "You don't appear to have a Bank!";
	} else {
		 //Delete the bank and all items. 

		$db->query("DELETE FROM orgbank_<dim> WHERE `bankowner` = '$sender' ");
		$msg = "Your bank was deleted.";	
	}
	$chatBot->send($msg, $sendto);
}

?>