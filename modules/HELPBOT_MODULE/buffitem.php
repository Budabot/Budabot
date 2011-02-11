<?php

include 'buffstuffalias.php';
include 'buffstuffdb.php';

if (preg_match("/^buffitem (.+)$/i", $message, $arr)) {
	$name = $arr[1];
	
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
		$inside = "<header>::::: Buff item info :::::<end>\n\n";
		$inside .= "Your query of <yellow>".$name."<end> returned the following item line(s):\n\n";
		if ($found == 1) {
			$inside .= $results[0][1]."\n\n";
		} else {
			forEach ($results as $result) {
				$inside .= "- <a href='chatcmd:///tell <myname> <symbol>buffitem ".$result[0]."'>".$result[0]."</a>".
						   (sizeof($result) == 3 ? " (".$result[2].")" : "")."\n";
			}
			$inside .= "\n".sizeof($results)." results found, please pick one by clicking it\n\n";
		}
		$inside .= "by Imoutochan, RK1";
		$windowlink = Text::make_link("Buff item search results", $inside);
	}
	$chatBot->send($windowlink, $sendto);
	$chatBot->send("<highlight>$found<end> result(s) in total", $sendto);
} else {
	$syntax_error = true;
}
	
?>