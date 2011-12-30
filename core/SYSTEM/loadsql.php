<?php

if (preg_match("/^loadsql (.*) (.*)$/i", $message, $arr)) {
	$module = strtoupper($arr[1]);
	$name = strtolower($arr[2]);
	
	$db->begin_transaction();
	
	$msg = $db->loadSQLFile($module, $name, true);
	
	$db->commit();
	
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>