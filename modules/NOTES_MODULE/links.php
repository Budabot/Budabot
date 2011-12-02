<?php

if (preg_match("/^links$/i", $message)) {
	$blob = "<header> :::::: Links :::::: <end>\n\n";

	$sql = "SELECT * FROM links ORDER BY name ASC";
  	$data = $db->query($sql);
  	forEach ($data as $row) {
	  	$remove = Text::make_chatcmd('Remove', "/tell <myname> <symbol>links rem $row->id");
		if (Setting::get('showfullurls') == 1) {
			$website = Text::make_chatcmd($row->website, "/start $row->website");
		} else {
			$website = Text::make_chatcmd('[Link]', "/start $row->website");
		}
	  	$blob .= "$website <white>$row->comments<end> [<green>$row->name<end>] $remove\n";
	}
	
	if (count($data) == 0) {
		$msg = "No links found.";
	} else {
		$msg = Text::make_blob('Links', $blob);
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
