<?php
/*
Written by Jaqueme
For Budabot
5/11/07
*/

if (preg_match("/^research ([0-9]+)$/i",$message, $arr)) {
	$level = $arr[1];
	if ($level < 1 OR $level > 10) {
		$msg .= "<orange>Invalid Research Level Input. Valid reserch levels are from 1-10.<end>";
	} else {
		$sql = "SELECT * FROM research WHERE level = ?";
		$row = $db->queryRow($sql, $level);
		
		$levelcap = $row->levelcap;
		$sk = $row->sk;
		$xp = $sk * 1000;
		$capxp = round($xp * .04);
		$capsk = round($sk * .04);
		$xp = number_format($xp);
		$sk = number_format($sk);
		
		$blob = "<header> ::::: XP/SK Needed for Research Levels ::::: <end>\n\n";
		$blob .= "<green>You must be <blue>Level $levelcap<end> to reach <blue>Research Level $level<end>.\n";
		$blob .= "You need <blue>$sk SK<end> to reach <blue>Research Level $level<end> per research line.\n\n";
		$blob .= "This equals <orange>$xp XP<end>.\n\n";
		$blob .= "Your research will cap at <yellow>~$capxp XP<end> or <yellow>~$capsk SK<end>.";
		$msg = Text::make_blob("Research", $blob);
	}
	
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^research ([0-9]+) ([0-9]+)$/i", $message, $arr)) {
	$lolevel = $arr[1];
	$hilevel = $arr[2];
	if ($lolevel < 0 OR $lolevel > 10 OR $hilevel < 0 OR $hilevel > 10) {
		$research .= "<orange>Invalid Research Level Input. Valid reserch levels are from 0-10.<end>";
	} else {
		$sql = 
			"SELECT 
				SUM(sk) totalsk,
				MAX(levelcap) levelcap
			FROM
				research
			WHERE
				level > ? AND level <= ?";
		$row = $db->queryRow($sql, $lolevel, $hilevel);
		
		$xp = number_format($row->totalsk * 1000);
		$sk = number_format($row->totalsk);
		
		$blob = "<header> ::::: XP/SK Needed for Research Levels ::::: <end>\n\n";
		$blob .= "<green>You must be <blue>Level $row->levelcap<end> to reach Research Level <blue>$hilevel.<end>\n";
		$blob .= "It takes <blue>$sk SK<end> to go from Research Level <blue>$lolevel<end> to Research Level <blue>$hilevel<end> per research line.\n\n";
		$blob .= "This equals <orange>$xp XP<end>.";
		$msg = Text::make_blob("Research", $blob);
	}
	
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>