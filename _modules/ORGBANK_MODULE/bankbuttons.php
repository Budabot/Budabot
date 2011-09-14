<?
/*
   ** Author: Elimeta of Team_Eli (RK2)
   ** Description: Org bank module
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


/////////////////////////////////////////////////
// * Using the bankbuttons. 
/////////////////////////////////////////////////
$msg = "";
$db->query("SELECT * FROM orgbank_<dim> WHERE `bankowner` = '$sender' ");
if ($db->numrows() < 1) { 
	$Shop_Owner = 0;
	$msg .=("You dont have a bank. Please type bank for help...\n");
}

if (!$msg) {  //we got here so lets add stuff 
	if (eregi("^bank[M,D,P] ([0-9]+)\/([0-9]+)\/([0-9]+)\/(.+)\/(.+)?$", $message, $arr)) {
		$command = substr($message,0,5);  //bankM, bankD, bankP
		$msg .= ("$command.\n");
		$lowID = $arr[1]; 
		$highID = $arr[2]; 
		$itemQL = $arr[3]; 
		$itemname = $arr[4]; 
		$comment = $arr[5];
		
		// replacing &#39; with \' cause client cant handle a window, with a chatcmd:, sending a tell with ' inside it :p
		$itemname = str_replace("&#39;", "\'", $itemname);
		$comment = str_replace("&#39;", "\'", $comment);
		
		//search if this item is in DB
		$db->query("SELECT * FROM orgbank_<dim> WHERE `lowID` = '$lowID' AND `highID` = '$highID' AND `ql` = '$itemQL' AND `bankowner` = '$sender'");
		
		if ($db->numrows() > 0) { // Found it
			$editrow = $db->fObject();
			$lowID = $editrow->lowID; 
			$highID = $editrow->highID; 
			$itemQL = $editrow->ql; 
			$originalquantity = $editrow->quantity;
			$quantity = $editrow->quantity; 
			$itemname = $editrow->itemname; 
			$comment = $editrow->comment;
			$itemname = str_replace("&#39;", "\'", $itemname);
			//$comment = str_replace("&#39;", "\'", $comment);
		
			if ($command == "bankM") {
				$quantity--;
			} 
			else if ($command == "bankP") {
				$quantity++;
			} 
			else if ($command == "bankD") {
				$quantity = 0;
			}
			$setstr = "`quantity` = '$quantity'";
			
			if ($quantity == 0) {
				$db->query("DELETE FROM orgbank_<dim> WHERE `lowID` = '$lowID' AND `highID` = '$highID' AND `ql` = '$itemQL' AND `bankowner` = '$sender'");
				$msg = "Removed: [QL $itemQL] <a href=\"itemref://$lowID/$highID/$itemQL\">$itemname</a> ";
			} else {
				$db->query("UPDATE orgbank_<dim> SET $setstr WHERE `lowID` = '$lowID' AND `highID` = '$highID' AND `ql` = '$itemQL' AND `comment` = '$comment' AND `bankowner` = '$sender'");
				$msg = "Altered: [QL $itemQL] <a href=\"itemref://$lowID/$highID/$itemQL\">$itemname</a> ";
			}
			if ($quantity > 1){
				$msg .="Quantity: (<highlight>".$originalquantity."<end>-><highlight>$quantity<end>) ";
				$msg .="\n<green>You need to type bank to refresh your bank now...<end>";
			
			}
			//if ($comment){
				//$msg .="$comment.<end>";
			//}
		}
		else {
			$msg = "Could not find your <a href=\"itemref://$lowID/$highID/$itemQL\">$itemname</a>";
			if ($comment) {
				$msg .="$comment<end>.";
			}
		}
	} else if( eregi("^bankadd[M,D,P](.+)?$", $message, $arr) ){
		$msg = "We adding/removing an item? Try 'bank' to see your bank, and pressing [+ 1] if you want to up the quantity, or [- 1] to lower quantity or [DELETE] to remove it completely.";
	}
}

if ($msg) {
	$chatBot->send($msg, $sendto);
}

?>