<?php

$fields = array();
$fields['q'] = '';
$fields['type'] = '';
$fields['s'] = $chatBot->vars["dimension"];
$fields['limit'] = $setting->get('max_shopping_results');

if (preg_match("/^wtb (.+)$/i", $message, $arr)) {
	//WTS messages = 0, WTB messages = 1
	$fields['type'] = "0";
	$fields['q'] = $arr[1];
} else if (preg_match("/^wts (.+)$/i", $message, $arr)) {
	//WTS messages = 0, WTB messages = 1
	$fields['type'] = "1";
	$fields['q'] = $arr[1];
} else {
	$syntax_error = true;
	return;
}

if (!$syntax_error) {
	$query_string = '?mode=xml';
	forEach ($fields as $name => $value) {
		$query_string .= "&{$name}=" . rawurlencode($value);
	}

	$xml_file = file_get_contents("http://services.rubi-ka.com/market.php" . $query_string);
	try {
		$xml_doc = new SimpleXMLElement($xml_file);

		$blob = '';
		forEach ($xml_doc->marketpost as $marketpost) {
			$char_link = Text::make_userlink($marketpost->player);
			$time = Util::unixtime_to_readable(time() - $marketpost->time);
			$message = preg_replace('/<a href="itemref:\/\/(\d+)\/(\d+)\/(\d+)">([^<]+)<\/a>/', "<a href='itemref://\\1/\\2/\\3'>\\4</a>", $marketpost->message);
			$blob .= "[{$char_link}]: {$message} <orange>{$time} ago<end> \n\n";
		}
	} catch (Exception $e) {

	}

	if ($blob != '') {
		$blob .= "\nSearch results provided by <a href='chatcmd:///start www.rubi-ka.com'>www.rubi-ka.com</a> - Zajin (RK1)";
		$msg = Text::make_blob("Shopping results for '{$fields['q']}'", $blob);
	} else {
		$msg = 'No items found.';
	}

	$sendto->reply($msg);
}

?>
