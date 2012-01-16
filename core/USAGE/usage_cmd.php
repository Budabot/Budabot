<?php

$limit = 25;

if (preg_match("/^usage$/i", $message) || preg_match("/^usage ([a-z0-9]+)$/i", $message, $arr)) {
	if (isset($arr)) {
		$time = Util::parseTime($arr[1]);
		if ($time == 0) {
			$msg = "Please enter a valid time.";
			$sendto->reply($msg);
			return;
		}
		$time = $time;
	} else {
		$time = 604800;
	}
	
	$timeString = Util::unixtime_to_readable($time);
	$time = time() - $time;
	
	// most used commands
	$sql = "SELECT command, COUNT(command) AS count FROM usage_<myname> WHERE dt > ? GROUP BY command ORDER BY count DESC LIMIT $limit";
	$data = $db->query($sql, $time);
	
	$blob = "<header> ::: Most Used Commands ::: <end>\n";
	forEach ($data as $row) {
		$blob .= "<highlight>{$row->command}<end> ({$row->count})\n";
	}
	
	// users who have used the most commands
	$sql = "SELECT sender, COUNT(sender) AS count FROM usage_<myname> WHERE dt > ? GROUP BY sender ORDER BY count DESC LIMIT $limit";
	$data = $db->query($sql, $time);
	
	$blob .= "\n<header> ::: Most Active Users ::: <end>\n";
	forEach ($data as $row) {
		$blob .= "<highlight>{$row->sender}<end> ({$row->count})\n";
	}
	
	$msg = Text::make_blob("Usage Statistics ({$timeString})", $blob);
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
