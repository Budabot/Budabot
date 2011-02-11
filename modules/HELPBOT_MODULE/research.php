<?php
/*
Written by Jaqueme
For Budabot
5/11/07
*/

if (preg_match("/^research ([0-9]+)$/i",$message, $arr)) {
	$level = $arr[1];
	if ($level < 1 OR $level > 10) {
		$research .= "<orange>Invalid Research Level Input. Valid reserch levels are from 1-10.<end>";
	} else {
		$sql = "SELECT * FROM research WHERE level = $level";
		$db->query($sql);
		$row = $db->fObject();
		
		$levelcap = $row->levelcap;
		$sk = $row->sk;
		$xp = $sk * 1000;
		$capxp = round($xp * .04);
		$capsk = round($sk * .04);
		$xp = number_format($xp);
		$sk = number_format($sk);
		$research = "<header>  ::::: XP/SK NEEDED FOR RESEARCH LEVELS  :::::<end>\n\n";
		$research .= "<green>You must be <blue>Level $levelcap<end> to reach <blue>Research Level $level<end>.\n";
		$research .= "You need <blue>$sk SK<end> to reach <blue>Research Level $level<end>.\n\n";
		$research .= "This equals <range>$xp XP.<end>\n\n";
		$research .= "Your research will cap at <yellow>$capxp<end> XP or <yellow>$capsk<end> SK.";
		$research = Text::make_link("Research", $research);
	}	
} else if (preg_match("/^research ([0-9]+) ([0-9]+)$/i", $message, $arr)) {
	$lolevel = $arr[1];
	$hilevel = $arr[2];
	if ($lolevel < 1 OR $lolevel > 10 OR $hilevel < 1 OR $hilevel > 10) {
		$research .= "<orange>Invalid Research Level Input. Valid reserch levels are from 1-10.<end>";
	} else {
		$sql = 
			"SELECT 
				r1.level lolevel,
				r1.sk losk,
				r1.levelcap lolevelcap,
				r2.level hilevel,
				r2.sk hisk,
				r2.levelcap hilevelcap
			FROM
				research r1,
				research r2
			WHERE
				r1.level = $lolevel AND r2.level = $hilevel";
		$db->query($sql);
		$row = $db->fobject();
		$range = $row->hisk - $row->losk;
		$xp = number_format($range * 1000);
		$range = number_format($range);
		$research = "<header>  ::::: XP/SK NEEDED FOR RESEARCH LEVELS  :::::<end>\n\n";
		$research .= "<green>You must be <blue>Level $row->hilevelcap<end> to reach <blue>Research Level $row->hilevel.<end>\n";
		$research .= "It takes <blue>$range SK<end> to go from <blue>Research Level $row->lolevel<end> to <blue>Research Level $row->hilevel<end>.\n\n";
		$research .= "This equals <orange>$xp XP.<end>";
		$research = Text::make_link("Research", $research);
	}
} else {
	$syntax_error = true;
	return;
}	

$chatBot->send($research, $sendto);

?>