<?php
   /*
   ** Author: Junglegeorge (RK2) basesd on inactivemem by Legendadv (RK2)
   ** Description: List org meembers who recently logged on
   */

if (preg_match("/^recentseen ([a-z0-9]+)/i", $message, $arr)) {

	if ($chatBot->vars["my_guild_id"] == "") {
	    $sendto->reply("The Bot needs to be in an org to show the orgmembers.");
		return;
	}

	$time = Util::parseTime($arr[1]);
	if ($time < 1) {
		$msg = "You must enter a valid time parameter.";
		$sendto->reply($msg);
		return;
	}

	$timeString = Util::unixtime_to_readable($time, false);
	$time = time() - $time;

	$data = $db->query("SELECT case when a.main is null then o.name else a.main end as main ,o.logged_off,o.name FROM org_members_<myname> o LEFT JOIN alts a ON o.name = a.alt WHERE `mode` != 'del'AND `logged_off` > ? ORDER BY 1, o.logged_off desc, o.name", $time); 


	if (count($data) == 0) {
	    $sendto->reply("No members recorded.");
		return;
	}

	$numinactive = 0;
	$highlight = 0;
  
	$blob = "Org members who have logged on since <highlight>{$timeString}<end>.\n\n";
	
  $prevtoon = '';
	forEach ($data as $row) {
		if ($row->main != $prevtoon) {
      $prevtoon = $row->main;
		  $numrecentcount++;
		  $alts = Text::make_chatcmd("Alts", "/tell <myname> alts {$row->main}");
      $logged = $row->logged_off;
		  $lasttoon = $row->name;

		  $player = $row->main." [{$alts}]\nLast seen as [$lasttoon] on ".date(Util::DATETIME, $logged)."\n\n";
		  if ($highlight == 1) {
			 $blob .= "<highlight>$player<end>";
			 $highlight = 0;
		  } else {
			 $blob .= $player;
			 $highlight = 1;
		  }
    } 
	}
	$msg = Text::make_blob("$numrecentcount Recently seen Org Members", $blob);
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}
?>
