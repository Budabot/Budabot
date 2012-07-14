<?php

$towers = Registry::getInstance('towers');

if (preg_match("/^penalty$/i", $message) || preg_match("/^penalty ([a-z0-9]+)$/i", $message, $arr)) {
	if (isset($arr)) {
		$time = Util::parseTime($arr[1]);
		if ($time < 1) {
			$msg = "You must enter a valid time parameter.";
			$sendto->reply($msg);
			return;
		}
	} else {
		$time = 7200;  // default to 2 hours
	}
	$penaltyTimeString = Util::unixtime_to_readable($time, false);

	$data = $towers->getSitesInPenalty(time() - $time);

	if (count($data) > 0) {
		$blob = '';
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
			$blob .= "<{$row->att_faction}>{$row->att_guild_name}<end> - <white>$timeString ago<end>\n";
		}
		$msg = Text::make_blob("Orgs in penalty ($penaltyTimeString)", $blob);
	} else {
		$msg = "There are no orgs who have attacked or won battles in the past $penaltyTimeString.";
	}
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
