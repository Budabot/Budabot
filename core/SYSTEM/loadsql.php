<?php

if (preg_match("/^loadsql (.*) (.*)$/i", $message, $arr)) {
	$module = strtoupper($arr[1]);
	$name = strtolower($arr[2]);
	
	$db->begin_transaction();
	
	$msg = $db->loadSQLFile($module, $name, true);
	
	$db->commit();
	
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>