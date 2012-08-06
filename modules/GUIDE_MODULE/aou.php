<?php

global $version;
$url = "http://www.ao-universe.com/mobile/parser.php?bot=budabot&bot_version=$version";
if (preg_match("/^aou (\\d+)$/i", $message, $arr)) {
	$guideid = $arr[1];

	$guide = file_get_contents($url . "&mode=view&id=" . $guideid);

	$dom = new DOMDocument;
	$dom->loadXML($guide);

	$content = $dom->getElementsByTagName('content')->item(0);

	$title = $content->getElementsByTagName('name')->item(0)->nodeValue;

	$blob = Text::make_chatcmd("Guide on AO-Universe.com", "/start http://www.ao-universe.com/main.php?site=knowledge&id={$guideid}") . "\n";
	$blob .= Text::make_chatcmd("Guide on AO-Universe.com Mobile", "/start {$url}?id={$guideid}") . "\n\n";
	$blob .= $content->getElementsByTagName('text')->item(0)->nodeValue;
	$blob .= "\n\n<yellow>Powered by<end> " . Text::make_chatcmd("AO-Universe.com", "/start http://www.ao-universe.com");

	$msg = Text::make_blob($title, $blob);
	$sendto->reply($msg);
} else if (preg_match("/^aou (.+)$/i", $message, $arr)) {
	$search = $arr[1];

	$results = file_get_contents($url . "&mode=search&search=" . rawurlencode($search));

	$dom = new DOMDocument;
	$dom->loadXML($results);

	$guides = $dom->getElementsByTagName('guide');

	$blob = '';
	$count = 0;
	forEach ($guides as $guide) {
		$count++;
		$id = $guide->getElementsByTagName('id')->item(0)->nodeValue;
		$name = $guide->getElementsByTagName('name')->item(0)->nodeValue;
		$desc = $guide->getElementsByTagName('desc')->item(0)->nodeValue;
		$guide_link = Text::make_chatcmd("$name", "/tell <myname> aou $id");

		$blob .= "$guide_link\n{$desc}\n\n";
	}

	$blob .= "\n<yellow>Powered by<end> " . Text::make_chatcmd("AO-Universe.com", "/start http://www.ao-universe.com");

	if ($count > 0) {
		$msg = Text::make_blob("AO-U Guides containing '$search' ($count)", $blob);
	} else {
		$msg = "Could not find any guides containing: '$search'";
	}
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
