<?php

function orgmatesformat ($memberlist, $color, $timestart, $orgname) {
	$chatBot = Registry::getInstance('chatBot');
	
	$map = $memberlist["orgtype"];

	$totalonline = 0;
	$totalcount = count($memberlist["result"]);
	forEach ($memberlist["result"] as $amember) {
		$newlist[$amember["rank_id"]][] = $amember["name"];
	}
	
	$blob = array("");
	
	for ($rankid = 0; $rankid < count($map); $rankid++) {
		$onlinelist = "";
		$offlinelist = "";
		$olcount = 0;
		$rank_online = 0;
		$rank_total = count($newlist[$rankid]);
		
		sort($newlist[$rankid]);
		for ($i = 0; $i < $rank_total; $i++) {
			if ($memberlist["result"][$newlist[$rankid][$i]]["online"]) {
				$rank_online++;
				$onlinelist .= "  " . $memberlist["result"][$newlist[$rankid][$i]]["post"] . "\n";
			} else {
				if ($offlinelist != "") {
					$offlinelist .= ", ";
					if (($olcount % 50) == 0) {
						$offlinelist .= "<end><pagebreak>" . $color["offline"];
					}
				}
				$offlinelist .= $newlist[$rankid][$i];
				$olcount++;
			}
		}
		
		$totalonline += $rank_online;
		
		$bh = $color["header"] . $map[$rankid] . "</font> ";
		$bh .= "(" . $color["onlineH"] . "{$rank_online}</font> online of " . $color["onlineH"] . "{$rank_total}</font>)";
		
		$bhi = $bh . " cont...\n";
		$bh .= "\n";
		
		$b = "";
		if ($onlinelist != "") {
			$b .= $onlinelist;
		}
		if ($offlinelist != "") {
			$b .= $color["offline"] . $offlinelist . "<end>\n";
		}
		
		$blob[] = array("header" => $bh, "content" => $b, "footer" => "\n\n", "header_incomplete" => $bhi, "footer_incomplete" => "\n");
	}
	
	$totaltime = time() - $timestart;
	$header  = $color["onlineH"].$orgname."<end> has ";
	$header .= $color["onlineH"]."$totalonline</font> online out of a total of ".$color["onlineH"]."$totalcount</font> members. ";
	$header .= "(".$color["onlineH"]."$totaltime</font> seconds)\n\n";
	$blob[0] = $header;
	
	return $blob;
}

function checkOrglistEnd($forceEnd = false) {
	$chatBot = Registry::getInstance('chatBot');

	// Don't want to reboot to see changes in color edits, so I'll store them in an array outside the function.
	$orgcolor["header"]  = "<font color='#FFFFFF'>";   // Org Rank title
	$orgcolor["onlineH"] = "<highlight>";              // Highlights on whois info
	$orgcolor["offline"] = "<font color='#555555'>";   // Offline names

	if (isset($chatBot->data["ORGLIST_MODULE"]) && count($chatBot->data["ORGLIST_MODULE"]["added"]) == 0 || $forceEnd) {
		$blob = orgmatesformat($chatBot->data["ORGLIST_MODULE"], $orgcolor, $chatBot->data["ORGLIST_MODULE"]["start"], $chatBot->data["ORGLIST_MODULE"]["org"]);
		$msg = Text::make_structured_blob("Orglist for '".$chatBot->data["ORGLIST_MODULE"]["org"]."'", $blob);
		$chatBot->send($msg, $chatBot->data["ORGLIST_MODULE"]["sendto"]);

		// in case it was ended early
		forEach ($chatBot->data["ORGLIST_MODULE"]["added"] as $name => $value) {
			Buddylist::remove($name, 'onlineorg');
		}
		unset($chatBot->data["ORGLIST_MODULE"]);
	}
}

?>
