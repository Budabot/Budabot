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

	$sqlquery = "SELECT DISTINCT module FROM cmdcfg_<myname> WHERE `cmd` = '{$cmd}' OR `cmd` IN (SELECT cmd FROM cmd_alias_<myname> WHERE `alias` = '{$cmd}')";
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
		$blob .= Text::make_link($foundmodule.' configuration', '/tell <myname> config '.$foundmodule, 'chatcmd') . "\n";
	}

	if (count($data) == 0) {
		$msg = "No results found.";
	} else {
		$msg = Text::make_link(count($data) . ' results found', $blob, 'blob');
	}
	$chatBot->send($msg, $sendto);
}


?>