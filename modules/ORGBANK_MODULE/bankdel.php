<?
      /*
   ** Author: Elimeta of Team_Eli (RK2)
   ** Description: Orgbank
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Many thanks to Lucier (RK1), without who's bank module, Organk 
   ** would not exist.
   **
   ** Date(created): 27.04.2011
   ** Date(last modified): 20.04.2011
   */

$table = "orgbank_".$this->vars["dimension"];

$message = str_replace("'", "\'", $message);
// Either client.exe, server, bot is changing chars to html code
// We may need to switch them around.
//$htmlcode=array("&amp;", "&quot;", "&lt;", "&gt;");$snglchar=array('&','"', '<', '>');

/////////////////////////////////////////////////
// * Deleting an item from the bank. 
/////////////////////////////////////////////////
$msg = "";
$owner = $sender; 
$slot = "1";
$db->query("SELECT * FROM $table WHERE `bankowner` = '$owner' ");
if ($db->numrows() < 1) { 
$Shop_Owner = 0;
$msg .=("You dont have a bank. Please type bank for help...\n");
}

if (!$msg){
	if( eregi("^bankdel ([0-9\\]+)(.+)$", $message, $arr)){

    	$ql_to_delete = trim($arr[1]);
		$item_to_delete = trim($arr[2]);
		$item_to_delete = str_replace("&#39;", "\'", $item_to_delete);
		// $comment = str_replace("&#39;", "\'", $comment);
		$db->query("SELECT * FROM $table WHERE bankowner = '$owner' AND itemname LIKE '%$item_to_delete%'AND ql = '$ql_to_delete'");
		$items_found = $db->numrows();
		if(!$items_found){
				$msg.=("No items in your bank matched for QL: $ql_to_delete and $item_to_delete. Delete cancelled.\n");
			}
		if ($items_found = 1){  //We got some matches. 
			$data = $db->fobject("all");
			forEach ($data as $row) {
			$lowID = $row->lowID;
			$highid = $row->highID;
			$ql = $row->ql;
			$itemname = $row->itemname;
			$db->query("DELETE FROM $table WHERE bankowner = '$owner' AND itemname = '$itemname' AND ql = '$ql'");
			$msg .= (" $items_found match found: <a href='itemref://$row->lowID/$row->highID/$row->ql'>$row->itemname</a>");
			$msg .=(" This item has been deleted from your bank. \n");
			}
		}ELSE{
			$msg .=("I found $items_found matches for Ql: $ql_to_delete and $item_to_delete in your bank.\nPlease make it clearer what you want to remove. Delete cancelled.\n");
			}
}
}

/////////////////////////////////////////////////
// we have a message after all that? post it
/////////////////////////////////////////////////
$msg = str_replace("\'", "'", $msg);
	if ($msg){	// Send info back
		
		$chatBot->send($msg, $sender);

		
	}
	

?>