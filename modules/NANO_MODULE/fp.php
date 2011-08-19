<?php
   
if (preg_match("/^fp (\\d+)$/i", $message, $arr1) || preg_match("/^fp (.+)$/i", $message, $arr2)) {
	if ($arr2) {
		$name = str_replace("'", "''", $arr2[1]);

		$tmp = explode(" ", $name);
		forEach ($tmp as $key => $value) {
			$query .= " AND `name` LIKE '%$value%'";
		}

		$db->query("SELECT * FROM nanos WHERE 1=1 $query ORDER BY lowql DESC, name LIMIT 0, {$chatBot->settings["maxnano"]}");
		$data = $db->fObject('all');
	} else { // if ($arr1)
		$id = $arr1[1];
		$db->query("SELECT * FROM nanos WHERE `lowid` = $id ORDER BY lowql DESC, name LIMIT 0, {$chatBot->settings["maxnano"]}");
		$data = $db->fObject('all');
	}
	
	$count = count($data);

	if ($count == 0) {
		$msg = "No nanos found.";
	} else if ($count == 1) {
		$row = $data[0];
		
		$url = "http://itemxml.xyphos.com/?id={$row->lowid}";  // use low id for id

		$data = file_get_contents($url, 0);
		if (empty($data) || '<error>' == substr($data, 0, 7)) {
			$msg = "Unable to query Items XML Database.";
			$chatBot->send($msg, $sendto);
			return;
		}

		$doc = new DOMDocument();
		$doc->prevservWhiteSpace = false;
		$doc->loadXML($data);
		
		$name = $doc->getElementsByTagName('name')->item(0)->nodeValue;
		$requirements = $doc->getElementsByTagName('actions')->item(0)->getElementsByTagName('action')->item(0)->getElementsByTagName('requirements')->item(0)->getElementsByTagName('requirement');

		$fpUsable = false;
		forEach ($requirements as $requirement) {
			if ($requirement->getElementsByTagName('stat')->item(0)->attributes->getNamedItem("name")->nodeValue == "VisualProfession") {
				$fpUsable = true;
			}
		}
		
		$item = Text::make_item($row->lowid, $row->lowid, $row->lowql, $row->name) . " ({$row->lowql})\n";
		
		if ($fpUsable) {
			$msg = "$item <green>is<end> usable in false profession";
		} else {
			$msg = "$item <orange>is not<end> usable in false profession";
		}
	} else {
		$blob = "<header> :::::: Nano Search Results ($count) :::::: <end>\n\n";
		forEach ($data as $row) {
			$check_fp = Text::make_link("Check False Profession", "/tell <myname> fp $row->lowid", 'chatcmd');
			$blob .= Text::make_item($row->lowid, $row->lowid, $row->lowql, $row->name) . " ({$row->lowql}) $check_fp\n\n";
		}
		
		$msg = Text::make_blob("Nano Search Results ($count)", $blob);
	}

	$chatBot->send($msg, $sendto);
} else {
  	$syntax_error = true; 	
}

?>