<?php

if (preg_match("/^playfields$/i", $message)) {
	$blob = '';
	
	$sql = "SELECT * FROM playfields ORDER BY long_name";
	$data = $db->query($sql);
	forEach ($data as $row) {
		$blob .= "{$row->id}   <green>{$row->long_name}<end>   <cyan>({$row->short_name})<end>\n";
	}
	
	$msg = Text::make_blob("Playfields", $blob);
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>