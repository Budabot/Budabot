<?
$info = explode(" ", $message);
list($msg, $MaSkill) = $info;

if($type == "msg")
    $sendto = $sender;
elseif($type == "priv")
	$sendto = "";
elseif($type == "guild")
	$sendto = "guild";

$header = "\n<header>::::: MA Fist Calculator - Version 1.00 :::::<end>\n\n";
$footer = "";

$help = $header;
$help .= "<font color=#3333CC>MAFist Usage:</font>\n";
$help .= "/tell <myname> <symbol>mafist [<orange>MA<end>]\n";
$help .= "[<orange>MA<end>] = MA Skill\n";
$help .= "Example:\n";
$help .= "You have 750 MA Skill.\n";
$help .= "<a href='chatcmd:///tell <myname> <symbol>mafist 750'>/tell <myname> <symbol>mafist 750</a>\n\n";
$help .= $footer;

$helplink = bot::makeLink("::How to use MA Fist::", $help);

if(!$MaSkill)
	bot::send($helplink, $sendto);
else{
	$fistql = round($MaSkill/2);

	if ($fistql <= 200)
		$speed = 1.25;
	else if ($fistql <= 500)   
		{            
        $speed = 1.25 + (0.2*(($fistql-200)/300));
		}
	else if ($fistql <= 1000)
		{
		$speed = 1.45 + (0.2*(($fistql-500)/500));  
		}
	else if ($fistql <= 1500)
		{
		$speed = 1.65 + (0.2*(($fistql-1000)/500));
		}

	$speed = round($speed,2);
	$inside = $header;
	$inside .= "Results:\n";
	$inside	.= "MA Skill: <orange>". $MaSkill ."<end>\n";
	$inside	.= "Fist QL: <orange>". $fistql ."<end>\n";
	$inside	.= "Fist Speed: <orange>". $speed."<end>/<orange>".$speed ."<end>\n";
	$inside .= $footer;

	$windowlink = bot::makeLink("::Your Fist Results::", $inside);
	bot::send($windowlink, $sendto);
	}