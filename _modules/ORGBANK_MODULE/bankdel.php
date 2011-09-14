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

/////////////////////////////////////////////////
// * Deleting an item from the bank. 
/////////////////////////////////////////////////
$msg = "";
$slot = "1";
$db->query("SELECT * FROM orgbank_<dim> WHERE `bankowner` = '$sender' ");
if ($db->numrows() < 1) { 
	$msg .= "You dont have a bank. Please type bank for help...";
	$chatBot->send($msg, $sendto);
	return;
}

if (preg_match("/^bankdel ([0-9\\]+)(.+)$/i", $message, $arr)) {

	$ql_to_delete = trim($arr[1]);
	$item_to_delete = trim($arr[2]);
	$item_to_delete = str_replace("&#39;", "\'", $item_to_delete);
	$db->query("SELECT * FROM orgbank_<dim> WHERE bankowner = '$sender' AND itemname LIKE '%$item_to_delete%'AND ql = '$ql_to_delete'");
	$items_found = $db->numrows();
	if (!$items_found) {
		$msg.= "No items in your bank matched for QL: $ql_to_delete and $item_to_delete. Delete cancelled.\n";
	}
	if ($items_found = 1){  //We got some matches. 
		$data = $db->fobject("all");
		forEach ($data as $row) {
			$lowID = $row->lowID;
			$highid = $row->highID;
			$ql = $row->ql;
			$itemname = $row->itemname;
			$db->query("DELETE FROM orgbank_<dim> WHERE bankowner = '$sender' AND itemname = '$itemname' AND ql = '$ql'");
			$msg .= " $items_found match found: <a href='itemref://$row->lowID/$row->highID/$row->ql'>$row->itemname</a>";
			$msg .= " This item has been deleted from your bank. \n";
		}
	} else {
		$msg .= "I found $items_found matches for Ql: $ql_to_delete and $item_to_delete in your bank.\nPlease make it clearer what you want to remove. Delete cancelled.";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>