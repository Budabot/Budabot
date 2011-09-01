<?
/*
** Author: Elimeta of Team_Eli (RK2)
** Description: Org Bank Module
** Version: 1.0
**
** Developed for: Budabot(http://sourceforge.net/projects/budabot)
**
** Many thanks to Lucier (RK1), without who's bank module, Org Bank 
** would not exist.
**
** Date(created): 04.06.2011
** Date(last modified): 04.06.2011
*/

$table = "orgbank_".$this->vars["dimension"];
$message = str_replace("'", "\'", $message);
$owner = $sender; 
$slot = "0";





if(preg_match("/^bank (.+)$/i", $message, $arr)){ 
	$command = trim($arr[1]);
	
	// SECTION START: MAKING BANKS
	if($command == "basic" || $command == "tabbed"){ // We're asking to make a bank from scratch. 
		$db->query("SELECT * FROM $table WHERE `bankowner` = '$owner' ");
		// Does this person already have a bank? 
		if ($db->numrows() > 0) { 
			$msg = ("You already have a bank. Don't be greedy!\n");
		}ELSE {
			// No, So make a default placeholder for the bank. 
			$owner = $sender; 
			$slot = 0;
			$timestamp = time();
			$lowid = 0;
			$highid = 0;
			$ql = 0 ;
			$itemname = 0;
			$quantity = 0;
			$comment = "";
			$banktitle = "Bank";
			$bankmenu = "closed";
			
			if($command == "basic"){
				$banktab = "none";
				$banktype = "basic";
				$tab1 = $tab2 = $tab3 = $tab4 = $tab5 = "none";
				$db->query("INSERT INTO '$table' VALUES ( '$owner', '$slot', '$timestamp', '$lowID', '$highID', '$ql', '$itemname', '$quantity', '$comment', '$banktitle', '$bankmenu', '$banktab', '$banktype', '$tab1', '$tab2', '$tab3', '$tab4', '$tab5' )");

			}

			if($command == "tabbed"){
				$banktab = "tab1";
				$banktype = "tabbed";
				$tab1 = "tab 1"; 
				$tab2 = "tab 2"; 
				$tab3 = "tab 3"; 
				$tab4 = "tab 4"; 
				$tab5 = "tab 5";
				$db->query("INSERT INTO '$table' VALUES ( '$owner', '$slot', '$timestamp', '$lowID', '$highID', '$ql', '$itemname', '$quantity', '$comment', '$banktitle', '$bankmenu', '$banktab', '$banktype', '$tab1', '$tab2', '$tab3', '$tab4', '$tab5' )");

			}	
		}
	}
}
//SECTION END 

