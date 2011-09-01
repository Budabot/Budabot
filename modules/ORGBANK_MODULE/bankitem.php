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


// Either client.exe, server, bot is changing chars to html code
// We may need to switch them around.
//$htmlcode=array("&amp;", "&quot;", "&lt;", "&gt;");$snglchar=array('&','"', '<', '>');

$message = str_replace("'", "\'", $message);
if (preg_match("/^bankitem (.+)$/i", $message, $arr) || preg_match("/^bankitem (.+)$/i", $message, $arr)) {
    $item_to_find = trim($arr[1]);
	if(!$item_to_find){
		$msg .= ("<grey> You need to tell me what to look for..."); 
		}
	
IF ($msg == ""){ // We got here, so lets search for an item.
	$db->query("SELECT * FROM $table WHERE itemname LIKE \"%$item_to_find%\" ");
	$items_found = $db->numrows();

	IF ($items_found > 0) {
		//We got some matches. 
		$msg = ("<header><center>::::: Org Bank: Search Menu :::::</center><end>\n\n");
		$msg .= ("<green>Search results for:<end><white>  $item_to_find <end>\n\n");
		$msg .= "<White>Click a players name to visit their Bank.\n";
		$msg .= "_________________________________________\n";

		$data = $db->fobject("all");
		forEach ($data as $row) {
			$bankowner = $row->bankowner;
			$bankslot = $row->bankslot;
			$lowID = $row->lowID;
			$highid = $row->highID;
			$ql = $row->ql;
			$itemname = $row->itemname;
			$comment = $row->comment;
			$quantity = $row->quantity;
			$banktab = $row->banktab;
			if(!$banktab){
				$banktab = "other";
			}
			if ($bankslot > 0) {
			$msg .= "<green>Owner:<end><white> <a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner $banktab>$bankowner</a> <end> <a href='itemref://$row->lowID/$row->highID/$row->ql'>$row->itemname</a>\n<white>QL:<end><green> $row->ql<end><white> Comment: <end><green>$row->comment\n\n";		
			
			}
		}
		$msg = Text::make_link("Click to view your search results.", substr($msg, 0,strlen($msg)-1));
	}ELSE{
		$msg = ("<green>I'm sorry, but <end><white>$item_to_find<end><green> was not matched.<end>\n");
		$msg = str_replace("/'", "'", $msg);

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
