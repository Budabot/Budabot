<?php

if (preg_match("/^penalty$/i", $message) || preg_match("/^penalty (\\d+)$/i", $message, $arr)) {
	$hours = 2;
	if ($arr) {
		$hours = $arr[1];
	}
	$time = time() - (3600 * $hours);

	$sql = "
		SELECT att_guild_name, att_faction, MAX(IFNULL(t2.time, t1.time)) AS penalty_time
		FROM tower_attack_<myname> t1
			LEFT JOIN tower_victory_<myname> t2 ON t1.id = t2.id
		WHERE (t2.time IS NULL AND t1.time > $time) OR t2.time > $time
		GROUP BY att_guild_name, att_faction
		ORDER BY att_faction ASC, penalty_time DESC";
	$db->query($sql);
	$data = $db->fObject('all');
	
	if (count($data) > 0) {
		$blob = "<header> :::::: Orgs in penalty ($hours hour(s)) :::::: <end>\n";
		$current_faction = '';
		forEach ($data as $row) {
			if ($row->att_guild_name == '') {
				continue;
			}
			if ($current_faction != $row->att_faction) {
				$blob .= "\n<header> ::: {$row->att_faction} ::: <end>\n";
				$current_faction = $row->att_faction;
			}
			$timeString = Util::unixtime_to_readable(time() - $row->penalty_time, false);
			$blob .= "<$row->att_faction>{$row->att_guild_name}<end> - <white>$timeString ago<end>\n";
		}
		$msg = Text::make_blob("Orgs in penalty ($hours hour(s))", $blob);
	} else {
		$msg = "There are no orgs who have attacked or won battles in the past $hours hour(s)";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