//SECTION START: OPENING AND CLOSING THINGS
if ($command == "open") {
	$bankmenu = "open";
	// Save the new menu setting into the placeholder for this user.
	$setstr = "`bankmenu` = '$bankmenu'";
	$db->query("UPDATE $table SET $setstr WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
} 
if ($command == "closed") {
	$bankmenu = "closed";
	// Save the new menu setting into the placeholder for this user.
	$setstr = "`bankmenu` = '$bankmenu'";
	$db->query("UPDATE $table SET $setstr WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
}
//SECTION END

//SECTION START: GETTING COMMANDS TO SWITCH TABS FROM USERS AND CODE
// First check it's not a basic bank.. no switching allowed. 
$slot = "0";
$db->query("SELECT * FROM $table WHERE `bankowner` = '$owner' AND bankslot = '$slot'");
if ($db->numrows() > 0) { 
	$row = $db->fObject();
	$banktype = $row->banktype;
	$tab1 = $row->tab1;
	$tab2 = $row->tab2;
	$tab3 = $row->tab3;
	$tab4 = $row->tab4;
	$tab5 = $row->tab5;
	$banktab=$row->banktab;
	if(!$command){
		$command = $banktab;
	}
	if($banktype !== "basic"){		
		switch ($command) {
		case ($command == $tab1):
		case ($command == tab1):
			$banktab = "tab1";
			$setstr = "`banktab` = '$banktab'";
			$db->query("UPDATE $table SET $setstr WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
			break;
		case ($command == $tab2):
		case ($command == tab2):	
			$banktab = "tab2";
			$setstr = "`banktab` = '$banktab'";
			$db->query("UPDATE $table SET $setstr WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
			break;
		case ($command == $tab3):
		case ($command == tab3):
			$banktab = "tab3";
			$setstr = "`banktab` = '$banktab'";
			$db->query("UPDATE $table SET $setstr WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
			break;
		case ($command == $tab4):
		case ($command == tab4):		
			$banktab = "tab4";
			$setstr = "`banktab` = '$banktab'";
			$db->query("UPDATE $table SET $setstr WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
			break;
		case ($command == $tab5):
		case ($command == tab5):
			$banktab = "tab5";
			$setstr = "`banktab` = '$banktab'";
			$db->query("UPDATE $table SET $setstr WHERE `bankslot` = '$slot' AND `bankowner` = '$sender'");
			break;
		}
	}
}
//SECTION END

//SECTION START: SET UP THE TABS PRIOR TO DISPLAY. 
$slot = "0";
$db->query("SELECT * FROM $table WHERE `bankowner` = '$owner'");
if ($db->numrows() > 0) { 
	$tabrow = $db->fObject();
	$banktitle = $tabrow->banktitle;
	$bankmenu = $tabrow->bankmenu;
	$bankcomment = $tabrow->bankcomment;
	$banktype = $tabrow->banktype;
	$tab1 = $tabrow->tab1;
	$tab2 = $tabrow->tab2;
	$tab3 = $tabrow->tab3;
	$tab4 = $tabrow->tab4;
	$tab5 = $tabrow->tab5;
	$banktab = $tabrow->banktab;
	$uppertab1 = ucfirst($tab1);
	$uppertab2 = ucfirst($tab2);
	$uppertab3 = ucfirst($tab3);
	$uppertab4 = ucfirst($tab4);
	$uppertab5 = ucfirst($tab5);
	$msg .= $banktab;
	switch ($banktab) {
	case "tab1":
		$banktab1 = "<red>[<a href='chatcmd:///tell <myname> <symbol>bank tab1'>$uppertab1</a>]<end>";
		$banktab2 = "<white>[<a href='chatcmd:///tell <myname> <symbol>bank tab2'>$uppertab2</a>]<end>";
		$banktab3 = "<white>[<a href='chatcmd:///tell <myname> <symbol>bank tab3'>$uppertab3</a>]<end>";
		$banktab4 = "<white>[<a href='chatcmd:///tell <myname> <symbol>bank tab4'>$uppertab4</a>]<end>";
		$banktab5 = "<white>[<a href='chatcmd:///tell <myname> <symbol>bank tab5'>$uppertab5</a>]<end>";
		break;
	case "tab2":
		$banktab1 = "<white>[<a href='chatcmd:///tell <myname> <symbol>bank tab1'>$uppertab1</a>]<end>";
		$banktab2 = "<red>[<a href='chatcmd:///tell <myname> <symbol>bank tab2'>$uppertab2</a>]<end>";
		$banktab3 = "<white>[<a href='chatcmd:///tell <myname> <symbol>bank tab3'>$uppertab3</a>]<end>";
		$banktab4 = "<white>[<a href='chatcmd:///tell <myname> <symbol>bank tab4'>$uppertab4</a>]<end>";
		$banktab5 = "<white>[<a href='chatcmd:///tell <myname> <symbol>bank tab5'>$uppertab5</a>]<end>";
		break;
	case "tab3":
		$banktab1 = "<white>[<a href='chatcmd:///tell <myname> <symbol>bank tab1'>$uppertab1</a>]<end>";
		$banktab2 = "<white>[<a href='chatcmd:///tell <myname> <symbol>bank tab2'>$uppertab2</a>]<end>";
		$banktab3 = "<red>[<a href='chatcmd:///tell <myname> <symbol>bank tab3'>$uppertab3</a>]<end>";
		$banktab4 = "<white>[<a href='chatcmd:///tell <myname> <symbol>bank tab4'>$uppertab4</a>]<end>";
		$banktab5 = "<white>[<a href='chatcmd:///tell <myname> <symbol>bank tab5'>$uppertab5</a>]<end>";
		break;
	case "tab4":
		$banktab1 = "<white>[<a href='chatcmd:///tell <myname> <symbol>bank tab1'>$uppertab1</a>]<end>";
		$banktab2 = "<white>[<a href='chatcmd:///tell <myname> <symbol>bank tab2'>$uppertab2</a>]<end>";
		$banktab3 = "<white>[<a href='chatcmd:///tell <myname> <symbol>bank tab3'>$uppertab3</a>]<end>";
		$banktab4 = "<red>[<a href='chatcmd:///tell <myname> <symbol>bank tab4'>$uppertab4</a>]<end>";
		$banktab5 = "<white>[<a href='chatcmd:///tell <myname> <symbol>bank tab5'>$uppertab5</a>]<end>";
		break;
	case "tab5":
		$banktab1 = "<white>[<a href='chatcmd:///tell <myname> <symbol>bank tab1'>$uppertab1</a>]<end>";
		$banktab2 = "<white>[<a href='chatcmd:///tell <myname> <symbol>bank tab2'>$uppertab2</a>]<end>";
		$banktab3 = "<white>[<a href='chatcmd:///tell <myname> <symbol>bank tab3'>$uppertab3</a>]<end>";
		$banktab4 = "<white>[<a href='chatcmd:///tell <myname> <symbol>bank tab4'>$uppertab4</a>]<end>";
		$banktab5 = "<red>[<a href='chatcmd:///tell <myname> <symbol>bank tab5'>$uppertab5</a>]<end>";
		break;
	}
	
}
//SECTION END

//SECTION START: DISPLAYING THINGS
//The banks header. 
$msg = ("<header><center>::::: Org Bank: Home Menu :::::</center><end>\n");
$slot = 0;
$db->query("SELECT * FROM $table WHERE `bankowner` = '$owner'");
$title_found =$db->numrows();
// If user has no bank, put up a dialog to let him choose and make one. 
if ($title_found == 0){
	$msg .= ("\n<green><u>There are 2 types of Bank available:</u><end>\n\n");
	$msg .= ("<yellow>Basic<green> Bank, Everything in 1 big list[<a href='chatcmd:///tell <myname> <symbol>bank basic>Click Here</a>]\n\n");
	$msg .= ("<yellow> Tabbed <green>Bank, Things are sorted by Tabs. [<a href='chatcmd:///tell <myname> <symbol>bank tabbed>Click Here</a>] \n");
	$msg .= ("<green>(You name your tabs after making your bank).<end>\n\n");
	$msg .= ("<green>I made a mistake, <red>delete<green> the bank I made. [<a href='chatcmd:///tell <myname> <symbol>bankkill>Delete Bank</a>] \n\n\n");
}
// If user has a bank, start getting details about it. 
if ($title_found > 0){				
	$row = $db->fObject();
	$banktitle = $row->banktitle;
	$bankcomment = $row->comment;
	$bankmenu = trim($row->bankmenu);
	$banktype = $row->banktype;
	
	// Start printing the Settings page if it is open. Otherwise, print an open menu button. 
	if (!$bankmenu || $bankmenu == "open"){
		$msg .="<white>To hide your settings: [<a href='chatcmd:///tell <myname> <symbol>bank closed >Close Settings</a>]\n";
		$msg .=("<white>______________________________________________<end>\n");
		$msg .= "<green>Delete your bank and all items in it<end> [<a href='chatcmd:///tell <myname> <symbol>bankkill>Delete Bank</a>]\n";
		$msg .=("<white>______________________________________________<end>\n");
		if($banktype == "tabbed"){
			$msg .= "<green>Changing the tab names of your bank<end> [<a href='chatcmd:///tell <myname> <symbol>help banktabname>Help Me</a>]\n";
		}
		if($banktype == "basic"){
			$msg .= "<green>How to add an item to your bank<end> [<a href='chatcmd:///tell <myname> <symbol>help bankadd>Help Me</a>]\n";
		} ELSE {
			$msg .= "<green>How to add an item to your bank<end> [<a href='chatcmd:///tell <myname> <symbol>help bankaddtab>Help Me</a>]\n";
		}
		$msg .= "<green>How to delete an item from your bank<end> [<a href='chatcmd:///tell <myname> <symbol>help bankdel>Help Me</a>]\n";
		$msg .= "<green>How to search for an item in banks<end> [<a href='chatcmd:///tell <myname> <symbol>help bankitem>Help Me</a>]\n";
		$msg .= "<green>List all banks in the Organisation.<end> [<a href='chatcmd:///tell <myname> <symbol>help banklist>Help Me</a>]\n";
		$msg .= "<green>Go directly to a Bank<end> [<a href='chatcmd:///tell <myname> <symbol>help banksearch>Help Me</a>]\n";
		$msg .= "<green>List all Bank commands<end> [<a href='chatcmd:///tell <myname> <symbol>help bankcommands>Help Me</a>]\n";
		$msg .= "<green>Your bank's title is currently: <grey> $banktitle [<a href='chatcmd:///tell <myname> <symbol>help banktitle>Change It</a>]\n";
		$msg .= "<green>Your bank's comment is currently: <grey> $bankcomment [<a href='chatcmd:///tell <myname> <symbol>help bankcomment>Change It</a>]\n";
		
	}ELSEIF($bankmenu = "closed"){
		$msg .="<white>To show your settings: [<a href='chatcmd:///tell <myname> <symbol>bank open >Open Menu</a>] \n";
	}
	
	// Check if there are items in the bank, prompt user to start adding items. 
	// Start printing tabs if it's a tabbed bank. 
	$slot = "0";
	$db->query("SELECT * FROM $table WHERE `bankowner` = '$owner'");
	$items_found = $db->numrows();
	if ($items_found == 1) {	// 1 item means they have a bank, but no goods in it... prompt them to add 
		$msg .= "\n<White>Now you can add items ";
		$msg .= " and then [<a href='chatcmd:///tell <myname> <symbol>bank>Refresh Bank</a>]\n";
		$msg .=("<white>______________________________________________<end>\n");
		if($banktype !== "basic"){
			$msg .=("<white>Select:  ");
			$msg .= "$banktab1 ";
			$msg .= "$banktab2 ";
			$msg .= "$banktab3 ";
			$msg .= "$banktab4 ";
			$msg .= "$banktab5 ";
			$msg .=("\n<white>______________________________________________<end>\n");
		}
	}
	// Start figuring out which items are in this tab. 
	$db->query("SELECT * FROM $table WHERE `bankowner` = '$owner' ");
	$items_found = $db->numrows();
	// Okay, the bank has more than just a placeholder.
	if ($items_found > 1) {
		$msg .=("<white>______________________________________________<end>\n");
		// Decide if we print tabs or not. 
		if($banktype !== "basic"){
			$msg .=("<white>Select:  ");
			$msg .= "$banktab1 ";
			$msg .= "$banktab2 ";
			$msg .= "$banktab3 ";
			$msg .= "$banktab4 ";
			$msg .= "$banktab5 ";
			$msg .=("\n<white>______________________________________________<end>\n");
		}
		// Get the items. 
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
			$itemtab = $row->banktab;
			//$banktab = $row->banktab;
			// Slot 0 = Bank placeholder. Slot 1 = An item within a Bank. Reserved for future uses. 
			if ($bankslot > 0) {
				// Is it a tabbed bank? if not default it to the first page. 
				if ($banktype == "basic"){
					$banktab = "none";
				}
				if($banktab == $itemtab){
					
					// Put up the item and the -+DEL Buttons. 
					$thisitem = "<a href='itemref://$row->lowID/$row->highID/$row->ql'>$row->itemname</a>";
					$edititem =  str_replace("\'", "&#39;", "$row->lowID/$row->highID/$row->ql/$row->itemname/");
					$msg .= "$thisitem <green>";
					$msg .= "[<a href='chatcmd:///tell <myname> <symbol>bankM $edititem'>- 1</a>|";
					$msg .= "<a href='chatcmd:///tell <myname> <symbol>bankD $edititem'>DELETE</a>";
					$msg .= "|<a href='chatcmd:///tell <myname> <symbol>bankP $edititem'>+ 1</a>]\n";
					$msg .= "<green>QL: <white>$ql ";
					$msg .= " <green>Quantity: <white>$row->quantity";
					$msg .= " <green>Comment: <white>$comment";
					$msg .= "\n\n";
				}
			}
		}
	}
	
	
}

$msg = Text::make_link("Please click to start or refresh your Bank.", substr($msg, 0,strlen($msg)-1));
/////////////////////////////////////////////////
// we have a message after all that? post it
/////////////////////////////////////////////////
$msg = str_replace("\'", "'", $msg);
if ($msg){	// Send info back
	$chatBot->send($msg, $sender);
	
}

?>
