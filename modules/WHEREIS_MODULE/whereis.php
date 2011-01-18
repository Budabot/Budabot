<?php
   /*
   Whereis Module Ver 1.1
   Written By Jaqueme
   For Budabot
   Database Adapted From One Originally 
   Compiled by Malosar For BeBot
   Whereis Database Module
   Written 5/11/07
   Last Modified 5/14/07
   */

$links = array("Help;chatcmd:///tell <myname> help whereis");

$msg = '';
if (preg_match("/^whereis (.+)$/i", $message, $arr)) {
	$search = $arr[1];
	$search = ucwords(strtolower($search));
	$db->query("SELECT * FROM whereis WHERE name LIKE '%".str_replace("'", "''", $search)."%'");
	$whereis_found = $db->numrows();
	$whereis = '';
	
	$data = $db->fobject("all");
	forEach ($data as $row) {
		$whereis .= "<yellow>$row->name \n <green>Can be found $row->answer\n";
	}
	
	if ($whereis_found > 1) {
		if (method_exists('bot', 'makeHeader')) {
			$header = bot::makeHeader("Result of Whereis Search For $search", $links);
		} else {
			$header = "<header>::::: Result of Whereis Search For $search :::::<end>\n";	
		}
		$header .= "There are $whereis_found matches to your query.\n\n";
		
		$whereis = $header . $whereis;
	
		$msg = bot::makelink("Whereis", $whereis);
	} else if ($whereis_found == 1) {
		$msg = $whereis;
	} else {
		$msg = "<yellow>There were no matches for your search.<end>";
	}
}
else {
	$msg = "<yellow>You must enter valid search criteria.<end>\n";
}

bot::send($msg , $sendto);

?>