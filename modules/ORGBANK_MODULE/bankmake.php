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

$table = "orgbank_".$this->vars["dimension"];
$owner = $sender; 
$slot = "0";


$message = str_replace("'", "\'", $message);
// Either client.exe, server, bot is changing chars to html code
// We may need to switch them around.
//$htmlcode=array("&amp;", "&quot;", "&lt;", "&gt;");$snglchar=array('&','"', '<', '>');

/////////////////////////////////////////////////
// Create a new bank. 
/////////////////////////////////////////////////
if( eregi("^bankmake$", $message)) {

	$db->query("SELECT * FROM $table WHERE `bankowner` = '$owner' ");
	if ($db->numrows() > 0) { 
		$msg = "Don't be greedy, you already have a bank!";
	}ELSE {
		// Make a default placeholder for the bank
		$owner = $sender; 
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
		$db->query("INSERT INTO '$table' VALUES ( '$owner', '$slot', '$timestamp', '$lowID', '$highID', '$ql', '$itemname', '$quantity', '$comment', '$banktitle', '$bankmenu' )");
		$msg = "Created an empty bank for you!";
	}
}		

/////////////////////////////////////////////////
// Delete an existing bank. 
/////////////////////////////////////////////////
if( eregi("^bankkill$", $message)) {

	$db->query("SELECT * FROM $table WHERE `bankowner` = '$owner' ");
	if ($db->numrows() < 1) { 
		$msg = "You don't appear to have a Bank!";
	}
	ELSE {
		 //Delete the bank and all items. 

		$db->query("DELETE FROM $table WHERE `bankowner` = '$owner' ");
		$msg = "Your bank was deleted.";	
	}
}

/////////////////////////////////////////////////
// we have a message after all that? post it
/////////////////////////////////////////////////
$msg = str_replace("\'", "'", $msg);
if ($msg){	// Send info back
	if($type == "msg"){
		$chatBot->send($msg, $sender);
	}
	
	
}


?>