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

$msg = "";
$owner = $sender; 
$slot = "0";
$db->query("SELECT * FROM orgbank_<dim> WHERE `bankowner` = '$owner' ");
if ($db->numrows() < 1) { 
	$Shop_Owner = 0;
	$msg .= "You don't have a bank. Please type !bank for help...\n";
}

$message = str_replace("'", "\'", $message);
// Either client.exe, server, bot is changing chars to html code
// We may need to switch them around.
//$htmlcode=array("&amp;", "&quot;", "&lt;", "&gt;");$snglchar=array('&','"', '<', '>');

if (!$msg) { // No message so no error, lets start changing the title. 

	/////////////////////////////////////////////////
	// Change the banktitle.  
	/////////////////////////////////////////////////

	if (preg_match("/^banktitle (.+)?$/i", $message, $arr)) {
		$retitle = ($arr[1]);
		$retitle = str_replace("'", "\'", $retitle);
		$db->query("SELECT * FROM orgbank_<dim> WHERE `bankowner` = '$owner' ");
		if ($db->numrows() > 0) { 
			$retitle = trim($retitle);
			if ($retitle == "none"){
				$retitle = "Bank";
				$slot = "0";
				$msg .= "The title of your bank was cleared.<end>";
				$db->query("UPDATE orgbank_<dim> SET `banktitle` = \"$retitle\" WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
			} else {
				$slot = "0";
				$displayit = str_replace("\'", "'", $retitle);
				$msg .= "The title of your bank was changed to: <white>'$displayit'.<end>";
				$db->query("UPDATE orgbank_<dim> SET `banktitle` = \"$retitle\" WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
			}
		}
	}

}

/////////////////////////////////////////////////
// we have a message after all that? post it
/////////////////////////////////////////////////
$msg = str_replace("\'", "'", $msg);
if ($msg){	// Send info back
	$chatBot->send($msg, $sendto);
}	

?>