<?php

if (preg_match("/^feedback ([a-z0-9-]*) (\\+1|\\-1) (.*)$/i", $message, $arr)) {
	$charid = AoChat::get_uid($arr[1]);
	$name = ucfirst(strtolower($arr[1]));
	$rep = $arr[2];
	$comment = str_replace("'", "''", $arr[3]);
	$by_charid = AoChat::get_uid($sender);

	if ($charid == false) {
		bot::send("Could not find character '$name'.", $sendto);
		return;
	}
	
	if ($charid == $by_charid) {
		bot::send("You cannot give yourself feedback.", $sendto);
		return;
	}
	
	$time = time() - 86400;
	
	$sql = "SELECT name FROM feedback WHERE `by_charid` = '$by_charid' AND `charid` = '$charid' AND `dt` > '$time'";
	$db->query($sql);
	if ($db->numrows() > 0) {
		bot::send("You may only submit feedback for a player once every 24 hours. Please try again later.", $sendto);
		return;
	}
	
	$sql = "SELECT name FROM feedback WHERE `by_charid` = '$by_charid'";
	$db->query($sql);
	if ($db->numrows() > 3) {
		bot::send("You may submit a maximum of 3 feedbacks in a 24 hour period. Please try again later.", $sendto);
		return;
	}

	$sql = "
		INSERT INTO feedback (
			`name`,
			`charid`,
			`reputation`,
			`comment`,
			`by`,
			`by_charid`,
			`dt`
		) VALUES (
			'$name',
			'$charid',
			'$rep',
			'$comment',
			'$sender',
			'$by_charid',
			'" . time() . "'
		)";

	$db->exec($sql);
	bot::send("Feedback for $name added successfully.", $sendto);
} else if (preg_match("/^feedback ([a-z0-9-]*)$/i", $message, $arr)) {
    $charid = AoChat::get_uid($arr[1]);
	$name = ucfirst(strtolower($arr[1]));
	
	if ($charid == false) {
		$where_sql = "WHERE `name` = '$name'";
	} else {
		$where_sql = "WHERE `charid` = '$charid'";
	}
    
	$db->query("SELECT reputation, COUNT(*) count FROM feedback {$where_sql} GROUP BY `reputation`");
	if($db->numrows() == 0) {
		$msg = "<highlight>$name<end> has no feedback.";
	} else {
		$num_positive = 0;
		$num_negative = 0;
		while ($row = $db->fObject()) {
			if ($row->reputation == '+1') {
				$num_positive = $row->count;
			} else if ($row->reputation == '-1') {
				$num_negative = $row->count;
			}
		}

		$blob = "<header>::::: Feedback for {$name} :::::<end>\n\n";
		$blob .= "Positive feedback: <green>{$num_positive}<end>\n";
		$blob .= "Negative feedback: <orange>{$num_negative}<end>\n\n";
		$blob .= "Last 10 comments about this user:\n\n";
		
		$sql = "SELECT * FROM feedback {$where_sql} ORDER BY `dt` DESC LIMIT 10";
		$db->query($sql);
		$data = $db->fObject('all');
		forEach ($data as $row) {
			if ($row->reputation == '-1') {
				$blob .= "<orange>";
			} else {
				$blob .= "<green>";
			}

			$time = Util::unixtime_to_readable(time() - $row->dt);
			$blob .= "({$row->reputation}) $row->comment <end> $row->by <white>{$time} ago<end>\n\n";
		}
		
		$msg = Text::make_link("Feedback for {$name}", $blob, 'blob');
	}

	bot::send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
