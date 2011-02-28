<?php
/* ******************************************** */
/* Author: C. Lohmann alias Derroylo(RK2)       */
/* Date(created): 24.02.2006                    */
/* Date(last modified): 26.02.2006              */
/* Project: Org/Raid Bot                        */
/* Description: Creates a Guardian Order        */
/* Version: 1.0                                 */
/* Part of: Guardian Module                     */
/* ******************************************** */

if (count($chatBot->data['guard']) == 0) {
  	$msg = "No 205+ Soldiers in chat.";
} else {
  	$chatBot->data['glist'] = array();
	$info  = "<header>::::: Info about Guardian macro :::::<end>\n\n";
	$info .= "The bot has it's own Guardian macro to use it just do ";
	$info .= "<symbol>g in the chat. \n\n";
	$info .= "<a href='chatcmd:///macro G_Macro /g <myname> <symbol>g'>Click here to make an G macro </a>";
	$info = Text::make_link("Info", $info);

  	//Create g Order
	forEach ($chatBot->data['guard'] as $key => $value) {
	  	if ($chatBot->data['guard_caller'] == $key) {
			$list[(sprintf("%03d", "300").$key)] = $key;
	  	} else if ($chatBot->data['guard'][$key]["g"] == "ready") {
			$list[(sprintf("%03d", (220 - $chatBot->data['guard'][$key]["lvl"])).$key)] = $key;
		} else {
			$list[(sprintf("%03d", "250").$key)] = $key;
		}
  	}

	$num = 0;
	ksort($list);
	reset($list);
  	$msg = "Guardian Order($info):";
	forEach ($list as $player) {
	  	if ($chatBot->data['guard'][$player]["g"] == "ready") {
	  		$status = "<green>*Ready*<end>";
	  	} else if (($chatBot->data['guard'][$player]["g"] - time()) > 300) {
	  		$status = "<red>Running<end>";
	  	} else {
		    $rem = $chatBot->data['guard'][$player]["g"] - time();
			$mins = floor($rem / 60);
			$secs = $rem - ($mins * 60);
		    $status = "<orange>$mins:$secs<end>";
		}
		$num++;
		$msg .= " [$num. <highlight>$player<end> $status]";
        $chatBot->data['glist'][] = $player;
        if ($num >= Setting::get("guard_max")) {
        	break;
		}
	}

  	//Send Glist to all soldiers
  	forEach ($chatBot->data['glist'] as $player) {
		$chatBot->send($msg, $player);
  	}
}
$chatBot->send($msg, $sendto);
?>