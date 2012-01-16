<?php

if (preg_match("/^feedback$/i", $message)) {
	$sql = "
		SELECT
			name,
			SUM(CASE WHEN reputation = '+1' THEN 1 ELSE 0 END) pos_rep,
			SUM(CASE WHEN reputation = '-1' THEN 1 ELSE 0 END) neg_rep
		FROM
			feedback
		GROUP BY
			name";
			
	$data = $db->query($sql);
	$count = count($data);
	
	if ($count == 0) {
		$msg = "There are no characters on the feedback list.";
		$sendto->reply($msg);
		return;
	}
	
	$blob = '';
	forEach ($data as $row) {
		$details_link = Text::make_chatcmd('Details', "/tell <myname> feedback $row->name");
		$blob .= "$row->name  <green>+{$row->pos_rep}<end> <orange>-{$row->neg_rep}<end>   {$details_link}\n";
	}
	$msg = Text::make_blob("Feedback List ($count)", $blob);
	$sendto->reply($msg);
} else if (preg_match("/^feedback ([a-z0-9-]*) (\\+1|\\-1) (.*)$/i", $message, $arr)) {
	$charid = $chatBot->get_uid($arr[1]);
	$name = ucfirst(strtolower($arr[1]));
	$rep = $arr[2];
	$comment = $arr[3];
	$by_charid = $chatBot->get_uid($sender);

	if ($charid == false) {
		$sendto->reply("Could not find character '$name'.");
		return;
	}
	
	if ($charid == $by_charid) {
		$sendto->reply("You cannot give yourself feedback.");
		return;
	}
	
	$time = time() - 86400;
	
	$sql = "SELECT name FROM feedback WHERE `by_charid` = ? AND `charid` = ? AND `dt` > ?";
	$data = $db->query($sql, $by_charid, $charid, $time);
	if (count($data) > 0) {
		$sendto->reply("You may only submit feedback for a player once every 24 hours. Please try again later.");
		return;
	}
	
	$sql = "SELECT name FROM feedback WHERE `by_charid` = ?";
	$data = $db->query($sql, $by_charid);
	if (count($data) > 3) {
		$sendto->reply("You may submit feedback a maximum of 3 times in a 24 hour period. Please try again later.");
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
			?,
			?,
			?,
			?,
			?,
			?,
			?
		)";

	$db->exec($sql, $name, $charid, $rep, $comment, $sender, $by_charid, time());
	$sendto->reply("Feedback for $name added successfully.");
} else if (preg_match("/^feedback ([a-z0-9-]*)$/i", $message, $arr)) {
    $charid = $chatBot->get_uid($arr[1]);
	$name = ucfirst(strtolower($arr[1]));
	
	if ($charid == false) {
		$where_sql = "WHERE `name` = '$name'";
	} else {
		$where_sql = "WHERE `charid` = '$charid'";
	}
    
	$data = $db->query("SELECT reputation, COUNT(*) count FROM feedback {$where_sql} GROUP BY `reputation`");
	if (count($data) == 0) {
		$msg = "<highlight>$name<end> has no feedback.";
	} else {
		$num_positive = 0;
		$num_negative = 0;
		forEach ($data as $row) {
			if ($row->reputation == '+1') {
				$num_positive = $row->count;
			} else if ($row->reputation == '-1') {
				$num_negative = $row->count;
			}
		}

		$blob = "Positive feedback: <green>{$num_positive}<end>\n";
		$blob .= "Negative feedback: <orange>{$num_negative}<end>\n\n";
		$blob .= "Last 10 comments about this user:\n\n";
		
		$sql = "SELECT * FROM feedback {$where_sql} ORDER BY `dt` DESC LIMIT 10";
		$data = $db->query($sql);
		forEach ($data as $row) {
			if ($row->reputation == '-1') {
				$blob .= "<orange>";
			} else {
				$blob .= "<green>";
			}

			$time = Util::unixtime_to_readable(time() - $row->dt);
			$blob .= "({$row->reputation}) $row->comment <end> $row->by <white>{$time} ago<end>\n\n";
		}
		
		$msg = Text::make_blob("Feedback for {$name}", $blob);
	}

	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
