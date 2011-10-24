<?php

if (preg_match("/^history (.+) (\d)$/i", $message, $arr) || preg_match("/^history (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	if (isset($arr[2])) {
		$dimension = $arr[2];
	} else {
		$dimension = $chatBot->vars['dimension'];
		if (!$chatBot->get_uid($name)) {
			$msg = "Player <highlight>$name<end> doesn't exist.";
			$chatBot->send($msg, $sendto);
			return;
		}
	}
	
	$msg = "Getting History of player <highlight>$name<end>...";
	$chatBot->send($msg, $sendto);
	
	$link = array();
	$history = new history($name, $dimension);
	if ($history->errorCode != 0) {
		$msg = $history->errorInfo;
	} else {
		$altInfo = Alts::get_alt_info($name);
		$link[] = "<header>::::: History of $name ::::::<end>\n\n";
		$lh = "<highlight>Options:<end>\n";
		if (count($altInfo->alts) > 0) {
		$lh .= "<tab><tab><a href='chatcmd:///tell <myname> alts $name'>Show Alts</a> \n";
		}
		$lh .= "<tab><tab><a href='chatcmd:///tell <myname> whois $name'>Whois</a>\n";
		$lh .= "<tab><tab><a href='chatcmd:///cc addbuddy $name'>Add to your friendslist</a>\n";
		$lh .= "<tab><tab><a href='chatcmd:///cc rembuddy $name'>Remove from your friendslist</a>\n\n";
		
		$lh .= "Date           Level    AI     Faction      Guild(rank) \n";
		$lh .= "________________________________________________ \n";
		$l = "";
		forEach ($history->data as $key => $data) {
			$level = $data["level"];
			
			if ($data["ailevel"] == "") {
				$ailevel = "<green>0<end>";
			} else {
				$ailevel = "<green>".$data["ailevel"]."<end>";
			}
			
			if ($data["faction"] == "Omni") {
				$faction = "<omni>Omni<end>";
			} else if ($data["faction"] == "Clan") {
				$faction = "<clan>Clan<end>";
			} else {
				$faction = "<neutral>Neutral<end>";
			}

			if ($data["guild"] == "") {
				$guild = "Not in a guild";
			} else {
				$guild = $data["guild"]."(".$data["rank"].")";
			}

			$l .= "$key |  $level  | $ailevel | $faction | $guild\n";
		}
		$link[] = array("header" => $lh, "content" => $l);
		$msg = Text::make_structured_blob("History of $name", $link);
	}

    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>