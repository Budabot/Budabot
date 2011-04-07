<?php

if (preg_match("/^links$/i", $message)) {
	$blob = "<header> :::::: Links :::::: <end>\n\n";

	$sql = "SELECT * FROM links ORDER BY dt DESC";
  	$db->query($sql);
	$data = $db->fObject('all');
  	forEach ($data as $row) {
	  	$remove = Text::make_link('Remove', "/tell <myname> <symbol>links rem $row->id" , 'chatcmd');
		if (Setting::get('showfullurls') == 1) {
			$website = Text::make_link($row->website, "/start $row->website", 'chatcmd');
		} else {
			$website = Text::make_link('[Link]', "/start $row->website", 'chatcmd');
		}
		$dt = gmdate("M j, Y, G:i", $row->dt);
	  	$blob .= "$website <white>$row->comments<end> [<green>$row->name<end>] <white>$dt<end> $remove\n";
	}
	
	if (count($data) == 0) {
		$msg = "No links found.";
	} else {
		$msg = Text::make_link('Links', $blob, 'blob');
	}
  	
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^links add ([^ ]+) (.+)$/i", $message, $arr)) {
	$website = html_entity_decode(str_replace("'", "''", $arr[1]));
	$comments = str_replace("'", "''", $arr[2]);

	$db->query("INSERT INTO links (`name`, `website`, `comments`, `dt`) VALUES('$sender', '$website', '$comments', '" . time() . "')");
	$msg = "Link added successfully.";
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^links rem ([0-9]+)$/i", $message, $arr)) {
	$id = $arr[1];

	$numRows = $db->exec("DELETE FROM links WHERE id = $id AND name LIKE '$sender'");
	if ($numRows) {
		$msg = "Link deleted successfully.";
	} else {
		$msg = "Link could not be found or was not submitted by you.";
	}
    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
