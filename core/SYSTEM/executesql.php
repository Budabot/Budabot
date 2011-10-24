<?php

if (preg_match("/^executesql (.*)$/i", $message, $arr)) {
	if (!AccessLevel::check_access($sender, 'superadmin')) {
		$msg = "This command may only be used by the super administrator.";
		$chatBot->send($msg, $sendto);
		return;
	}

	$sql = htmlspecialchars_decode($arr[1]);
	
	$num_rows = $db->exec($sql);

	if ($num_rows === false) {
		$msg = "There was en error executing your query: {$db->errorInfo[2]}";
	} else {
		$msg = "$num_rows rows affected.";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>