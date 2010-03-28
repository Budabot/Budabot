<?
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

$message = str_replace("'", "\'", $message);
$message = str_replace('"', "&quot;", $message);



// Adding a quote
if (preg_match("/^quote add (.+)$/i", $message, $arr)) {
	
	if (!isset($this->admins[$sender])) {
		$requirement = $this->settings["quote_add_min"];
		if ($requirement >= 0) {
			if (!$this->guildmembers[$sender]) {
				bot::send("Only org members can add a new quote.", $sendto);
				return;
			} else if ($requirement < $this->guildmembers[$sender]) {
				$rankdiff = $this->guildmembers[$sender]-$requirement;
				bot::send("You need $rankdiff promotion(s) in order to add a quote.", $sendto);
				return;
			}
		}else if (($requirement == -1 && !isset($this->chatlist[$sender])) && !$this->guildmembers[$sender]) {
			bot::send("You need to at least be in the private chat in order to add a quote.", $sendto);
			return;
		}
	}
	
	$arr[1] = trim($arr[1]);
	$db->query("SELECT * FROM quote WHERE `What` LIKE \"$arr[1]\"");
	if ($db->numrows() > 0) {
		$row = $db->fObject();
		$msg = "This quote is already in as quote <highlight>$row->IDNumber<end>.";
	} else {
		if(strlen($arr[1]) <= 1000) {
	
			$quoteDATE = date("F j, Y, g:i a");
			$quoteMSG = $arr[1];
			$quoteWHO = $sender;
	
			// Search for highest ID and +1 for new ID.
			$db->query("SELECT * FROM quote ORDER BY `IDNumber` DESC");
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
	
			} else {
				//without a colon.. quoting him/her/itself?
				$quoteOfWHO = $sender;
			}
			$db->query("INSERT INTO quote (`IDNumber`, `Who`, `OfWho`, `When`, `What`) VALUES ('$quoteID', '$quoteWHO', '$quoteOfWHO', '$quoteDATE', \"$quoteMSG\")");
			$msg = "Quote <highlight>$quoteID<end> has been added.";
		} else {
			$msg = "This quote is too big.";
		}
	}
	
