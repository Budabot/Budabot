<?php
	include 'utils.php';

	$skill_list 	= array(   1, 1000, 1001, 2000, 2001, 3000);
	$gen_dmg_list	= array(   1, 2000, 2001, 2500, 2501, 2850);
	$MA_rech_list 	= array(1800, 1800, 1188,  600,  600,  300);
	$MA_dmg_list	= array(   1, 2000, 2001, 2340, 2341, 2550);
	$shad_rech_list = array( 300,  300,  300,  300,  240,  200);
	$shad_dmg_list	= array(   1,  920,  921, 1872, 1873, 2750);
	$shad_rec_list	= array(  70,   70,   70,   75,   75,   80);
	$keep_heal_list = array(   1, 3000, 3001,10500,10501,30000);

	$header = "<header>::::: Dimach Calculator - Version 1.00 :::::<end>\n\n";
	$footer = "\n\nby Imoutochan, RK1";
	
	$help = $header;
	$help .= "<font color=#3333CC>Dimach Usage:</font>\n";
	$help .= "/tell <myname> <symbol>dimach [<orange>C<end>] [<orange>D<end>]\n";
	$help .= "[<orange>C<end>] = Class: MA, shade, keeper, other\n";
	$help .= "[<orange>D<end>] = Dimach Skill\n";
	$help .= "Example:\n";
	$help .= "You are a keeper have 750 dimach Skill.\n";
	$help .= "<a href='chatcmd:///tell <myname> <symbol>dimach keeper 750'>/tell <myname> <symbol>dimach keeper 750</a>\n\n";
	$help .= $footer;

	$helplink = $this->makeLink("::How to use Dimach::", $help);
			
	if (preg_match("/^dimach (ma|martial artist|keep|keeper|shad|shade|other|gen) ([0-9]+)$/i", $message, $arr)) {
		$dim_skill = trim($arr[2]);

		if ($dim_skill < 1001)
			$i = 0; 
		elseif ($dim_skill < 2001)
			$i = 2; 
		elseif ($dim_skill < 3001)
			$i = 4; 
		else { 
			$this->send("Skill entered is out of range... please enter a number between <highlight>1 and 3000<end>.",$sendto);
			return;
		}
		
		$class = strtolower(trim($arr[1]));
		switch ($class) {
			case "martial artist";
			case "ma";
				$MA_dmg 	 = interpolate($skill_list[$i], $skill_list[($i+1)], $MA_dmg_list[$i],  $MA_dmg_list[($i+1)],  $dim_skill);
				$MA_dim_rech = interpolate($skill_list[$i], $skill_list[($i+1)], $MA_rech_list[$i], $MA_rech_list[($i+1)], $dim_skill);
				$info = "Damage: <orange>".$MA_dmg."<end>-<orange>".$MA_dmg."<end>(<orange>1<end>)\n";
				$info .="Recharge ".timestamp($MA_dim_rech)."\n";
				$class_name = "Martial Artist";
			break;
			case "keep";
			case "keeper";
				$keep_heal 	= interpolate($skill_list[$i], $skill_list[($i+1)], $keep_heal_list[$i],$keep_heal_list[($i+1)], $dim_skill);
				$info = "Self heal: <font color=#ff9999>".$keep_heal."</font> HP\n";
				$info .= "Recharge: <orange>1<end> hour <font color=#ccccc>(constant)</font>\n";				
				$class_name = "Keeper";
			break;
			case "shad";
			case "shade";
				$shad_dmg 	= interpolate($skill_list[$i], $skill_list[($i+1)], $shad_dmg_list[$i], $shad_dmg_list[($i+1)],  $dim_skill);
				$shad_rec 	= interpolate($skill_list[$i], $skill_list[($i+1)], $shad_rec_list[$i], $shad_rec_list[($i+1)],  $dim_skill);
				$shad_dim_rech	= interpolate($skill_list[$i], $skill_list[($i+1)], $shad_rech_list[$i], $shad_rech_list[($i+1)], $dim_skill);
				$info = "Damage: <orange>".$shad_dmg."<end>-<orange>".$shad_dmg."<end>(<orange>1<end>)\n";
				$info .= "HP drain: <font color=#ff9999>".$shad_rec."</font>%\n";
				$info .= "Recharge ".timestamp($shad_dim_rech)."\n";				
				$class_name = "Shade";
			break;
			default;
				$gen_dmg = interpolate($skill_list[$i], $skill_list[($i+1)], $gen_dmg_list[$i],  $gen_dmg_list[($i+1)], $dim_skill);
				$info .= "Damage: <orange>".$gen_dmg."<end>-<orange>".$gen_dmg."<end>(<orange>1<end>)\n";
				$info .= "Recharge: <orange>30<end> minutes <font color=#ccccc>(constant)</font>\n";
				$class_name = "All classes besides MA, Shade and Keeper";
			break;
		}

		$inside = $header;
		$inside .= "<u>Results</u>:\n";
		$inside .= "Class: <orange>".$class_name."<end>\n";
		$inside .= "Dimach Skill: <orange>".$dim_skill."<end>\n";
		$inside .= $info;
		$inside .= $footer;
		
		$windowlink = $this->makeLink("::Your Dimach skill results::", $inside);
		$this->send($windowlink, $sendto);
	} else {
		$this->send($helplink, $sendto);
	}
?>