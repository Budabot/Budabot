<?php

// taken from http://www.sitepoint.com/forums/php-34/php5-need-something-like-innerhtml-instead-nodevalue-611393.html#post4224879
// and modified
if (!function_exists('innerXML')) {
	function innerXML($node) { 
		$str = '';
		$children = false;
		forEach ($node->childNodes as $child) {
			$children = true;
			$str .= innerXML($child);
		}
		
		if ($children == false) {
			return $node->nodeValue . "\n";
		} else {
			return $str;
		}
	}
}

$url = "http://www.ao-universe.com/mobile/guides.php";
if (preg_match("/^aou (\\d+)$/i", $message, $arr)) {
	$guideid = $arr[1];
	
	$guide = file_get_contents($url . "?id=" . $guideid);
	
	$dom = new DOMDocument;
	$dom->loadHTML($guide);
	
	$title = $dom->getElementsByTagName('header')->item(0)->nodeValue;

	$divs = $dom->getElementsByTagName('div');

	$blob .= "<header> :::::: $title :::::: <end>\n\n";
	forEach ($divs as $div) {
		if ($div->attributes->getNamedItem("class")->nodeValue == "content guidetext") {
			$blob .= strip_tags(str_replace("<br>", "\n", innerXML($div)));
			break;
		}
	}
	
	$blob .= "\n\n<yellow>Powered by<end> " . Text::make_chatcmd("AO-Universe.com", "/start http://www.ao-universe.com");
	
	$msg = Text::make_blob($title, $blob);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^aou (.+)$/i", $message, $arr)) {
	$search = str_replace(' ', '%', $arr[1]);
	
	$results = file_get_contents($url . "?q=" . $search);
	
	$dom = new DOMDocument;
	$dom->loadHTML($results);
	
	$divs = $dom->getElementsByTagName('div');

	$blob .= "<header> :::::: Guides containing '$search' :::::: <end>\n\n";
	forEach ($divs as $div) {
		if ($div->attributes->getNamedItem("class")->nodeValue == "guide") {
			$id = $div->getElementsByTagName('span')->item(0)->getElementsByTagName('a')->item(0)->attributes->getNamedItem("href")->nodeValue;
			$name = $div->getElementsByTagName('span')->item(0)->getElementsByTagName('a')->item(0)->nodeValue;
			$desc = $div->getElementsByTagName('span')->item(1)->nodeValue;
			$id = preg_replace("/[^0-9]/", "", $id);
			$guide_link = Text::make_chatcmd("$name", "/tell <myname> aou $id");
			
			$blob .= "$guide_link\n{$desc}\n\n";
		}
	}
	
	$blob .= "\n\n<yellow>Powered by<end> " . Text::make_chatcmd("AO-Universe.com", "/start http://www.ao-universe.com");
	
	$msg = Text::make_blob("AO-U Guides containing '$search'", $blob);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
