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
			if ($node->nodeName == 'br') {
				return $node->nodeValue . "\n";
			} else if ($node->nodeName == 'img') {
				if (preg_match("/http:\\/\\/www\\.ao-universe\\.com\\/aodb\\/icons\\/(\\d+)\\.png/", $node->attributes->getNamedItem("src")->nodeValue, $arr)) {
					return "\n<img src=rdb://{$arr[1]}>";
				} else {
					return $node->nodeValue;
				}
			} else {
				return $node->nodeValue . ' ';
			}
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
	$blob .= Text::make_chatcmd("Guide on AO-Universe.com", "/start http://www.ao-universe.com/main.php?site=knowledge&id={$guideid}") . "\n";
	$blob .= Text::make_chatcmd("Guide on AO-Universe.com Mobile", "/start {$url}?id={$guideid}") . "\n\n";
	forEach ($divs as $div) {
		if ($div->attributes->getNamedItem("class")->nodeValue == "content guidetext") {
			$blob .= str_replace("<br>", "\n", innerXML($div));
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
	$found = false;
	forEach ($divs as $div) {
		if ($div->attributes->getNamedItem("class")->nodeValue == "guide") {
			$found = true;
			$id = $div->getElementsByTagName('span')->item(0)->getElementsByTagName('a')->item(0)->attributes->getNamedItem("href")->nodeValue;
			$name = $div->getElementsByTagName('span')->item(0)->getElementsByTagName('a')->item(0)->nodeValue;
			$desc = $div->getElementsByTagName('span')->item(1)->nodeValue;
			$id = preg_replace("/[^0-9]/", "", $id);
			$guide_link = Text::make_chatcmd("$name", "/tell <myname> aou $id");

			$blob .= "$guide_link\n{$desc}\n\n";
		}
	}
	
	$blob .= "\n<yellow>Powered by<end> " . Text::make_chatcmd("AO-Universe.com", "/start http://www.ao-universe.com");
	
	if ($found) {
		$msg = Text::make_blob("AO-U Guides containing '$search'", $blob);
	} else {
		$msg = "Could not find any guides containing: '$search'";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
