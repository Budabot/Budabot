<?php
   /*
   Dynacamp Module Ver 2.0
   Written By Jaqueme
   For Budabot
   Database Adapted From IGN Module Written By Drevil1
   RK Dynacamp Database Module
   Written 5/11/07
   Last Modified 5/27/07
   */

$links = array("Help;chatcmd:///tell <myname> help dyna");

$dynacamps = '';
if (preg_match ("/^dyna ([0-2]?[0-9]?[0-9])$/i", $message, $arr)) {
	$search = str_replace(" ", "%", $arr[1]);
	$range1 = $search - 25;
	$range2 = $search + 25;
	$db->query("SELECT * FROM dynadb Where minQl > $range1 AND minQl < $range2 GROUP BY `zone` ORDER BY `minQl`");
	$dyna_found = $db->numrows();

	$dynacamps = Text::make_header("Results Of Dynacamp Search For $search", $links);

	$dynacamps .= "There are $dyna_found locations matching your query\n\n";
	$data = $db->fObject("all");
	foreach($data as $row) {
		$dynacamps .="<yellow>$row->zone:  Co-ordinates <blue>$row->cX<yellow>x<blue>$row->cY<end>\n";
		$dynacamps .="<green>Mob Type:  $row->mob\n";
		$dynacamps .="<blue>Level:  $row->minQl<yellow>-<blue>$row->maxQl\n\n";
	}
	
	$dynacamps = bot::makeLink("Dynacamps", $dynacamps);
	bot::send($dynacamps, $sendto);
} elseif (preg_match ("/^dyna (.+)$/i", $message, $arr)) {
	$search = str_replace(" ", "%", $arr[1]);
	$search = ucfirst(strtolower($search));
	$search = str_replace("'", "''", $arr[1]);
	$db->query("SELECT * FROM dynadb Where zone like '%$search%' OR mob = '$search' ORDER BY `minQl`");
	$dyna_found = $db->numrows();

	$dynacamps = Text::make_header("Results Of Dynacamp Search For $search", $links);

	$dynacamps .= "There are $dyna_found locations matching your query\n\n";
	$data = $db->fObject("all");
	foreach($data as $row) {
		$dynacamps .="<yellow>$row->zone:  Co-ordinates <blue>$row->cX<yellow>x<blue>$row->cY<end>\n";
		$dynacamps .="<green>Mob Type:  $row->mob\n";
		$dynacamps .="<blue>Level: $row->minQl<yellow>-<blue>$row->maxQl\n\n";
	}
	
	$dynacamps = bot::makeLink("Dynacamps", $dynacamps);
	bot::send($dynacamps, $sendto);
} else {
	$syntax_error = true;
}

?>