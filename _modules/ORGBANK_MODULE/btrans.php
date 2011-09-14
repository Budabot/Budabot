<?
/*
** Author: Elimeta of Team_Eli (RK2)
** Description: Btrans - Transfer table from Ushop to Orgbank.
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


$message = str_replace("'", "\'", $message);
// $owner = $sender; 
$slot = "0";
//$shoptab = "other";

if (preg_match("/^btrans/i", $message, $arr)) { 
	$db->query("SELECT * FROM myshop_<dim>");
	$gotsome = $db->numrows();
	if ($db->numrows() > 0) { 
		$chatBot->send("$gotsome records found", $sendto);
	}
	$data = $db->fobject("all");
	forEach ($data as $row) {
		$bankowner = $row->shopowner;
		$bankslot = $row->shopslot;
		$lowID = $row->lowID;
		$highID = $row->highID;
		$ql = $row->ql;
		$itemname = $row->itemname;
		//$itemname = str_replace('"', "&quot;", $itemname);
		//$itemname = str_replace("'", "\'", $itemname);
		//$itemname = str_replace("\'", "&39", $itemname);
		$quantity = $row->quantity;			
		$comment = $row->comment;
		$banktitle = $row->shoptitle;
		$bankmenu = $row->shopmenu; 	
		$banktab = $row->shoptab; 	
		$banktype = $row->shoptype; 	
		$tab1 = $row->tab1; 	
		$tab2 = $row->tab2; 	
		$tab3 = $row->tab3; 	
		$tab4 = $row->tab4; 	
		$tab5 = $row->tab5; 

		
		if ($banktype == "superior") {
			$banktype = "tabbed";
		}
		$db->query("INSERT INTO orgbank_<dim> VALUES ( '$bankowner', '$bankslot', '$timestamp', '$lowID', '$highID', '$ql', \"$itemname\", '$quantity', '$comment', '$banktitle', '$bankmenu', '$banktab', '$banktype', '$tab1', '$tab2','$tab3', '$tab4', '$tab5' )");
		if ($bankslot == "0") {
			$chatBot->send("Processing: $bankowner ($banktype)", $sendto);
		}
	} 
	
	
}

$msg = " Transferred to Orgbank Table. \n";
$msg = str_replace("\'", "'", $msg);
$chatBot->send($msg, $sendto);

?>
