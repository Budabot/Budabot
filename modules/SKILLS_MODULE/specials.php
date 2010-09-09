<?php

// <a href="itemref://280727/280727/300">Sloth of the Xan</a>
if (preg_match('/^specials \<a href\=\"itemref\:\/\/([0-9]+)\/([0-9]+)\/([0-9]+)\"\>/i', $message, $arr)) {
	$url = "http://itemxml.xyphos.com/?";
	//$url .= "lowid={$arr[1]}&";
	$url .= "id={$arr[2]}&";
	$url .= "ql={$arr[3]}&";

	$msg = "Calculating Specials Recycle... Please wait.";
	bot::send($msg, $sendto);

	$data = file_get_contents($url, 0);
	if (empty($data)) {
		$msg = "Unable to query Items XML Database.";
	}

	$doc = new DOMDocument();
	$doc->prevservWhiteSpace = false;
	$doc->loadXML($data);
	
	$attributes = $doc->getElementsByTagName('attributes')->item(0)->getElementsByTagName('attribute');

	forEach ($attributes as $attribute) {
		switch ($attribute->attributes->getNamedItem("name")->nodeValue) {
			case 'Can':
				$flags = $attribute->attributes->getNamedItem("value")->nodeValue;
				break;
			case 'AttackDelay':
				$attack_delay = $attribute->attributes->getNamedItem("value")->nodeValue;
				break;
			case 'RechargeDelay':
				$recharge_delay = $attribute->attributes->getNamedItem("value")->nodeValue;
				break;
			case 'FullAutoRecharge':
				$full_auto_recharge = $attribute->attributes->getNamedItem("value")->nodeValue;
				break;
			case 'BurstRecharge':
				$burst_recharge = $attribute->attributes->getNamedItem("value")->nodeValue;
				break;
			// TODO
		}		
	}
	$flags = explode(', ', $flags);
	
	if (in_array('FullAuto', $flags)) {
		// TODO
	}
	if (in_array('Burst', $flags)) {
		// TODO
	}
	if (in_array('FlingShot', $flags)) {
		// TODO
	}
	if (in_array('FastAttack', $flags)) {
		// TODO
	}
	if (in_array('AimedShot', $flags)) {
		// TODO
	}
	
	// brawl, dimach don't depend on weapon at all
	// we don't have a formula for sneak attack

} else {
	$msg = "Syntax Error! Proper Syntax is <highlight>specials [drop weapon in chat]<end>";
}

bot::send($msg, $sendto);

?>