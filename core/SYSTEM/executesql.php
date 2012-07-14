<?php

$accessLevel = Registry::getInstance('accessLevel');

if (preg_match("/^executesql (.*)$/i", $message, $arr)) {
	if (!$accessLevel->checkAccess($sender, 'superadmin')) {
		$msg = "This command may only be used by the super administrator.";
		$sendto->reply($msg);
		return;
	}

	$sql = htmlspecialchars_decode($arr[1]);

	$num_rows = $db->exec($sql);
	$msg = "$num_rows rows affected.";
	$sendto->reply($msg);
} else if (preg_match("/^querysql (.*)$/i", $message, $arr)) {
	if (!$accessLevel->checkAccess($sender, 'superadmin')) {
		$msg = "This command may only be used by the super administrator.";
		$sendto->reply($msg);
		return;
	}

	$sql = htmlspecialchars_decode($arr[1]);

	$data = $db->query($sql);

	if ($data === null) {
		$msg = "There was en error executing your query.";
	} else {
		$msg = Text::make_blob("Result", print_r($data, true));
	}
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
