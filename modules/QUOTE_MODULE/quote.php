<?php
   /*
   ** Author: Lucier (RK1)
   ** Description: Read/Write/Search Quotes
   ** Version: 1.3
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 12.03.2007
   ** Date(last modified): 14.06.2007
   */

// Adding a quote
if (preg_match("/^quote add (.+)$/si", $message, $arr)) {
	$arr[1] = trim($arr[1]);
	$db->query("SELECT * FROM `#__quote` WHERE `What` LIKE '".str_replace("'", "''", $arr[1])."'");
	if ($db->numrows() > 0) {
		$row = $db->fObject();
		$msg = "This quote is already in as quote <highlight>$row->IDNumber<end>.";
	} else {
		if (strlen($arr[1]) <= 1000) {
	
			$quoteDATE = date("F j, Y, g:i a");
			$quoteMSG = $arr[1];
			$quoteWHO = $sender;
	
			// Search for highest ID and +1 for new ID.
			$db->query("SELECT * FROM `#__quote` ORDER BY `IDNumber` DESC");
			$row = $db->fObject();

			if ($row->IDNumber == "") {
				$quoteID = 0;
			} else {
				$quoteID = $row->IDNumber+1;	
			}
			
			//Trying to determine who is being quoted.
			$findcolon = strpos($quoteMSG,":");
			$findbracket = strpos($quoteMSG,"] ")+2;
			if ($findcolon > 0) {
				if (substr($quoteMSG,0,4) == "To [") {
					//To [Person]: message
					$quoteOfWHO = $sender;
				} else if ((substr($quoteMSG,$findcolon-1,1) == "]") && (substr($quoteMSG,0,1) == "[")) {
					//[Person]: message.
					$quoteOfWHO = substr($quoteMSG,1,$findcolon-2);
				} else if (($findbracket > 2) && ($findbracket < $findcolon)) {	
					//[Neu. OOC] Lucier: message.
					$quoteOfWHO = substr($quoteMSG,$findbracket,$findcolon-$findbracket);
				} else if (substr($quoteMSG,$findcolon-7,7) == " shouts") {
					//Lucier shouts: message
					$quoteOfWHO = substr($quoteMSG,0,$findcolon-7);
				} else if (substr($quoteMSG,$findcolon-9,9) == " whispers") {
					//Lucier whispers: message
					$quoteOfWHO = substr($quoteMSG,0,$findcolon-9);
				} else {
					//Lucier: message
					$quoteOfWHO = substr($quoteMSG,0,$findcolon);
				}

				// TODO escape single quotes
			} else {
				//without a colon.. quoting him/her/itself?
				$quoteOfWHO = $sender;
			}
			$db->exec("INSERT INTO `#__quote` (`IDNumber`, `Who`, `OfWho`, `When`, `What`) VALUES ($quoteID, '$quoteWHO', '$quoteOfWHO', '$quoteDATE', '".str_replace("'", "''", $quoteMSG)."')");
			$msg = "Quote <highlight>$quoteID<end> has been added.";
		} else {
			$msg = "This quote is too big.";
		}
	}
	$chatBot->send($msg, $sendto);
// Removing a quote
} else if (preg_match("/^quote (rem|del|remove|delete) (\\d+)$/i", $message, $arr)) {

	$db->query("SELECT * FROM `#__quote` WHERE `IDNumber` = '$arr[2]'");

	if ($db->numrows() > 0) {
		$row = $db->fObject();
		$quoteID = $arr[2];
		$quoteWHO = $row->Who;
		$quoteOfWHO = $row->OfWho;
		$quoteDATE = $row->When;
		$quoteMSG = $row->What;

		//only author or admin can delete.
		if (($quoteWHO == $sender) || AccessLevel::check_access($sender, 'admin')) {
			$db->exec("DELETE FROM `#__quote` WHERE `IDNumber` = $quoteID");
			$msg = "This quote has been deleted.";
		} else {
			$msg = "Only an admin or $quoteWHO can delete this quote.";
		}
	} else {
		$msg = "Could not find this quote.  Already deleted?";
	}
	$chatBot->send($msg, $sendto);
//Searching for authors or victims.
} else if (preg_match("/^quote search (.+)$/i", $message, $arr)) {
	
	$search = ucfirst(strtolower($arr[1]));
	
	// Search for poster:
	$list = "";
	$db->query("SELECT * FROM `#__quote` WHERE `Who` LIKE '".str_replace("'", "''", $search)."'");
	while ($row = $db->fObject()) {
		$list .= "<a href='chatcmd:///tell <myname> quote $row->IDNumber'>$row->IDNumber</a>, ";
	}
	if ($list) {
		$msg .= "<tab>Quotes posted by <highlight>$search<end>: ";
		$msg .= substr($list,0,strlen($list)-2);
	}	
	
	// Search for victim:
	$list = "";
	$db->query("SELECT * FROM `#__quote` WHERE `OfWho` LIKE '".str_replace("'", "''", $search)."'");
	while ($row = $db->fObject()) {
		$list .= "<a href='chatcmd:///tell <myname> quote $row->IDNumber'>$row->IDNumber</a>, ";
	}
	if ($list) {
		if ($msg) {
			$msg .="\n\n";
		}
		$msg .= "<tab>Quotes <highlight>$search<end> said: ";
		$msg .= substr($list,0,strlen($list)-2);
	}

	// Search inside quotes:
	$list = "";
	$db->query("SELECT * FROM `#__quote` WHERE `OfWho` NOT LIKE '$search' AND `What` LIKE '%".str_replace("'", "''", $search)."%'");
	while ($row = $db->fObject()) {
		$list .= "<a href='chatcmd:///tell <myname> quote $row->IDNumber'>$row->IDNumber</a>, ";
	}
	if ($list) {
		if ($msg) {
			$msg .="\n\n";
		}
		$msg .= "<tab>Quotes that contain '<highlight>$search<end>': ";
		$msg .= substr($list,0,strlen($list)-2);
	}
	
	if ($msg) {
		$msg = Text::make_blob("Results for: '$search'", "<header> :::::: Quote Info :::::: <end>\n\n$msg");
	} else {
		$msg = "Couldn't find any matches for this search.";
	}
	$chatBot->send($msg, $sendto);
//Show the top quoters/quoted
} else if (preg_match("/^quote stats$/i", $message, $arr)) {
	$top = Setting::get("quote_stat_count");

	$db->query("SELECT * FROM `#__quote`");
	$count = $db->numrows();

	//$quoters = setup a list of who quoted the most
	$db->query("SELECT * FROM `#__quote` ORDER BY `Who`");
	$data = $db->fObject('all');
	$quoters = array();
	forEach ($data as $row) {
		if ($row->Who != "") {
			$quoters[$row->Who]++;
		}
	}
	arsort($quoters);

	//$victims = setup a list of who was quoted the most
	$db->query("SELECT * FROM `#__quote` ORDER BY `OfWho`");
	$data = $db->fObject('all');
	$victims = array();
	forEach ($data as $row) {
		if ($row->Who != "") {
			$victims[$row->OfWho]++;
		}
	}
	arsort($victims);
	
	$blob = "<header> :::::: Quote Stats :::::: <end>\n\n";
	$blob .= "<highlight>Top $top Quoters:<end> (".count($quoters)." total)\n";
	$listnum = 0;
	forEach ($quoters as $key => $val) {
		$listnum++;
		$blob .= "<tab>$listnum) ".
			"<a href='chatcmd:///tell ".$chatBot->vars["name"]." quote search $key>$key</a>".
			": <highlight>$val<end> ".number_format((100*($val/$count)),0)."%\n";
		if ($listnum >= $top) {
			break;
		}
	}

	$blob .= "\n<highlight>Top $top Quoted:<end> (".count($victims)." total)\n";
	$listnum = 0;
	forEach ($victims as $key => $val) {
		$listnum++;
		$blob .= "<tab>$listnum) ".
			"<a href='chatcmd:///tell ".$chatBot->vars["name"]." quote search $key>$key</a>".
			": <highlight>$val<end> ".number_format((100*($val/$count)),0)."%\n";
		if ($listnum >= $top) {
			break;
		}
	}

	$msg = Text::make_blob("Quote stats", $blob);
	$chatBot->send($msg, $sendto);
//View a specific quote
} else if (preg_match("/^quote ([0-9]+)$/i", $message, $arr)) {
	
	//get total number of entries(by grabbing the Highest ID.)
    $db->query("SELECT * FROM `#__quote` ORDER BY `IDNumber` DESC");
	$row = $db->fObject();
	$count = $row->IDNumber;
	
	$db->query("SELECT * FROM `#__quote` WHERE `IDNumber` = '$arr[1]'");
	if ($db->numrows() > 0) {
		$row = $db->fObject();
		$quoteID = $row->IDNumber;
		$quoteWHO = $row->Who;
		$quoteOfWHO = $row->OfWho;
		$quoteDATE = $row->When;
		$quoteMSG = $row->What;
		
		$msg = "<header> ::::: Quote Info ::::: <end>\n\n";
		$msg .= "<tab>ID: (<highlight>$quoteID<end> of $count)\n";
		$msg .= "<tab>Poster: <highlight>$quoteWHO<end>\n";
		$msg .= "<tab>Quoting: <highlight>$quoteOfWHO<end>\n";
		$msg .= "<tab>Date: <highlight>$quoteDATE<end>\n\n";
		
		$msg .= "<tab>Quotes posted by <highlight>$quoteWHO<end>: ";
		$db->query("SELECT * FROM `#__quote` WHERE `Who` = '$quoteWHO'");
		$list = "";
		while ($row = $db->fObject()) {
			$list .= "<a href='chatcmd:///tell ".$chatBot->vars["name"]." quote $row->IDNumber>$row->IDNumber</a>, ";
		}
		$msg .= substr($list,0,strlen($list)-2)."\n\n";
		
		$msg .="<tab>Quotes <highlight>$quoteOfWHO<end> said: ";
		$db->query("SELECT * FROM `#__quote` WHERE `OfWho` = '".str_replace("'", "''", $quoteOfWHO)."'");
		$list = "";
		while ($row = $db->fObject()) {
			$list .= "<a href='chatcmd:///tell ".$chatBot->vars["name"]." quote $row->IDNumber>$row->IDNumber</a>, ";
		}
		$msg .= substr($list,0,strlen($list)-2);

		$msg = Text::make_blob("Quote", $msg).': "'.$quoteMSG.'"';
		
	} else {
		$msg = "No quote found with that ID.";
	}
	$chatBot->send($msg, $sendto);
//View a random quote
} else if (preg_match("/^quote$/i", $message)) {
	//get total number of entries for rand (and see if we even have any quotes to show)
	
	// find the highest IDnumber
    $db->query("SELECT * FROM `#__quote` ORDER BY `IDNumber` DESC");
	$row = $db->fObject();
	$count = $row->IDNumber;

	if ($count != "") {
		do {
			// loop till we find a random entry that isnt deleted.
			$db->query("SELECT * FROM `#__quote` WHERE `IDNumber` = '".rand(0, $count)."'");
			if ($db->numrows() > 0) {
				$row = $db->fObject();
				$quoteID = $row->IDNumber;
				$quoteWHO = $row->Who;
				$quoteOfWHO = $row->OfWho;
				$quoteDATE = $row->When;
				$quoteMSG = $row->What;
				break;
			}
		} while (true);
		
		$msg = "<header> :::::: Quote Info :::::: <end>\n\n";
		$msg .= "<tab>ID: (<highlight>$quoteID<end> of $count)\n";
		$msg .= "<tab>Poster: <highlight>$quoteWHO<end>\n";
		$msg .= "<tab>Quoting: <highlight>$quoteOfWHO<end>\n";
		$msg .= "<tab>Date: <highlight>$quoteDATE<end>\n\n";
		
		$msg .= "<tab>Quotes posted by <highlight>$quoteWHO<end>: ";
		$db->query("SELECT * FROM `#__quote` WHERE `Who` = '".str_replace("'", "''", $quoteWHO)."'");
		$list = "";
		while ($row = $db->fObject()) {
			$list .= "<a href='chatcmd:///tell ".$chatBot->vars["name"]." quote $row->IDNumber>$row->IDNumber</a>, ";
		}
		$msg .= substr($list,0,strlen($list)-2)."\n\n";
		
		$msg .= "<tab>Quotes <highlight>$quoteOfWHO<end> said: ";
		$db->query("SELECT * FROM `#__quote` WHERE `OfWho` = '".str_replace("'", "''", $quoteOfWHO)."'");
		$list = "";
		while ($row = $db->fObject()) {
			$list .= "<a href='chatcmd:///tell ".$chatBot->vars["name"]." quote $row->IDNumber>$row->IDNumber</a>, ";
		}
		$msg .= substr($list, 0, strlen($list) - 2);
		
		$msg = Text::make_blob("Quote", $msg).': "'.$quoteMSG.'"';
		
	} else {
		$msg = "I dont have any quotes to show!";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
