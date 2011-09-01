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
*
* $db->query("CREATE TABLE IF NOT EXISTS $table (`shopowner`VARCHAR(25), `shopslot` INTEGER, `timestamp` INTEGER, `lowID` INTEGER,`highID` INTEGER, `ql` INTEGER, `itemname` VARCHAR(100), `quantity` INTEGER, 'price' VARCHAR(5), `comment` VARCHAR(50) NULL, 'shoptitle' VARCHAR(50), 'shopmenu' VARCHAR(25), 'shoprelay' VARCHAR(25), 'shoptab' VARCHAR(6), 'shoptype' VARCHAR(8), 'tab1' VARCHAR(10), 'tab2' VARCHAR(10), 'tab3' VARCHAR(10), 'tab4' VARCHAR(10), 'tab5' VARCHAR(10) )");
*
*
*/


$table1 = "myshop_".$this->vars["dimension"];
$table2 = "orgbank_".$this->vars["dimension"];

$message = str_replace("'", "\'", $message);
// $owner = $sender; 
$slot = "0";
//$shoptab = "other";

if(preg_match("/^btrans/i", $message, $arr)){ 
	$db->query("SELECT * FROM $table1");
	$gotsome = $db->numrows();
	if($db->numrows() > 0) { 
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

		
		if($banktype == "superior"){
			$banktype = "tabbed";
		}
		$db->query("INSERT INTO '$table2' VALUES ( '$bankowner', '$bankslot', '$timestamp', '$lowID', '$highID', '$ql', \"$itemname\", '$quantity', '$comment', '$banktitle', '$bankmenu', '$banktab', '$banktype', '$tab1', '$tab2','$tab3', '$tab4', '$tab5' )");
		if($bankslot == "0"){
			$chatBot->send("Processing: $bankowner ($banktype)", $sendto);
		}
	} 
	
	
}

$msg = (" Transferred to Orgbank Table. \n");
/////////////////////////////////////////////////
// we have a message after all that? post it
/////////////////////////////////////////////////
$msg = str_replace("\'", "'", $msg);
if ($msg){	// Send info back
	$chatBot->send($msg, $sendto);
	
}

?>
