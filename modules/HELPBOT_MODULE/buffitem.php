<?php

if (preg_match("/^buffitem (.+)$/i", $message, $arr)) {
	$name = $arr[1];

	$matches = array();
	$found = 0;
	$dbparam = '%' . str_replace(" ", "%", $name . '%');
	// search item line database
	$results = $db->query("SELECT * FROM buffitems WHERE item_name LIKE ? OR aliases LIKE ?", $dbparam, $dbparam);
	forEach ($results as $row) {
		$found++;
		$info =	make_info($row);
		$matches []= array($row->item_name, $info);
	}

	if ($found == 0) {
		$msg = "No matches, sorry.";
	} else {
		if ($found == 1) {
			$blob .= $matches[0][1];
			$blob .= "\n\nby Imoutochan, RK1";
			$msg = Text::make_blob("Buff Item - " . $matches[0][0], $blob);
		} else {
			$blob = "Your query of <yellow>".$name."<end> returned the following item line(s):\n\n";
			forEach ($matches as $result) {
				$blob .= "- <a href='chatcmd:///tell <myname> buffitem ".$result[0]."'>".$result[0]."</a>".
						   (sizeof($result) == 3 ? " (".$result[2].")" : "")."\n";
			}
			$blob .= "\n".sizeof($matches)." results found, please pick one by clicking it";
			$blob .= "\n\nby Imoutochan, RK1";
			$msg = Text::make_blob("Buff item search results (<highlight>$found<end>)", $blob);
		}
	}
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