// Removing a quote
} else if (preg_match("/^quote (rem|del|remove|delete) (.+)$/i", $message, $arr)) {

	$db->query("SELECT * FROM quote WHERE `IDNumber` = '$arr[2]'");

	if ($db->numrows() > 0) {
		$row = $db->fObject();
		$quoteID = $arr[2];
		$quoteWHO = $row->Who;
		$quoteOfWHO = $row->OfWho;
		$quoteDATE = $row->When;
		$quoteMSG = $row->What;

		//only author or superadmin can delete.
		if (($quoteWHO == $sender) || ($this->admins[$sender]["level"] >= 4)) {
			$db->query("DELETE FROM quote WHERE `IDNumber` = '$quoteID'");
			$msg = "This quote has been deleted.";
		} else {
			$msg = "Only the Superadmin or $quoteWHO can delete this quote.";
		}
	} else {
		$msg = "Could not find this quote.  Already deleted?";
	}

//Searching for authors or victims.
} else if (preg_match("/^quote search (.+)$/i", $message, $arr)) {
	
	$search = ucfirst(strtolower($arr[1]));
	
	// Search for poster:
	$list = "";
	$db->query("SELECT * FROM quote WHERE `Who` LIKE \"$search\"");
	while($row = $db->fObject()) {
		$list .= "<a href='chatcmd:///tell ".$this->vars["name"]." quote $row->IDNumber'>$row->IDNumber</a>, ";
	}
	if ($list) {
		$msg .="<tab>Quotes posted by <highlight>$search<end>: ";
		$msg .= substr($list,0,strlen($list)-2);
	}	
	
	// Search for victim:
	$list = "";
	$db->query("SELECT * FROM quote WHERE `OfWho` LIKE \"$search\"");
	while($row = $db->fObject()) {
		$list .= "<a href='chatcmd:///tell ".$this->vars["name"]." quote $row->IDNumber'>$row->IDNumber</a>, ";
	}
	if ($list) {
		if ($msg) {$msg .="<br><br>";}
		$msg .="<tab>Quotes <highlight>$search<end> said: ";
		$msg .= substr($list,0,strlen($list)-2);
	}

	// Search inside quotes:
	$list = "";
	$db->query("SELECT * FROM quote WHERE `OfWho` NOT LIKE \"$search\" AND `What` LIKE \"%$search%\"");
	while($row = $db->fObject()) {
		$list .= "<a href='chatcmd:///tell ".$this->vars["name"]." quote $row->IDNumber'>$row->IDNumber</a>, ";
	}
	if ($list) {
		if ($msg) {$msg .="<br><br>";}
		$msg .="<tab>Quotes that contain '<highlight>$search<end>': ";
		$msg .= substr($list,0,strlen($list)-2);
	}
	
	if ($msg) {$msg = bot::makeLink("Results for: '$search'", "<header>::::: Quote Info :::::<end><br><br>$msg");} 
	else {$msg = "Couldn't find any matches for this search.";}
	
	
	
//Show the top quoters/quoted
} else if (preg_match("/^quote stats$/i", $message, $arr)) {
	// might need to run it ourselves the first time. 
	// cron will keep updating it later.
	$msg = $this->vars["quotestats"];
	if ($msg == "") {
		include "quotestats.php";
	}
	$msg = $this->vars["quotestats"];
	
	
	
//View a specific quote
} else if (preg_match("/^quote ([0-9]+)$/i", $message, $arr)) {
	
	//get total number of entries(by grabbing the Highest ID.)
    $db->query("SELECT * FROM quote ORDER BY `IDNumber` DESC");
	$row = $db->fObject();
	$count = $row->IDNumber;
	
	$db->query("SELECT * FROM quote WHERE `IDNumber` = '$arr[1]'");
        if ($db->numrows() > 0) {
		$row = $db->fObject();
		$quoteID = $row->IDNumber;
		$quoteWHO = $row->Who;
		$quoteOfWHO = $row->OfWho;
		$quoteDATE = $row->When;
		$quoteMSG = $row->What;
		
		$msg = "<header>::::: Quote Info :::::<end><br><br>";
		$msg .="<tab>ID: (<highlight>$quoteID<end> of $count)<br>";
		$msg .="<tab>Poster: <highlight>$quoteWHO<end><br>";
		$msg .="<tab>Quoting: <highlight>$quoteOfWHO<end><br>";
		$msg .="<tab>Date: <highlight>$quoteDATE<end><br><br>";
		
		$msg .="<tab>Quotes posted by <highlight>$quoteWHO<end>: ";
		$db->query("SELECT * FROM quote WHERE `Who` = '$quoteWHO'");
		$list = "";
		while($row = $db->fObject()) {
			$list .= "<a href='chatcmd:///tell ".$this->vars["name"]." quote $row->IDNumber>$row->IDNumber</a>, ";
		}
		$msg .= substr($list,0,strlen($list)-2)."<br><br>";
		
		$msg .="<tab>Quotes <highlight>$quoteOfWHO<end> said: ";
		$db->query("SELECT * FROM quote WHERE `OfWho` = '$quoteOfWHO'");
		$list = "";
		while($row = $db->fObject()) {
			$list .= "<a href='chatcmd:///tell ".$this->vars["name"]." quote $row->IDNumber>$row->IDNumber</a>, ";
		}
		$msg .= substr($list,0,strlen($list)-2);

		$msg = bot::makeLink("Quote", $msg).': "'.$quoteMSG.'"';
		
	} else {
		$msg = "No quote found with that ID.";
	}
	
	
// if i didnt get a number, they messed up.
} else if (preg_match("/^quote (.+)$/i", $message, $arr)) {	
	$msg = "Its <symbol>quote for a random quote, or <symbol>quote # for a specific quote.";
	
	
	
//View a random quote
} else if (preg_match("/^quote$/i", $message)) {
	//get total number of entries for rand (and see if we even have any quotes to show)
	
	// find the highest IDnumber
    $db->query("SELECT * FROM quote ORDER BY `IDNumber` DESC");
	$row = $db->fObject();
	$count = $row->IDNumber;

	if ($count != "") {
		do {
			// loop till we find a random entry that isnt deleted.
			$db->query("SELECT * FROM quote WHERE `IDNumber` = '".rand(0, $count)."'");
			if ($db->numrows() > 0) {
				$row = $db->fObject();
				$quoteID = $row->IDNumber;
				$quoteWHO = $row->Who;
				$quoteOfWHO = $row->OfWho;
				$quoteDATE = $row->When;
				$quoteMSG = $row->What;
				break;
			}
		} while(1);
		
		$msg = "<header>::::: Quote Info :::::<end><br><br>";
		$msg .="<tab>ID: (<highlight>$quoteID<end> of $count)<br>";
		$msg .="<tab>Poster: <highlight>$quoteWHO<end><br>";
		$msg .="<tab>Quoting: <highlight>$quoteOfWHO<end><br>";
		$msg .="<tab>Date: <highlight>$quoteDATE<end><br><br>";
		
		$msg .="<tab>Quotes posted by <highlight>$quoteWHO<end>: ";
		$db->query("SELECT * FROM quote WHERE `Who` = '$quoteWHO'");
		$list = "";
		while($row = $db->fObject()) {
			$list .= "<a href='chatcmd:///tell ".$this->vars["name"]." quote $row->IDNumber>$row->IDNumber</a>, ";
		}
		$msg .= substr($list,0,strlen($list)-2)."<br><br>";
		
		$msg .="<tab>Quotes <highlight>$quoteOfWHO<end> said: ";
		$db->query("SELECT * FROM quote WHERE `OfWho` = '$quoteOfWHO'");
		$list = "";
		while($row = $db->fObject()) {
			$list .= "<a href='chatcmd:///tell ".$this->vars["name"]." quote $row->IDNumber>$row->IDNumber</a>, ";
		}
		$msg .= substr($list,0,strlen($list)-2);
		
		$msg = bot::makeLink("Quote", $msg).': "'.$quoteMSG.'"';
		
	} else {
		$msg = "I dont have any quotes to show!";
	}
}



// Send info back

if ($msg) {
	$msg =  str_replace("\'", "'", $msg);
	bot::send($msg, $sendto);
}
?>
