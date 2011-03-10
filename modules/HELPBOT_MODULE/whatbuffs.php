<?php

include 'buffstuffdb.php';

if (preg_match("/^whatbuffs (.+)$/i", $message, $arr)) {
	$name = trim($arr[1]);
	// check if key words are unambiguous
	$skills = array();
	$results = array();
	forEach ($skill_list as $skill) {
		if (matches($skill, $name)) {
			array_unshift($skills, $skill);
		}
	}

	switch (sizeof($skills)) {
		case 0:
			$chatBot->send("There is no such skill, or at least no twink relevant skill going by that name.", $sendto); 	// skill does not exist
			return;

		case 1:
			$info = "";										// exactly one matching skill
			$found = 0;
			forEach ($buffitems as $key => $item_info) {	
				if (contains($item_info, $skills[0])) {
					$found++;
					$info .= "- <a href='chatcmd:///tell <myname> <symbol>buffitem $key'>$key</a>\n";
				}
			}
			if ($found > 0) {								// found items that modify this skill
				$inside = "<header>::::: Buff item helper :::::<end>\n\n";
				$inside .= "Your query of <yellow>$name<end> yielded the following results:\n\n";
				$inside .= "Items that buff ".$skills[0].":\n\n";
				$inside .= $info;
				$inside .= "\n\nClick the item(s) for more info\n\n";
				$inside .= "by Imoutochan, RK1";
				$windowlink = Text::make_link(":: Your \"What buffs ...?\" results ::", $inside);
				$chatBot->send($windowlink, $sendto); 
				$chatBot->send("<highlight>$found<end> result(s) in total", $sendto);
				return;
			} else {
				$chatBot->send("Nothing that buffs ".$skills[0]." in my database, sorry.", $sendto);
				return; 
			}
			break;

		default:
			$info = ""; 									// found more than 1 matching skill
			forEach ($skills as $skill) {
				$info .= "- <a href='chatcmd:///tell <myname> <symbol>whatbuffs ".$skill."'>$skill</a>\n";
			}
			$inside = "<header>::::: Buff item helper :::::<end>\n\n";
			$inside .= "Your query of <yellow>$name<end> matches more than one skill:\n\n";
			$inside .= $info."\n";
			$inside .= "Which of those skills did you mean?\n\n";
			$inside .= "by Imoutochan, RK1";
			$windowlink = Text::make_link(":: Your \"What buffs ...?\" results ::", $inside);
			$chatBot->send($windowlink, $sendto); 
			$chatBot->send("Found several skills matching your key words.", $sendto);
			return;
	}
} else {
	$syntax_error = true;
}

?>