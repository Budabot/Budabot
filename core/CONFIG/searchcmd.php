<?php
	/*
	 * This command will search for the specified
	 * command and will return a link to the
	 * module configuration containing the command
	 *
	 * Author: Mindrila (RK1)
	 * Date: 21.05.2010
	 */

if (preg_match("/^searchcmd (.*)/i", $message, $arr)) {
	$cmd = strtolower($arr[1]);
	
	$alias_cmd = CommandAlias::get_command_by_alias($cmd);
	if ($alias_cmd != null) {
		$cmd = $alias_cmd;
	}

	$sqlquery = "SELECT DISTINCT module FROM cmdcfg_<myname> WHERE `cmd` = '{$cmd}'";
	$db->query($sqlquery);
	
	if (0 == $db->numrows()) {
		$msg = "<highlight>{$cmd}<end> could not be found.";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	$data = $db->fObject("all");
	$blob = "<header> :::::: Command Search Results :::::: <end>\n\n";
	forEach ($data as $row) {
		$foundmodule = strtoupper($row->module);
		$blob .= Text::make_chatcmd($foundmodule.' configuration', '/tell <myname> config '.$foundmodule) . "\n";
	}

	if (count($data) == 0) {
		$msg = "No results found.";
	} else {
		$msg = Text::make_blob(count($data) . ' results found', $blob);
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>