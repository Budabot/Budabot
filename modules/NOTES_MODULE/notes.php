<?php

if (preg_match("/^notes$/i", $message)) {
	$blob = "<header> :::::: Notes for $sender :::::: <end>\n\n";

	$sql = "SELECT * FROM notes WHERE name LIKE '$sender'";
  	$db->query($sql);
	$data = $db->fObject('all');
  	forEach ($data as $row) {
	  	$remove = Text::make_chatcmd('Remove', "/tell <myname> <symbol>note rem $row->id");
	  	$blob .= "$remove $row->note\n\n";
	}
	
	if (count($data) == 0) {
		$msg = "No notes for $sender.";	
	} else {
		$msg = Text::make_blob("Notes for $sender", $blob);
	}
  	
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^notes (rem|add) (.*)$/i", $message, $arr)) {
	$action = strtolower($arr[1]);
	$parm2 = $arr[2];

	if ($action == 'rem') {
		$numRows = $db->exec("DELETE FROM notes WHERE id = $parm2 AND name LIKE '$sender'");
		
		if ($numRows) {
			$msg = "Note deleted successfully.";
		} else {
			$msg = "Note could not be found.";
		}
	} else if ($action == 'add') {
		$note = str_replace("'", "''", $parm2);

		$db->exec("INSERT INTO notes (name, note) VALUES('$sender', '$note')");
		$msg = "Note added successfully.";
	}
    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
