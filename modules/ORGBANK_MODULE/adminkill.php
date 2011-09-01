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



// Either client.exe, server, bot is changing chars to html code
// We may need to switch them around.
//$htmlcode=array("&amp;", "&quot;", "&lt;", "&gt;");$snglchar=array('&','"', '<', '>');

/////////////////////////////////////////////////
// Admin Delete an existing shop. 
/////////////////////////////////////////////////
$message = str_replace("'", "\'", $message);
if (preg_match("/^adminkill (.+)$/i", $message, $arr)) {
	$to_kill = trim($arr[1]);
	$db->query("SELECT * FROM $table WHERE `bankowner` = '$to_kill' ");
	if ($db->numrows() < 1) { 
		$msg .= "Adminkill: $to_kill does not appear to have a bank?\n";
	}
	if ($db->numrows() > 0) {		
		// Delete the shop and all items.
		$db->query("DELETE FROM $table WHERE `bankowner` = '$to_kill' ");
		$msg = "<highlight>Adminkill: $username<end> deleted. Shop removed.\n";
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