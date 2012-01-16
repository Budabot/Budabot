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

	if ($num_rows === false) {
		$msg = "There was en error executing your query: {$db->errorInfo[2]}";
	} else {
		$msg = "$num_rows rows affected.";
	}
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>