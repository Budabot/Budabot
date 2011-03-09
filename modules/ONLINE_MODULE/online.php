<?php

if (preg_match("/^online$/i", $message)){
	list($numonline, $msg, $list) = get_online_list();
	if ($numonline != 0) {
		$blob = Text::make_link($msg, $list);
		$chatBot->send($blob, $sendto);
	} else {
		$chatBot->send($msg, $sendto);
	}
} else if (preg_match("/^online (.*)$/i", $message, $arr)) {
	switch (strtolower($arr[1])) {
		case "all":
			$prof = "all";
			break;
		case "adv":
			$prof = "Adventurer";
			break;
		case "agent":
			$prof = "Agent";
			break;
		case "crat":
			$prof = "Bureaucrat";
			break;
		case "doc":
			$prof = "Doctor";
			break;
		case "enf":
			$prof = "Enforcer";
			break;
		case "eng":
			$prof = "Engineer";
			break;
		case "fix":
			$prof = "Fixer";
			break;
		case "keep":
			$prof = "Keeper";
			break;
		case "ma":
			$prof = "Martial Artist";
			break;
		case "mp":
			$prof = "Meta-Physicist";
			break;
		case "nt":
			$prof = "Nano-Technician";
			break;
		case "sol":
			$prof = "Soldier";
			break;
		case "trad":
			$prof = "Trader";
			break;
		case "shade":
			$prof = "Shade";
			break;
	}

	if (!$prof) {
		$msg = "Please choose one of these professions: adv, agent, crat, doc, enf, eng, fix, keep, ma, mp, nt, sol, shade, trad or all";
		$chatBot->send($msg, $sendto);
		return;
	}

	list($numonline, $msg, $list) = get_online_list($prof);
	if ($numonline != 0) {
		$blob = Text::make_link($msg, $list);
		$chatBot->send($blob, $sendto);
	} else {
		$chatBot->send($msg, $sendto);
	}
}
?>
