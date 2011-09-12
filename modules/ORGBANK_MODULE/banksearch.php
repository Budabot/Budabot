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


if (preg_match("/^banksearch (.+) (.+)$/i", $message, $arr) || preg_match("/^banksearch (.+)$/i", $message, $arr) ) {
	$person_to_find = ucfirst(trim($arr[1]));
	$command = trim($arr[2]);
	
	//So... lets look for a bank.
	$db->query("SELECT * FROM orgbank_<dim> WHERE `bankowner` = '$person_to_find' ");
	$items_found = $db->numrows();
	$bankowner = $person_to_find;
	//Make sure the bank isn't empty
	if ($items_found > 1) {
		$owner = $person_to_find;
		$uid = $chatBot->get_uid($owner);
		$name = ucfirst(strtolower($owner));
		//Lets get some status on our bankowner... 
		if (!$uid) {
			$msg = "Player <highlight>$owner<end> no longer exists!.";
		}ELSE{
			//if the player is a buddy then
			$online_status = Buddylist::is_online($name);
			if ($online_status === null) {
				Buddylist::add($name, 'is_online');
			}
		}
		// Get the current title, bank type and comment of the bank.
		// Also set up which tab we will open the bank with. If passed from itemsearch, 
		// this could be any of the 5 tabs. Default tab is 'tab1'. 		
		$slot = 0;
		$db->query("SELECT * FROM orgbank_<dim> WHERE `bankowner` = '$person_to_find'");
		$title_found =$db->numrows();
		if ($title_found > 0) {
			//Lets get all the info we're going to need from the bank placeholder
			$titlerow = $db->fObject();
			$banktitle = $titlerow->banktitle;
			$comment = $titlerow->comment;
			$banktype = $titlerow->banktype;
			$tab1 = $titlerow->tab1;
			$tab2 = $titlerow->tab2;
			$tab3 = $titlerow->tab3;
			$tab4 = $titlerow->tab4;
			$tab5 = $titlerow->tab5;
			$uppertab1 = ucfirst($tab1);
			$uppertab2 = ucfirst($tab2);
			$uppertab3 = ucfirst($tab3);
			$uppertab4 = ucfirst($tab4);
			$uppertab5 = ucfirst($tab5);
			$banktab=$titlerow->banktab;
			if (!$command) {
				$command = $banktab;
			}
			if ($banktype !== "basic") {					
				switch ($command) {
				case ($command == $tab1):
				case ($command == tab1):
					$banktab = "tab1";
					$banktab1 = "<red>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab1'>$uppertab1</a>]<end>";
					$banktab2 = "<white>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab2'>$uppertab2</a>]<end>";
					$banktab3 = "<white>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab3'>$uppertab3</a>]<end>";
					$banktab4 = "<white>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab4'>$uppertab4</a>]<end>";
					$banktab5 = "<white>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab5'>$uppertab5</a>]<end>";
					break;
				case ($command == $tab2):
				case ($command == tab2):	
					$banktab = "tab2";
					$banktab1 = "<white>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab1'>$uppertab1</a>]<end>";
					$banktab2 = "<red>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab2'>$uppertab2</a>]<end>";
					$banktab3 = "<white>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab3'>$uppertab3</a>]<end>";
					$banktab4 = "<white>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab4'>$uppertab4</a>]<end>";
					$banktab5 = "<white>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab5'>$uppertab5</a>]<end>";
					break;
				case ($command == $tab3):
				case ($command == tab3):
					$banktab = "tab3";
					$banktab1 = "<white>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab1'>$uppertab1</a>]<end>";
					$banktab2 = "<white>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab2'>$uppertab2</a>]<end>";
					$banktab3 = "<red>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab3'>$uppertab3</a>]<end>";
					$banktab4 = "<white>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab4'>$uppertab4</a>]<end>";
					$banktab5 = "<white>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab5'>$uppertab5</a>]<end>";
					break;
				case ($command == $tab4):
				case ($command == tab4):		
					$banktab = "tab4";
					$banktab1 = "<white>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab1'>$uppertab1</a>]<end>";
					$banktab2 = "<white>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab2'>$uppertab2</a>]<end>";
					$banktab3 = "<white>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab3'>$uppertab3</a>]<end>";
					$banktab4 = "<red>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab4'>$uppertab4</a>]<end>";
					$banktab5 = "<white>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab5'>$uppertab5</a>]<end>";
					break;
				case ($command == $tab5):
				case ($command == tab5):
					$banktab = "tab5";
					$banktab1 = "<white>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab1'>$uppertab1</a>]<end>";
					$banktab2 = "<white>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab2'>$uppertab2</a>]<end>";
					$banktab3 = "<white>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab3'>$uppertab3</a>]<end>";
					$banktab4 = "<white>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab4'>$uppertab4</a>]<end>";
					$banktab5 = "<red>[<a href='chatcmd:///tell <myname> <symbol>banksearch $bankowner tab5'>$uppertab5</a>]<end>";
					break;
					
				}
			}
		}
		// Check if a comment is set for the bank.
		$msg = ("<header><center>::::: Org Bank: Visitors Menu :::::</center><end>\n\n");
		$msg .= ("<green>Welcome to<end><white> $person_to_find 's <green>$banktitle<end>\n");
		if ($comment) {
			$msg .= "$person_to_find <green>comments: $comment<end>\n";
		}
		if ($online_status == 1) {
			$msg .= "$person_to_find is currently:<green> Online<end> \n";
		} else {
			$msg .= "$person_to_find is currently:<red> Offline<end> \n";
		}
		$msg .=("<white>______________________________________________<end>\n");
		// Decide if we print tabs or not. 
		if ($banktype !== "basic"){
			$msg .=("<white>Select:  ");
			$msg .= "$banktab1 ";
			$msg .= "$banktab2 ";
			$msg .= "$banktab3 ";
			$msg .= "$banktab4 ";
			$msg .= "$banktab5 ";
			$msg .=("\n<white>______________________________________________<end>\n");
		}
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
			if ($bankslot > 0) {
			
				if ($banktype == "basic"){
				$banktab = "none";
			}
				if($banktab == $itemtab){
					$msg .= "<a href='itemref://$row->lowID/$row->highID/$row->ql'>$row->itemname</a>\n<green>QL:<end><white> $row->ql<end><green> Quantity: <white>$row->quantity <green>Comment: <end><white>$row->comment\n\n";		
				}
			}
		}
		
		$msg = Text::make_link("Click to view $person_to_find's bank.", substr($msg, 0,strlen($msg)-1));
	} else if ($items_found < 1) {
		$msg = ("Couldn't find a match for $person_to_find. You sure thats a player?\n");  		
	} else {
		$msg = ("I'm sorry, but $person_to_find does not appear to have a bank or perhaps it has no items in it.\n");
	}
	
}

if ($msg) {
	$chatBot->send($msg, $sendto);
}

?>
