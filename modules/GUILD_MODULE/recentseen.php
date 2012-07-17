<?php
   /*
   ** Author: Junglegeorge (RK2) basesd in inacticemem by Legendadv (RK2)
   ** Description: Lookup recent org members
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

	$data = $db->query("SELECT * FROM org_members_<myname> o LEFT JOIN alts a ON o.name = a.alt WHERE `mode` != 'del' AND `logged_off` > ?  ORDER BY o.name", $time);

	if (count($data) == 0) {
	    $sendto->reply("No members recorded.");
		return;
	}

	$numinactive = 0;
	$highlight = 0;

	$blob = "Org members who have logged on since <highlight>{$timeString}<end>.\n\n";
	

	forEach ($data as $row) {
		$logged = 0;
		$main = $row->main;
		if ($row->main != "") {
			$data1 = $db->query("SELECT * FROM alts a JOIN org_members_<myname> o ON a.alt = o.name WHERE `main` = ?", $row->main);
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
		$alts = Text::make_chatcmd("Alts", "/tell <myname> alts {$row->name}");
		$logged = $row->logged_off;
		$lasttoon = $row->name;

		$player = $row->name."; Main: $main; [{$alts}]\nLast seen on [$lasttoon] on ".date(Util::DATETIME, $logged)."\n\n";
		if ($highlight == 1) {
			$blob .= "<highlight>$player<end>";
			$highlight = 0;
		} else {
			$blob .= $player;
			$highlight = 1;
		}
	}
	$msg = Text::make_blob("$numinactive Recent seen Org Members", $blob);
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}
?>
