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
$table = "orgbank_".$this->vars["dimension"];
$owner = $sender; 
$slot = "0";
$db->query("SELECT * FROM $table WHERE `bankowner` = '$owner' ");
if ($db->numrows() < 1) { 
$Shop_Owner = 0;
$msg .=("You don't have a bank. Please type !bank for help...\n");//
}



$message = str_replace("'", "\'", $message);
// Either client.exe, server, bot is changing chars to html code
// We may need to switch them around.
//$htmlcode=array("&amp;", "&quot;", "&lt;", "&gt;");$snglchar=array('&','"', '<', '>');

if(!$msg){ // No message so no error, lets start changing the title. 

	/////////////////////////////////////////////////
	// Change the banktitle.  
	/////////////////////////////////////////////////

	if( eregi("^bankcomment (.+)?$", $message, $arr)){
		$newcomment = ($arr[1]);
		$newcomment = str_replace("'", "\'", $newcomment);
		$db->query("SELECT * FROM $table WHERE `bankowner` = '$owner' ");
		if ($db->numrows() > 0) { 
			$Shop_Owner = 1;
			$newcomment = trim($newcomment);
			if ($newcomment == "none"){
				$newcomment = "";
				$slot = "0";
				$msg .= "The comment for your bank was cleared.<end>";
				$db->query("UPDATE $table SET `comment` = \"$newcomment\" WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
			}ELSE{
				$slot = "0";
				$displaycomment = str_replace("\'", "'", $newcomment);
				$msg .= "The comment for your bank was changed to: <white>'$displaycomment'.<end>";
				$db->query("UPDATE $table SET `comment` = \"$newcomment\" WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
			}
			
			
			
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
	if($type == "guild"){
		$chatBot->send($msg, $sender);
	}
	
}	



?>