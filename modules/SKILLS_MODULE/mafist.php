<?php

if (preg_match("/^mafist ([0-9]+)$/i", $message, $arr)) {
	$MaSkill = $arr[1];
	
	// MA templates
	$skill_list = array(1,200,1000,1001,2000,2001,3000);
	
	$MA_min_list = array (3,25,90,91,203,204,425);
	$MA_max_list = array (5,60,380,381,830,831,1280);
	$MA_crit_list = array(3,50,500,501,560,561,770);

	$shade_min_list = array (3,25,55,56,130,131,280);
	$shade_max_list = array (5,60,258,259,682,683,890);
	$shade_crit_list = array(3,50,250,251,275,276,300);

	$gen_min_list = array (3,25,65,66,140,141,300);
	$gen_max_list = array (5,60,280,281,715,716,990);
	$gen_crit_list = array(3,50,500,501,605,605,630);
	
	if ($MaSkill < 200) {
		$i = 0; 
	} else if ($MaSkill < 1001) {
		$i = 1; 
	} else if ($MaSkill < 2001) {
		$i = 3; 
	} else if ($MaSkill < 3001) {
		$i = 5; 
	} else { 
		$chatBot->send("Skill entered is out of range... please enter a number between <highlight>1 and 3000<end>.",$sendto);
		return;
	}
	
	$fistql = round($MaSkill/2,0);
	if ($fistql <= 200) {
		$speed = 1.25;
	} else if ($fistql <= 500) {
		$speed = 1.25 + (0.2*(($fistql-200)/300));
	} else if ($fistql <= 1000)	{
		$speed = 1.45 + (0.2*(($fistql-500)/500));  
	} else if ($fistql <= 1500)	{
		$speed = 1.65 + (0.2*(($fistql-1000)/500));
	}
	$speed = round($speed,2);
	
	$blob = "<header> :::::: MA Fist Calculator :::::: <end>\n\n";
	$blob	.= "MA Skill: <orange>". $MaSkill ."<end>\n\n";
	$blob .= "<u>Results</u>:\n\n";
	
	$blob .= "Fist speed: <orange>".$speed."<end>s/<orange>".$speed."<end>s\n\n";
	
	$min = interpolate($skill_list[$i], $skill_list[($i + 1)], $MA_min_list[$i], $MA_min_list[($i + 1)], $MaSkill);
	$max = interpolate($skill_list[$i], $skill_list[($i + 1)], $MA_max_list[$i], $MA_max_list[($i + 1)], $MaSkill);
	$crit = interpolate($skill_list[$i], $skill_list[($i + 1)], $MA_crit_list[$i], $MA_crit_list[($i + 1)], $MaSkill);
	$dmg = "<orange>".$min."<end>-<orange>".$max."<end>(<orange>".$crit."<end>)";
	$blob .= "Class: <orange>Martial Artist<end>\n";
	$blob .= "Fist damage: ".$dmg."\n\n";

	$min = interpolate($skill_list[$i], $skill_list[($i + 1)], $shade_min_list[$i], $shade_min_list[($i + 1)], $MaSkill);
	$max = interpolate($skill_list[$i], $skill_list[($i + 1)], $shade_max_list[$i], $shade_max_list[($i + 1)], $MaSkill);
	$crit = interpolate($skill_list[$i], $skill_list[($i + 1)], $shade_crit_list[$i], $shade_crit_list[($i + 1)], $MaSkill);
	$dmg = "<orange>".$min."<end>-<orange>".$max."<end>(<orange>".$crit."<end>)";
	$blob .= "Class: <orange>Shade<end>\n";
	$blob .= "Fist damage: ".$dmg."\n\n";

	$min = interpolate($skill_list[$i], $skill_list[($i + 1)], $gen_min_list[$i], $gen_min_list[($i + 1)], $MaSkill);
	$max = interpolate($skill_list[$i], $skill_list[($i + 1)], $gen_max_list[$i], $gen_max_list[($i + 1)], $MaSkill);
	$crit = interpolate($skill_list[$i], $skill_list[($i + 1)], $gen_crit_list[$i], $gen_crit_list[($i + 1)], $MaSkill);
	$dmg = "<orange>".$min."<end>-<orange>".$max."<end>(<orange>".$crit."<end>)";
	$blob .= "Class: <orange>All classes besides MA and Shade<end>\n";
	$blob .= "Fist damage: ".$dmg."\n\n";

	$msg = Text::make_blob("::Your MA skill results::", $blob);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
