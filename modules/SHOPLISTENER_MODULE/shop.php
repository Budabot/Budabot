<?php

if (preg_match("/^shop (.+)$/i", $message, $arr)) {
	$search = $arr[1];
	$sql = "
		SELECT
			sender,
			message,
			MAX(dt) as dt
		FROM
			shopping_items s1
			JOIN shopping_messages s2
				ON s1.message_id = s2.id
		WHERE
			s2.dimension = <dim>
			AND s1.name LIKE '%" . str_replace("'", "''", $search) . "%'
		GROUP BY
			sender,
			message";
	$db->query($sql);
	$data = $db->fObject('all');
	
	$blob = "<header> :::::: Shopping Results for '$search' :::::: <end>\n\n";
	forEach ($data as $row) {
		$senderLink = Text::make_userlink($row->sender);
		$timeString = Util::unixtime_to_readable(time()- $row->dt, false);
		$post = preg_replace('/<a href="itemref:\/\/(\d+)\/(\d+)\/(\d+)">([^<]+)<\/a>/', "<a href='itemref://\\1/\\2/\\3'>\\4</a>", $row->message);
		$blob .= "[$senderLink]: {$post} - <highlight>($timeString ago)<end>\n\n";
	}
	
	$msg = Text::make_blob("Shopping Results for '$search'", $blob);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>