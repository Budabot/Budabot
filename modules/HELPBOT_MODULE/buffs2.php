<?php

if (preg_match("/^bufftest$/i", $message, $arr)) {
	$blob = "<header> :::::: Buff item ability selection :::::: <end>\n\n";
	$blob .= Text::make_chatcmd("Agility", "/tell buffitems agility") " - ";
	$blob .= Text::make_chatcmd("Intelligence", "/tell buffitems Intelligence") " - ";
	$blob .= Text::make_chatcmd("Psychic", "/tell buffitems Psychic") " - ";
	$blob .= Text::make_chatcmd("Sense", "/tell buffitems Sense") " - ";
	$blob .= Text::make_chatcmd("Stamina", "/tell buffitems Stamina") " - ";
	$blob .= Text::make_chatcmd("Strength", "/tell buffitems Strength") " - ";
	$chatBot->send($blob, $sendto);

if (preg_match("/^buffitems (.+)$/i", $message, $arr)) {
	$name = $arr[1];	
	//$data = db->query(SELECT * FROM buffitems WHERE level = ?;
	$results = array();
	$found = 0;
	// search item line database
	forEach ($buffitems as $key => $value) {
		unset($info);
		if (matches($key, $name)) {
			$found++;
			$info =	make_info($key, $value);
			array_unshift($results, array($key, $info));
		}
	}
	// search  item alias database
	forEach ($aliases as $key => $values) {
		unset($info);
		if (contains($values, $name) && !(duplicate($key, $results))) {
			$found++;
			$buffitem = $buffitems[$key];
			$alias = get_alias($values, $name);
			$info =	"Item <green>$alias<end>\nbelongs into the line of ";
			$info .= make_info($key, $buffitem);
			array_unshift($results, array($key, $info, $alias));
		}
	}
	
	if ($found == 0) {
		$chatBot->send("No matches, sorry.", $sendto);
		return;
	} else {
		$blob = "<header>::::: Buff item info :::::<end>\n\n";
		$blob .= "Your query of <yellow>".$name."<end> returned the following item line(s):\n\n";
		if ($found == 1) {
			$blob .= $results[0][1]."\n\n";
		} else {
			forEach ($results as $result) {
				$blob .= "- <a href='chatcmd:///tell <myname> <symbol>buffitem ".$result[0]."'>".$result[0]."</a>".
						   (sizeof($result) == 3 ? " (".$result[2].")" : "")."\n";
			}
			$blob .= "\n".sizeof($results)." results found, please pick one by clicking it";
		}
		$blob .= "\n\nby Imoutochan, RK1";
		$msg = Text::make_blob("Buff item search results (<highlight>$found<end>)", $blob);
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}
	
?>