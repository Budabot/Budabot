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
// Either client.exe, server, bot is changing chars to html code
// We may need to switch them around.
//$htmlcode=array("&amp;", "&quot;", "&lt;", "&gt;");$snglchar=array('&','"', '<', '>');

//Setup a few things
$table = "orgbank_".$this->vars["dimension"];
$message = str_replace("'", "\'", $message);
$msg = "";
$owner = $sender; 
$slot = 0;



/////////////////////////////////////////////////
// * Adding an item to the bank. 
/////////////////////////////////////////////////

// Do we have a bank in the first place? No? Then tell us so. 
$db->query("SELECT * FROM $table WHERE `bankowner` = '$owner' ");
if ($db->numrows() < 1) { 
	$msg .=("You don't have a bank. Please type bank for help...\n");
}
if ($db->numrows() > 0) { 
	$row = $db->fObject();
	// Okay, so we do, but what type is it? Basic or Tabbed?
	$banktype = $row->banktype;
}


if(eregi("^bankadd (.+)? <a href=\"itemref:\/\/([0-9]+)\/([0-9]+)\/([0-9]+)\">(.+)<\/a>(.+)?$", $message, $arr)|| eregi("^bankadd <a href=\"itemref:\/\/([0-9]+)\/([0-9]+)\/([0-9]+)\">(.+)<\/a>(.+)?$", $message, $arr)) {
	//Did we input a command for a tabbed shop?
	if(substr(trim($arr[1]),0,3) == "tab"){
		$tab = trim($arr[1]);
		switch ($tab) {
		case (tab1):
		case (tab2):
		case (tab3):
		case (tab4):
		case (tab5):
			$banktab = $tab;
			break;
			default :
			// Well it's a tab, but not 1-5.
			$msg = ("<green>Thats not a tab number I recognise, try <yellow>1<green>-<yellow>5<green>.\n");
			$chatBot->send($msg, $sender);
			return;
			break;
			// Logic fails me... they found a loophole obviously.
			$msg .=("I'm sorry, you have how many Banana's? Lets try again.\n");
			$chatBot->send($msg, $sender);
			return;			
		}
		// We got a tab we recognise... lets continue from here.
		// Okay - what type shop they got?
		if($banktype == "basic"){
			$msg =("<green>You have a <yellow>Basic<green> Bank. You can't use tabs.\n");
			$chatBot->send($msg, $sender);
			return;
		}
		// Hurrah... they gave us a tab we like, and it's a tabbed shop! go! GO!
		$lowID = $arr[2]; 
		$highID = $arr[3]; 
		$itemQL = $arr[4]; 
		$itemname = $arr[5]; 
		$comment = $arr[6];
		$owner = $sender;
		if(!$comment){
			$comment = "";
		}
		
	} ELSE IF (ctype_digit (trim($arr[1]))){ // Ok... not a tab... lowId of an item perhaps? All numbers?
		// Okay, they just used bankadd... what type shop they got? 
		if($banktype == "tabbed"){
			$msg =("<green>You have a <yellow>Tabbed<green> Bank. Please give me a <yellow>Tabnumber<green>.<end>\n");
			$chatBot->send($msg, $sender);
			return;
		}
		// Hey, it's not a tab command, and they have a basic shop! go! Go!
		$banktab = "none";
		$lowID = $arr[1]; 
		$highID = $arr[2]; 
		$itemQL = $arr[3]; 
		$itemname = $arr[4]; 
		$comment = $arr[5];
		$owner = $sender;
		if(!$comment){
			$comment = "";
		}
	} ELSE {
		// Does'nt look like anything we're interested in... reject it. 
		$msg .=("<green>That doesn't look right... go check the help.<end>\n");
		$chatBot->send($msg, $sender);
		return;
	}
	// Now we're through the logic minefield... lets input this puppy into the database.
	// With a few more sanity checks, of course! 
	
	// keep $comment length acceptable.
	$comment = substr(trim($comment),0,25);
	// change " to &quot;, cause query doesnt like " in itemname and comment
	$itemname = str_replace('"', "&quot;", $itemname); $comment = str_replace('"', "&quot;", $comment);
	// We want $quantity as a number
	if (!ctype_digit ($quantity)) {$quantity=1;} else { $quantity=$quantity+0;}
	
	// Now check if we have this item already
	$db->query("SELECT * FROM $table WHERE `lowID` = '$lowID' AND `highID` = '$highID' AND `ql` = '$itemQL' AND `itemname` = \"$itemname\" AND `bankowner` = '$owner'");
	if ($db->numrows() > 0) { 
		$row = $db->fObject();
		
		// Yes we do. Combine quantity with old and new.
		$quantity+=$row->quantity;
		
		// Updating Quantity of Entry
		$db->query("UPDATE $table SET `quantity` = '$quantity' WHERE `lowID` = '$lowID' AND `highID` = '$highID' AND `ql` = '$itemQL' AND `itemname` = \"$itemname\" AND `bankowner` = '$sender'");
		
		$msg = "Updated: [QL $itemQL] <a href=\"itemref://$lowID/$highID/$itemQL\">$itemname</a> ";
		if ($quantity > 1){$msg .="Quantity: (<highlight>".$row->quantity."<end>-><highlight>$quantity<end>) ";}
	}else {
		// We don't have those in the Bank - Adding it as a new entry
		$slot = 1;
		$db->query("INSERT INTO $table ('bankowner', 'bankslot',`lowID`, `highID`, `ql`, `itemname`, `quantity`, `comment`, 'banktab') VALUES ('$owner', '$slot','$lowID', '$highID', '$itemQL', \"$itemname\", '$quantity', \"$comment\", '$banktab')");     
		$msg = "Added: [QL $itemQL] <a href=\"itemref://$lowID/$highID/$itemQL\">$itemname</a> ";
		if ($quantity > 1){
			$msg .="Quantity: <highlight>$quantity<end> ";
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