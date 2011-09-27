<?php

if (preg_match("/^shop (.+)$/i", $message, $arr)) {
	$search = $arr[1];
	$sql = "
		SELECT DISTINCT
			s2.sender,
			s2.message,
			MAX(dt) as dt
		FROM
			shopping_items s1
			JOIN shopping_messages s2
				ON s1.message_id = s2.id
		WHERE
			s2.dimension = <dim>
			AND s1.name LIKE '%" . str_replace("'", "''", $search) . "%'";
	$db->query($sql);
	$data = $db->fObject('all');
	
	$blob = "<header> :::::: Shopping Results for '$search' :::::: <end>\n\n";
	forEach ($data as $row) {
		$senderLink = Text::make_userlink($row->sender);
		$timeString = Util::unixtime_to_readable(time()- $row->dt, false);
		$blob .= "[$senderLink]: {$row->message} - <highlight>($timeString ago)<end>\n";
	}
	
	$msg = Text::make_blob("Shopping Results for '$search'", $blob);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>