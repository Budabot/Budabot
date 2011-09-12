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


if (preg_match("/^banklist/i", $message, $arr)) {
    $name_to_find = $arr[1];
	$db->query("SELECT * FROM orgbank_<dim> WHERE bankowner LIKE '%' ");
	$owners_found = $db->numrows();
	if ($owners_found > 0) {
		//We got some matches. 
		$msg = ("<header><center>::::: Org Bank: Bank List Menu :::::</center><end>\n\n");
		$msg .= ("<green>Search results for:<end><white> ALL BANKS <end>  <a href='chatcmd:///tell <myname> <symbol>banklist>[Refresh Page]</a>\n\n");
		$msg .= "<White>Click a players name to vist their bank.\n";
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
			if ($bankslot == 0) {
				$db->query("SELECT * FROM orgbank_<dim> WHERE bankowner = '$row->bankowner' ");
				$items_count = ($db->numrows() - 1);
				$titlerow = $db->fObject();
				$banktitle = $titlerow->banktitle;	
				$msg .= "<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner>$bankowner 's</a><green> $banktitle\n(<white>$items_count<end><green> items in bank).<end>\n\n";		
			}
			
		}
		$msg = Text::make_link("Click to view your search results.", substr($msg, 0,strlen($msg)-1));
	}
	if ($owners_found == 0) {
		$msg = "I'm sorry, but <white>ALL BANKS<end> was not matched.";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
