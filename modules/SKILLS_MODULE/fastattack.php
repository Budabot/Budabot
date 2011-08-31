<?php

if (preg_match("/^fastattack ([0-9]*\.?[0-9]+) ([0-9]+)$/i", $message, $arr)) {
	$AttTim = trim($arr[1]);
	$fastSkill = trim($arr[2]);
	
	list($fasthardcap, $fastskillcap) = cap_fast_attack($AttTim);

	$fastrech =  round(($AttTim * 16) - ($fastSkill / 100));

	if ($fastrech < $fasthardcap) {
		$fastrech = $fasthardcap;
	}

	$inside = "<header> :::::: Fast Attack Calculator :::::: <end>\n\n";
	$inside .= "Results:\n";
	$inside	.= "Attack: <orange>". $AttTim ." <end>second(s).\n";
	$inside	.= "Fast Atk Skill: <orange>". $fastSkill ."<end>\n";
	$inside	.= "Fast Atk Recharge:<orange> ". $fastrech ."<end>s\n";
	$inside	.= "You need <orange>".$fastskillcap."<end> Fast Atk Skill to cap your fast attack at: <orange>".$fasthardcap."<end>s";

	$windowlink = Text::make_blob("::Your Fast Attack Results::", $inside);
	$chatBot->send($windowlink, $sendto);
} else {
	$syntax_error = true;
}