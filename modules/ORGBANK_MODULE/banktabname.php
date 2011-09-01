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
$table = "orgbank_".$this->vars["dimension"];
$owner = $sender; 
$slot = "0";
// Does the player have a bank?
$db->query("SELECT * FROM $table WHERE `bankowner` = '$owner' ");
if ($db->numrows() < 1) { 
	$msg .=("<green>You dont have a bank. Please type bank for help...<end>\n");//
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

if( eregi("^banktabname([1-5])? (.+)?$", $message, $arr)){
	// You can't change tabs on a Basic bank....
	if($banktype == "basic"){
		$msg .=("<green>You have a <yellow>Basic<green> Bank. No tabs to rename!\n");
		$chatBot->send($msg, $sendto);
		return;
	}
	$number = $arr[1];
	$newtabname = trim($arr[2]);
	if (strlen($newtabname) > 10){
		$msg .=("<green>More than 10 letters... think smaller!\n");
		$chatBot->send($msg, $sendto);
		return;
	}
	
	$newtabname =  str_replace("'", "&#039;", $newtabname);
	$newtabname = substr($newtabname,0,10);
	$db->query("SELECT * FROM $table WHERE `bankowner` = '$owner' ");
	if ($db->numrows() > 0) { 
		$Shop_Owner = 1;
		$newtabname = trim($newtabname);
		$slot = "0";
		$displaynewtabname = trim($arr[2]);
		$tabnumber ="tab$number";
		if($newtabname !== "open" && $newtabname !== "closed"&& $newtabname !== "basic"&& $newtabname !== "tabbed"){
			$setstr = "`$tabnumber` = '$newtabname'";
			$msg .= ("<green>Tab <yellow>$number<green> of your bank was changed to: <white>'$displaynewtabname'<green>.<end>");
			$db->query("UPDATE $table SET $setstr WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
			
		}ELSE{
			$msg .= ("<green>Sorry, <white>$newtabname<green> is a reserved word. Tab <yellow?$number<green> not set.\n");
		}
		
		
	}
}


/////////////////////////////////////////////////
// we have a message after all that? post it
/////////////////////////////////////////////////
$msg = str_replace("\'", "'", $msg);
if ($msg){	// Send info back
	if($type == "msg" || $type == "guild" || $type == "priv"){
		$chatBot->send($msg, $sendto);
	}
	
	
}


?>