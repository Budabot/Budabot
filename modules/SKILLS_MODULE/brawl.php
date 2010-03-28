<?php
	include 'utils.php';

	$skill_list = array( 1, 1000, 1001, 2000, 2001, 3000);
	$min_list 	= array( 1,  100,  101,  170,  171,  235);
	$max_list 	= array( 2,  500,  501,  850,  851, 1145);
	$crit_list 	= array( 3,  500,  501,  600,  601,  725);
	
	if($type == "msg")
		$sendto = $sender;
	elseif($type == "priv")
		$sendto = "";
	elseif($type == "guild")
		$sendto = "guild";

	$header = "<header>::::: Brawl Calculator - Version 1.00 :::::<end>\n\n";
	$footer = "\n\nby Imoutochan, RK1";
	
	$help = $header;
	$help .= "<font color=#3333CC>Brawl Usage:</font>\n";
	$help .= "/tell <myname> <symbol>Brawl [<orange>B<end>]\n";
	$help .= "[<orange>B<end>] = Brawl Skill\n";
	$help .= "Example:\n";
	$help .= "You have 750 brawl Skill.\n";
	$help .= "<a href='chatcmd:///tell <myname> <symbol>brawl 750'>/tell <myname> <symbol>brawl MA 750</a>\n\n";
	$help .= $footer;

	$helplink = bot::makeLink("::How to use brawl::", $help);
	
	if (!function_exists(interpolate)){
		function interpolate($x1, $x2, $y1, $y2, $x) {
			$result = ($y2 - $y1)/($x2 - $x1) * ($x - $x1) + $y1;
			$result = round($result,0);
			return $result;
		}
	}
				
	if (eregi("^brawl ([0-9]+)$", $message, $arr)) {
		$brawl_skill = trim($arr[1]);

		if ($brawl_skill < 1001)
			$i = 0; 
		elseif ($brawl_skill < 2001)
			$i = 2; 
		elseif ($brawl_skill < 3001)
			$i = 4; 
		else { 
			bot::send("Skill entered is out of range... please enter a number between <highlight>1 and 3000<end>.",$sendto);
			return;
		}
		
		$min  = interpolate($skill_list[$i], $skill_list[($i+1)], $min_list[$i], $min_list[($i+1)], $brawl_skill);
		$max  = interpolate($skill_list[$i], $skill_list[($i+1)], $max_list[$i], $max_list[($i+1)], $brawl_skill);
		$crit = interpolate($skill_list[$i], $skill_list[($i+1)], $crit_list[$i], $crit_list[($i+1)], $brawl_skill);
		$stunC = (($brawl_skill < 1000) ? "<orange>10<end>%, <font color=#cccccc>will become </font>20<font color=#cccccc>% above </font>1000<font color=#cccccc> brawl skill</font>" : "<orange>20<end>%");
		$stunD = (($brawl_skill < 2001) ?  "<orange>3<end>s, <font color=#cccccc>will become </font>4<font color=#cccccc>s above </font>2001<font color=#cccccc> brawl skill</font>" :  "<orange>4<end>s");
		
		
		
		$inside = $header;
		$inside .= "<u>Results</u>:\n";
		$inside .= "Brawl Skill: <orange>".$brawl_skill."<end>\n";
		$inside .= "Brawl recharge: <orange>15<end> seconds <font color=#ccccc>(constant)</font>\n";
		$inside .= "Damage: <orange>".$min."<end>-<orange>".$max."<end>(<orange>".$crit."<end>)\n";
		$inside .= "Stun chance: ".$stunC."\n";
		$inside .= "Stun duration: ".$stunD."\n";
		$inside .= $footer;
		
		$windowlink = bot::makeLink("::Your Brawl skill results::", $inside);
		bot::send($windowlink, $sendto);
	} else bot::send($helplink, $sendto);	
?>