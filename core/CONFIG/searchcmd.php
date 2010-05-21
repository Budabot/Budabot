<?php
	/*
	 * This command will search for the specified
	 * command and will return a link to the
	 * module configuration containing the command
	 *
	 * Author: Mindrila (RK1)
	 * Date: 21.05.2010
	 */
global $db;
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
	
	foreach ($data as $row)
	{
		$foundmodule = strtoupper($row->module);
		$msg = "<highlight>".strtolower($arr[1]) . "<end> was found in <orange>" . $foundmodule . "<end>. ";
		$link = bot::makeLink($foundmodule.' configuration', '/tell <myname> config '.$foundmodule, 'chatcmd');
		bot::send($msg.$link,$sendto);
	}
}


?>