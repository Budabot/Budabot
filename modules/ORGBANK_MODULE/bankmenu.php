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

$table = "orgbank_".$this->vars["dimension"];
$owner = $sender; 
$slot = "0";


$message = str_replace("'", "\'", $message);
// Either client.exe, server, bot is changing chars to html code
// We may need to switch them around.
//$htmlcode=array("&amp;", "&quot;", "&lt;", "&gt;");$snglchar=array('&','"', '<', '>');

/////////////////////////////////////////////////
// Toggle shopmenu open & closed 
/////////////////////////////////////////////////
if( eregi("^bankmenu (.+)?$", $message, $arr)){
	$owner = $sender; 
	$slot = "0";
	$command = trim("$arr[1]");
	//$db->query("SELECT * FROM $table WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
	$db->query("SELECT * FROM $table WHERE `bankowner` = '$owner' ");
	if ($db->numrows() < 1) { // No Shop to find
		$msg .= "You need to create a shop first. Type bank for help. ";	
	}elseif($db->numrows() > 0) {// Found it
		
		if ($command == "open") {
		$bankmenu = "open";
		$setstr = "`bankmenu` = '$bankmenu'";
		$db->query("UPDATE $table SET $setstr WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
		$msg .= "<green>Your bankmenu is now: <white>$bankmenu<end><green>.<end>";
		} 
		if ($command == "closed") {
			$bankmenu = "closed";
			$setstr = "`bankmenu` = '$bankmenu'";
		$db->query("UPDATE $table SET $setstr WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
		$msg .= "<green>Your bankmenu is now: <white>$bankmenu<end><green> .<end>";
		} 
	}
	

}
/////////////////////////////////////////////////
// we have a message after all that? post it
/////////////////////////////////////////////////
$msg = str_replace("\'", "'", $msg);
if ($msg){	// Send info back
	if($type == "msg"){
		$chatBot->send($msg, $sendto);
	}
	
	
}


?>