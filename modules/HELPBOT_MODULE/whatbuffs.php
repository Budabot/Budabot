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
		case 0:  // skill does not exist
			$sendto->reply("Could not find a skill by that name.");
			return;

		case 1:  // exactly one matching skill
			$info = "";
			$found = 0;
			forEach ($buffitems as $key => $item_info) {
				if (contains($item_info, $skills[0])) {
					$found++;
					$info .= "- " . Text::make_chatcmd($key, "/tell <myname> buffitem $key") . "\n";
				}
			}
			if ($found > 0) {								// found items that modify this skill
				$inside = "Your query of <yellow>$name<end> yielded the following results:\n\n";
				$inside .= "Items that buff ".$skills[0].":\n\n";
				$inside .= $info;
				$inside .= "\n\nby Imoutochan, RK1";
				$windowlink = Text::make_blob("What Buffs '$name' ($found)", $inside);
				$sendto->reply($windowlink);
				$sendto->reply("<highlight>$found<end> result(s) in total");
				return;
			} else {
				$sendto->reply("Nothing that buffs ".$skills[0]." in my database.");
				return;
			}
			break;

		default:  // found more than 1 matching skill
			$info = "";
			forEach ($skills as $skill) {
				$info .= "- " . Text::make_chatcmd($skill, "/tell <myname> whatbuffs $skill") . "\n";
			}
			$inside = "Your query of <yellow>$name<end> matches more than one skill:\n\n";
			$inside .= $info;
			$inside .= "\n\nby Imoutochan, RK1";
			$windowlink = Text::make_blob("What Buffs Skills (" . count($skills) . ")", $inside);
			$sendto->reply($windowlink);
			$sendto->reply("Found several skills matching your key words.");
			return;
	}
} else {
	$syntax_error = true;
}

?>
