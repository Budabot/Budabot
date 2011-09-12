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

// Set up some basic stuff.
$owner = $sender; 
$slot = "0";
// Does the player have a bank?
$db->query("SELECT * FROM orgbank_<dim> WHERE `bankowner` = '$owner' ");
if ($db->numrows() < 1) { 
	$msg .= "You dont have a bank. Please type bank for help...";
	$chatBot->send($msg, $sendto);
	return;
}
if ($db->numrows() > 0) { 
	$row = $db->fObject();
	// Okay, so we do, but what type is it? Basic or Tabbed?
	$banktype = $row->banktype;
}


/////////////////////////////////////////////////
// Change tab names.  
/////////////////////////////////////////////////

if (preg_match("/^banktabname([1-5])? (.+)?$/i", $message, $arr)) {
	// You can't change tabs on a Basic bank....
	if ($banktype == "basic") {
		$msg .= "You have a <highlight>Basic<end> Bank. No tabs to rename!";
		$chatBot->send($msg, $sendto);
		return;
	}
	$number = $arr[1];
	$newtabname = trim($arr[2]);
	if (strlen($newtabname) > 10){
		$msg .= "More than 10 letters... think smaller!");
		$chatBot->send($msg, $sendto);
		return;
	}
	
	$newtabname =  str_replace("'", "''", $newtabname);
	$db->query("SELECT * FROM orgbank_<dim> WHERE `bankowner` = '$owner' ");
	if ($db->numrows() > 0) { 
		$slot = "0";
		$displaynewtabname = trim($arr[2]);
		if ($newtabname !== "open" && $newtabname !== "closed" && $newtabname !== "basic" && $newtabname !== "tabbed") {
			$msg .= ("<green>Tab <yellow>$number<green> of your bank was changed to: <white>'$displaynewtabname'<green>.<end>");
			$db->query("UPDATE orgbank_<dim> SET `tab$number` = '$newtabname' WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
		} else {
			$msg .= "<green>Sorry, <highlight>$newtabname<end> is a reserved word. Tab <highlight>$number<end> not set.<end>\n";
		}
	}
}

if ($msg) {
	$chatBot->send($msg, $sendto);
}


?>