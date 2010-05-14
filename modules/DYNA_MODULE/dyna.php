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
	if (ereg ("^dyna ([0-2]?[0-9]?[0-9])$", $message, $arr)) {
		$search = str_replace(" ", "%", $arr[1]);
		$range1 = $search - 25;
		$range2 = $search + 25;
		$db->query("SELECT * FROM dynadb Where minQl > $range1 AND minQl < $range2 GROUP BY `zone` ORDER BY `minQl`");
		$dyna_found = $db->numrows();
		$dynacamps = '';
		if (method_exists('bot', 'makeHeader')) {
			$dynacamps = bot::makeHeader("Results Of Dynacamp Search For $search", $links);
		} else {
			$dynacamps = "<header>::::: Results Of Dynacamp Search For $search :::::<end>\n";	
		}
		$dynacamps .= "There are $dyna_found locations matching your query\n\n";
		$data = $db->fObject("all");
		foreach($data as $row) {
			$dynacamps .="<yellow>$row->zone:  Co-ordinates <blue>$row->cX<yellow>x<blue>$row->cY<end>\n";
			$dynacamps .="<green>Mob Type:  $row->mob\n";
			$dynacamps .="<blue>Level:  $row->minQl<yellow>-<blue>$row->maxQl\n\n";
		}
		
		$dynacamps = bot::makeLink("Dynacamps", $dynacamps);
	} elseif (ereg ("^dyna (.+)$", $message, $arr)) {
		$search = str_replace(" ", "%", $arr[1]);
		$search = ucfirst(strtolower($search));
		$db->query("SELECT * FROM dynadb Where zone like '%$search%' OR mob = '$search' ORDER BY `minQl`");
		$dyna_found = $db->numrows();
		$dynacamps = '';
		if (method_exists('bot', 'makeHeader')) {
			$dynacamps = bot::makeHeader("Results Of Dynacamp Search For $search", $links);
		} else {
			$dynacamps = "<header>::::: Results Of Dynacamp Search For $search :::::<end>\n";	
		}
		$dynacamps .= "There are $dyna_found locations matching your query\n\n";
		$data = $db->fObject("all");
		foreach($data as $row) {
			$dynacamps .="<yellow>$row->zone:  Co-ordinates <blue>$row->cX<yellow>x<blue>$row->cY<end>\n";
			$dynacamps .="<green>Mob Type:  $row->mob\n";
			$dynacamps .="<blue>Level: $row->minQl<yellow>-<blue>$row->maxQl\n\n";   
		}
		
		$dynacamps = bot::makeLink("Dynacamps", $dynacamps);
	} else {
		$dynacamps = "<red>Could not locate a Dynacamp related to information provided.<end>";
	}

	bot::send($dynacamps, $sendto);

?>