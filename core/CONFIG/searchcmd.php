<?php
	/*
	 * This command will search for the specified
	 * command and will return a link to the
	 * module configuration containing the command
	 *
	 * Author: Mindrila (RK1)
	 * Date: 21.05.2010
	 */

if (preg_match("/^searchcmd (.*)/i", $message, $arr))
{
	$sqlquery = "SELECT DISTINCT module FROM cmdcfg_<myname> WHERE `cmd` = '".strtolower($arr[1])."' ;";
	$db->query($sqlquery);
	
	if ( 0 == $db->numrows())
	{
		$msg = "<highlight>".strtolower($arr[1])."<end> could not be found.";
		bot::send($msg,$sendto);
		return;
	}
	
	$data = $db->fObject("all");
	$blob = '';
	$msg = '';
	foreach ($data as $row)
	{
		$foundmodule = strtoupper($row->module);
		$blob .= bot::makeLink($foundmodule.' configuration', '/tell <myname> config '.$foundmodule, 'chatcmd') . "\n";
	}
	if (count($data) == 0)
	{
		$msg = "No results found.";
	}
	else
	{
		$msg = bot::makeLink(count($data) . ' results found.', $blob);
	}
	bot::send($msg, $sendto);
}


?>