<?php
   /*
   ** Author: Legendadv (RK2)
   ** Description: Lookup inactive org members
   */

if (preg_match("/^inactivemem ([a-z0-9]+)/i", $message, $arr)) {
	
	if ($chatBot->vars["my_guild_id"] == "") {
	    $chatBot->send("The Bot needs to be in an org to show the orgmembers.", $sendto);
		return;
	}
	
	$time = Util::parseTime($arr[1]);
	if ($time < 1) {
		$msg = "You must enter a valid time parameter.";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	$timeString = Util::unixtime_to_readable($time, false);
	$time = time() - $time;
	
	$data = $db->query("SELECT * FROM org_members_<myname> o LEFT JOIN alts a ON o.name = a.alt WHERE `mode` != 'del' AND `logged_off` < $time  ORDER BY o.name");  

  	if (count($data) == 0) {
	    $chatBot->send("No members recorded.", $sendto);    
		return;
	}

	$numinactive = 0;
	$highlight = 0;
	$blob = "<header>::::: Inactive Members of <myguild> :::::<end>\n\n";
	$blob .="Org members who have been inactive for atleast <highlight>{$timeString}<end>.\n\n";
	$blob .="<red>**Be careful with clicking the Org Kick links.  It will cause you to /org kick, and the bot can't help you undo that.<end>\n\n";
	
	forEach ($data as $row) {
		$logged = 0;
		$main = $row->main;
		if ($row->main != "") {
			$data1 = $db->query("SELECT * FROM alts a JOIN org_members_<myname> o ON a.alt = o.name WHERE `main` = '{$row->main}'");
			forEach ($data1 as $row1) {
				if ($row1->logged_off > $time) {
					continue 2;
				}
				
				if ($row1->logged_off > $logged) {
					$logged = $row1->logged_off;
					$lasttoon = $row1->name;
				}
			}
		}
		
		$numinactive++;
		$kick = " [".Text::make_chatcmd("Kick {$row->name}?", "/k {$row->name}")."]"; ///org kick {$row->name}
		$alts = Text::make_chatcmd("Alts", "/tell <myname> alts {$row->name}");
		$logged = $row->logged_off;
		$lasttoon = $row->name;
		
		$player = $row->name."; Main: $main; [{$alts}]$kick\nLast seen on [$lasttoon] on ".date("Y-m-d",$logged)."\n\n";
		if ($highlight == 1) {
			$blob .= "<highlight>$player<end>";
			$highlight = 0;
		} else {
			$blob .= $player;
			$highlight = 1;
		}
	}
	$msg = Text::make_blob("$numinactive Inactive Org Members", $blob);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}
?>
