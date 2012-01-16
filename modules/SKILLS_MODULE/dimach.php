<?php

if (preg_match("/^dimach ([0-9]+)$/i", $message, $arr)) {
	$dim_skill = $arr[1];
	
	$skill_list 	= array(   1, 1000, 1001, 2000, 2001, 3000);
	$gen_dmg_list	= array(   1, 2000, 2001, 2500, 2501, 2850);
	$MA_rech_list 	= array(1800, 1800, 1188,  600,  600,  300);
	$MA_dmg_list	= array(   1, 2000, 2001, 2340, 2341, 2550);
	$shad_rech_list = array( 300,  300,  300,  300,  240,  200);
	$shad_dmg_list	= array(   1,  920,  921, 1872, 1873, 2750);
	$shad_rec_list	= array(  70,   70,   70,   75,   75,   80);
	$keep_heal_list = array(   1, 3000, 3001,10500,10501,30000);

	if ($dim_skill < 1001) {
		$i = 0; 
	} elseif ($dim_skill < 2001) {
		$i = 2; 
	} elseif ($dim_skill < 3001) {
		$i = 4; 
	} else { 
		$sendto->reply("Skill entered is out of range... please enter a number between <highlight>1 and 3000<end>.");
		return;
	}
	
	$blob = "<u>Results</u>:\n";
	$blob .= "Dimach Skill: <orange>".$dim_skill."<end>\n\n";
	
	$MA_dmg 	 = interpolate($skill_list[$i], $skill_list[($i+1)], $MA_dmg_list[$i],  $MA_dmg_list[($i+1)],  $dim_skill);
	$MA_dim_rech = interpolate($skill_list[$i], $skill_list[($i+1)], $MA_rech_list[$i], $MA_rech_list[($i+1)], $dim_skill);
	$blob .= "Class: <orange>Martial Artist<end>\n";
	$blob .= "Damage: <orange>".$MA_dmg."<end>-<orange>".$MA_dmg."<end>(<orange>1<end>)\n";
	$blob .= "Recharge ".timestamp($MA_dim_rech)."\n\n";

	$keep_heal 	= interpolate($skill_list[$i], $skill_list[($i+1)], $keep_heal_list[$i],$keep_heal_list[($i+1)], $dim_skill);
	$blob .= "Class: <orange>Keeper<end>\n";
	$blob .= "Self heal: <font color=#ff9999>".$keep_heal."</font> HP\n";
	$blob .= "Recharge: <orange>1<end> hour <font color=#ccccc>(constant)</font>\n\n";

	$shad_dmg 	= interpolate($skill_list[$i], $skill_list[($i+1)], $shad_dmg_list[$i], $shad_dmg_list[($i+1)],  $dim_skill);
	$shad_rec 	= interpolate($skill_list[$i], $skill_list[($i+1)], $shad_rec_list[$i], $shad_rec_list[($i+1)],  $dim_skill);
	$shad_dim_rech	= interpolate($skill_list[$i], $skill_list[($i+1)], $shad_rech_list[$i], $shad_rech_list[($i+1)], $dim_skill);
	$blob .= "Class: <orange>Shade<end>\n";
	$blob .= "Damage: <orange>".$shad_dmg."<end>-<orange>".$shad_dmg."<end>(<orange>1<end>)\n";
	$blob .= "HP drain: <font color=#ff9999>".$shad_rec."</font>%\n";
	$blob .= "Recharge ".timestamp($shad_dim_rech)."\n\n";

	$gen_dmg = interpolate($skill_list[$i], $skill_list[($i+1)], $gen_dmg_list[$i],  $gen_dmg_list[($i+1)], $dim_skill);
	$blob .= "Class: <orange>All classes besides MA, Shade and Keeper<end>\n";
	$blob .= "Damage: <orange>".$gen_dmg."<end>-<orange>".$gen_dmg."<end>(<orange>1<end>)\n";
	$blob .= "Recharge: <orange>30<end> minutes <font color=#ccccc>(constant)</font>\n\n";

	$blob .= "by Imoutochan, RK1";
	
	$msg = Text::make_blob("Dimach Results", $blob);
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
