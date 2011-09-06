<?php

if (preg_match("/^bior$/i", $message)) {
	if (count($chatBot->data['bior']) == 0) {
		$msg = "No Adventurer, Keeper, Enforcer or Engineer 201+ in chat.";
	} else {
		$chatBot->data['blist'] = array();
		$info  = "<header>::::: Info about Bio Regrowth macro :::::<end>\n\n";
		$info .= "The bot has it's own Bio Regrowth macro to use it just do ";
		$info .= "<symbol>b in the chat. \n\n";
		$info .= "<a href='chatcmd:///macro BR_Macro /g <myname> <symbol>b'>Click here to make an Bio Regrowth macro </a>";
		$info = Text::make_blob("Info", $info);

		//Create Bio Regrowth Order
		forEach ($chatBot->data['bior'] as $key => $value) {
			if ($chatBot->data['bior_caller'] == $key) {
				$list[(sprintf("%03d", "300").$key)] = $key;
			} else if ($chatBot->data['bior'][$key]["b"] == "ready") {
				$list[(sprintf("%03d", (220 - $chatBot->data['bior'][$key]["lvl"])).$key)] = $key;
			} else {
				$list[(sprintf("%03d", "250").$key)] = $key;
			}
		}

		$num = 0;
		ksort($list);
		reset($list);
		$msg = "Bio Regrowth Order($info):";
		forEach ($list as $player) {
			if ($chatBot->data['bior'][$player]["b"] == "ready") {
				$status = "<green>*ready*<end>";
			} else if (($chatBot->data['bior'][$player]["b"] - time()) > 300) {
				$status = "<red>running<end>";
			} else {
				$rem = $chatBot->data['bior'][$player]["b"] - time();
				$mins = floor($rem / 60);
				$secs = sprintf("%02d", $rem - ($mins * 60));
				$status = "<orange>$mins:$secs<end>";
			}
			$num++;
			$msg .= " [$num. <highlight>$player<end> $status]";
			$chatBot->data['blist'][] = $player;
			if ($num >= Setting::get("bior_max")) {
				break;
			}
		}

		//Send Blist
		forEach ($chatBot->data['blist'] as $player) {
			$chatBot->send($msg, $player);
		}
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>