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

$links = array("Help" => "/tell <myname> help whereis");

$msg = '';
if (preg_match("/^whereis (.+)$/i", $message, $arr)) {
	$search = $arr[1];
	$search = strtolower($search);
	$db->query("SELECT * FROM whereis WHERE name LIKE '%".str_replace("'", "''", $search)."%'");
	$data = $db->fobject("all");
	$count = count($data);
	
	if ($count > 1) {
		$blob = Text::make_header("Result of Whereis Search for '$search'", $links);
		$blob .= "There are $count matches to your query.\n\n";
		forEach ($data as $row) {
			$blob .= "<yellow>$row->name<end>\n<green>Can be found $row->answer<end>\n\n";
		}
		
		$msg = Text::make_blob("Whereis ($count)", $blob);
	} else if ($count == 1) {
		$row = $data[0];
		$msg = "<yellow>$row->name<end>\n<green>Can be found $row->answer<end>";
	} else {
		$msg = "There were no matches for your search.";
	}
	$chatBot->send($msg , $sendto);
} else {
	$syntax_error = true;
}

?>