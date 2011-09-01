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

$table = "orgbank_".$this->vars["dimension"];
$owner = $sender; 
$slot = "0";


$message = str_replace("'", "\'", $message);
// Either client.exe, server, bot is changing chars to html code
// We may need to switch them around.
//$htmlcode=array("&amp;", "&quot;", "&lt;", "&gt;");$snglchar=array('&','"', '<', '>');

/////////////////////////////////////////////////
// Toggle banktab between choices
/////////////////////////////////////////////////
if( eregi("^banktab (.+)?$", $message, $arr)){
	$owner = $sender; 
	$slot = "0";
	$command = trim("$arr[1]");
	//$db->query("SELECT * FROM $table WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
	$db->query("SELECT * FROM $table WHERE `bankowner` = '$owner' ");
	if ($db->numrows() < 1) { // No bank to find
		$msg .= "You need to create a bank first. Type bank for help. ";	
	}elseif($db->numrows() > 0) {// Found it
		$newtab = ucfirst($command);
		if ($command == "other") {
			$banktab = "other";
			$setstr = "`banktab` = '$banktab'";
			$db->query("UPDATE $table SET $setstr WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
			$msg .= "<green>Your bank will now open at the: <white>$newtab<end><green> tab.<end>\n";
		} 
		if ($command == "armour") {
			$banktab = "armour";
			$setstr = "`banktab` = '$banktab'";
			$db->query("UPDATE $table SET $setstr WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
			$msg .= "<green>Your bank will now open at the: <white>$newtab<end><green> tab.<end>\n";
		} 
		if ($command == "nano") {
			$banktab = "nano";
			$setstr = "`banktab` = '$banktab'";
			$db->query("UPDATE $table SET $setstr WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
			$msg .= "<green>Your bank will now open at the: <white>$newtab<end><green> tab.<end>\n";
		} 
		if ($command == "weapon") {
			$banktab = "weapon";
			$setstr = "`banktab` = '$banktab'";
			$db->query("UPDATE $table SET $setstr WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
			$msg .= "<green>Your bank will now open at the: <white>$newtab<end><green> tab.<end>\n";
		}
		if(!$msg){
			$msg = ("Sorry, what was that? You want how many Banana's?\n");
		}
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