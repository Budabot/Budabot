<?php

if (preg_match("/^fastattack ([0-9]*\\.?[0-9]+) ([0-9]+)$/i", $message, $arr)) {
	$AttTim = $arr[1];
	$fastSkill = $arr[2];
	
	list($fasthardcap, $fastskillcap) = cap_fast_attack($AttTim);

	$fastrech =  round(($AttTim * 16) - ($fastSkill / 100));

	if ($fastrech < $fasthardcap) {
		$fastrech = $fasthardcap;
	}

	$blob = "<header> :::::: Fast Attack Calculator :::::: <end>\n\n";
	$blob .= "Results:\n";
	$blob .= "Attack: <orange>". $AttTim ." <end>second(s).\n";
	$blob .= "Fast Atk Skill: <orange>". $fastSkill ."<end>\n";
	$blob .= "Fast Atk Recharge: <orange>". $fastrech ."<end>s\n";
	$blob .= "You need <orange>".$fastskillcap."<end> Fast Atk Skill to cap your fast attack at: <orange>".$fasthardcap."<end>s";

	$msg = Text::make_blob("::Your Fast Attack Results::", $blob);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
