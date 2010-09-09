<?php
	$skill_list = array( 1, 1000, 1001, 2000, 2001, 3000);
	$min_list 	= array( 1,  100,  101,  170,  171,  235);
	$max_list 	= array( 2,  500,  501,  850,  851, 1145);
	$crit_list 	= array( 3,  500,  501,  600,  601,  725);

	if (preg_match("/^brawl ([0-9]+)$/i", $message, $arr)) {
		$brawl_skill = trim($arr[1]);

		if ($brawl_skill < 1001) {
			$i = 0; 
		} else if ($brawl_skill < 2001)
			$i = 2; 
		} else if ($brawl_skill < 3001)
			$i = 4; 
		} else { 
			bot::send("Skill entered is out of range... please enter a number between <highlight>1 and 3000<end>.",$sendto);
			return;
		}
		
		$min  = interpolate($skill_list[$i], $skill_list[($i+1)], $min_list[$i], $min_list[($i+1)], $brawl_skill);
		$max  = interpolate($skill_list[$i], $skill_list[($i+1)], $max_list[$i], $max_list[($i+1)], $brawl_skill);
		$crit = interpolate($skill_list[$i], $skill_list[($i+1)], $crit_list[$i], $crit_list[($i+1)], $brawl_skill);
		$stunC = (($brawl_skill < 1000) ? "<orange>10<end>%, <font color=#cccccc>will become </font>20<font color=#cccccc>% above </font>1000<font color=#cccccc> brawl skill</font>" : "<orange>20<end>%");
		$stunD = (($brawl_skill < 2001) ?  "<orange>3<end>s, <font color=#cccccc>will become </font>4<font color=#cccccc>s above </font>2001<font color=#cccccc> brawl skill</font>" :  "<orange>4<end>s");

		$inside = "<header>::::: Brawl Calculator - Version 1.00 :::::<end>\n\n";
		$inside .= "<u>Results</u>:\n";
		$inside .= "Brawl Skill: <orange>".$brawl_skill."<end>\n";
		$inside .= "Brawl recharge: <orange>15<end> seconds <font color=#ccccc>(constant)</font>\n";
		$inside .= "Damage: <orange>".$min."<end>-<orange>".$max."<end>(<orange>".$crit."<end>)\n";
		$inside .= "Stun chance: ".$stunC."\n";
		$inside .= "Stun duration: ".$stunD."\n";
		$inside .= "\n\nby Imoutochan, RK1";
		
		$windowlink = bot::makeLink("::Your Brawl skill results::", $inside);
		bot::send($windowlink, $sendto);
	} else {
		$syntax_error = true;
	}
?>