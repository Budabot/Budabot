<?php
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

	if (preg_match("/^mafist ([a-z]+) ([0-9]+)$/i", $message, $arr)) {
		$MaSkill = trim($arr[2]);
		
		if ($MaSkill < 200) {
			$i = 0; 
		} else if ($MaSkill < 1001) {
			$i = 1; 
		} else if ($MaSkill < 2001) {
			$i = 3; 
		} else if ($MaSkill < 3001) {
			$i = 5; 
		} else { 
			bot::send("Skill entered is out of range... please enter a number between <highlight>1 and 3000<end>.",$sendto);
			return;
		}
		
		$class = substr(strtolower(trim($arr[1])), 0, 2);
		switch ($class) {
			case "ma":
				$min_list = $MA_min_list; $max_list = $MA_max_list; $crit_list = $MA_crit_list; $class_name = "Martial Artist";
			break;
			case "sh":
				$min_list = $shade_min_list; $max_list = $shade_max_list; $crit_list = $shade_crit_list; $class_name = "Shade";
			break;
			default:
				$min_list = $gen_min_list; $max_list = $gen_max_list; $crit_list = $gen_crit_list; $class_name = "All classes besides MA and Shade";
			break;
		}

		$min = interpolate($skill_list[$i], $skill_list[($i + 1)], $min_list[$i], $min_list[($i + 1)], $MaSkill);
		$max = interpolate($skill_list[$i], $skill_list[($i + 1)], $max_list[$i], $max_list[($i + 1)], $MaSkill);
		$crit = interpolate($skill_list[$i], $skill_list[($i + 1)], $crit_list[$i], $crit_list[($i + 1)], $MaSkill);
		$dmg = "<orange>".$min."<end>-<orange>".$max."<end>(<orange>".$crit."<end>)";		

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
		
		$inside = "<header>::::: MA Fist Calculator - Version 1.00 :::::<end>\n\n";
		$inside .= "<u>Results</u>:\n";
		$inside .= "Class: <orange>".$class_name."<end>\n";
		$inside	.= "MA Skill: <orange>". $MaSkill ."<end>\n";
		$inside	.= "Fist damage: ".$dmg."\n";
		$inside .= "Fist speed: <orange>".$speed."<end>s/<orange>".$speed."<end>s\n";
		
		$windowlink = Text::make_link("::Your MA skill results::", $inside);
		bot::send($windowlink, $sendto);
		
	} else {
		$syntax_error = true;
	}
?>