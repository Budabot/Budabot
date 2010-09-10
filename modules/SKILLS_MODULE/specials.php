<?php

// <a href="itemref://280727/280727/300">Sloth of the Xan</a>
if (preg_match('/^specials \<a href\=\"itemref\:\/\/([0-9]+)\/([0-9]+)\/([0-9]+)\"\>/i', $message, $arr)) {
	$url = "http://itemxml.xyphos.com/?";
	$url .= "id={$arr[1]}&";  // use low id for id
	//$url .= "id={$arr[2]}&";  // use high id for id
	$url .= "ql={$arr[3]}&";
	
	//$url .= "id=214212&";
	//$url .= "ql=300&";

	$msg = "Calculating Specials Recycle... Please wait.";
	bot::send($msg, $sendto);

	$data = file_get_contents($url, 0);
	if (empty($data)) {
		$msg = "Unable to query Items XML Database.";
		bot::send($msg, $sendto);
		return;
	}

	$doc = new DOMDocument();
	$doc->prevservWhiteSpace = false;
	$doc->loadXML($data);
	
	$attributes = $doc->getElementsByTagName('attributes')->item(0)->getElementsByTagName('attribute');

	forEach ($attributes as $attribute) {
		switch ($attribute->attributes->getNamedItem("name")->nodeValue) {
			case 'Can':
				$flags = $attribute->attributes->getNamedItem("extra")->nodeValue;
				break;
			case 'AttackDelay':
				$attack_time = $attribute->attributes->getNamedItem("value")->nodeValue;
				break;
			case 'RechargeDelay':
				$recharge_time = $attribute->attributes->getNamedItem("value")->nodeValue;
				break;
			case 'FullAutoRecharge':
				$full_auto_recharge = $attribute->attributes->getNamedItem("value")->nodeValue;
				break;
			case 'BurstRecharge':
				$burst_recharge = $attribute->attributes->getNamedItem("value")->nodeValue;
				break;
		}		
	}
	$flags = explode(', ', $flags);
	$recharge_time /= 100;
	$attack_time /= 100;
	
	$blob = "<header>::: Weapon Specials :::<end>\n\n";
	if (in_array('FullAuto', $flags)) {
		list($FACap, $FA_Skill_Cap) = cap_full_auto($attack_time, $recharge_time, $full_auto_recharge);
		$blob .= "FullAutoRecharge: $full_auto_recharge -- You will need at least <orange>".$FA_Skill_Cap."<end> Full Auto skill to cap your recharge at: <orange>".$FACap."<end>s\n\n";
		$found = true;
	}
	if (in_array('Burst', $flags)) {
		list($cap, $burstskillcap) = cap_burst($attack_time, $recharge_time, $burst_recharge);
		$blob .= "BurstRecharge: $burst_recharge -- You need <orange>".$burstskillcap."<end> Burst Skill to cap your burst at: <orange>".$cap."<end>s\n\n";
		$found = true;
	}
	if (in_array('FlingShot', $flags)) {
		list($flinghardcap, $flingskillcap) = cap_fling_shot($attack_time);
		$blob .= "FlingRecharge: You need <orange>".$flingskillcap."<end> Fling Skill to cap your fling at: <orange>".$flinghardcap."<end>s\n\n";
		$found = true;
	}
	if (in_array('FastAttack', $flags)) {
		list($fasthardcap, $fastskillcap) = cap_fast_attack($attack_time);
		$blob .= "FastAttackRecharge: You need <orange>".$fastskillcap."<end> Fast Atk Skill to cap your fast attack at: <orange>".$fasthardcap."<end>s\n\n";
		$found = true;
	}
	if (in_array('AimedShot', $flags)) {
		list($cap, $ASCap) = cap_aimed_shot($attack_time, $recharge_time);
		$blob .= "AimedShotRecharge: You need <orange>".$ASCap."<end> AS skill to cap your recharge at: <orange>".$cap."<end>s.\n\n";
		$found = true;
	}
	
	// brawl, dimach don't depend on weapon at all
	// we don't have a formula for sneak attack
	
	if (!$found) {
		$msg = "No specials on this weapon that could be calculated.";
	} else {
		$blob .= "Written by Tyrence(RK2)\n";
		$blob .= "Stats provided by xyphos.com";
		$msg = $this->makeLink('Weapon Specials', $blob, 'blob');
	}

	bot::send($msg, $sendto);
} else {
	$msg = "Syntax Error! Proper Syntax is <highlight>specials [drop weapon in chat]<end>";
	bot::send($msg, $sendto);
}

?>