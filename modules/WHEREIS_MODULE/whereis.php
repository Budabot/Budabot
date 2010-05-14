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

$output = '';
if (preg_match("/^whereis (.+)$/i", $message, $arr)) {
	$search = $arr[1];
	$search = ucwords(strtolower($search));
	$db->query("SELECT * FROM whereis WHERE name LIKE '%$search%'");
	$whereis_found = $db->numrows();
	$whereis = '';
	if (method_exists('bot', 'makeHeader')) {
		$whereis = bot::makeHeader("Result of Whereis Search For $search", $links);
	} else {
		$whereis = "<header>::::: Result of Whereis Search For $search :::::<end>\n";	
	}
	$whereis .= "There are $whereis_found matches to your query.\n\n";
	
	$data = $db->fobject("all");
	foreach($data as $row)
	{
		$whereis .= "<yellow>$row->name \n <green>Can be found $row->answer\n";
	}
	
	if ($db->numrows() > 0) {
		$output = bot::makelink("Whereis", $whereis);
	} else {
		$output .= "<yellow>There were no matches for your search.</end>";
	}
}
else {
	$output .= "<yellow>You must enter valid search criteria.</end>\n";
}

bot::send($output, $sendto);

?>