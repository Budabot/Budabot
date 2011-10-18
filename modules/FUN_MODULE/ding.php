<?php
   /*
   ** Author: Neksus (RK2)
   ** Description: Spams a congrats message in Guildchat
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 15.07.2006
   ** Date(last modified): 15.07.2006
   ** 
   */
if (preg_match("/^ding$/i", $message)) {
	$dingText = array(
		"Yeah yeah gratz, I would give you a better response but you didn't say what you dinged\n<red>Usage: !ding 'level'<end>",
		"Hmmm, I really want to know what level you dinged, but gratz anyways nub.",
		"When are you people going to start using me right! Gratz for your level though.",
		"Gratz! But what are we looking at? I need a level next time.");

 	$chatBot->send(Util::rand_array_value($dingText), $sendto);
} else if (preg_match("/^ding dong$/i", $message)) {
	$msg =	"Ditch, Bitch!";
 	$chatBot->send($msg, $sendto);
} else if (preg_match("/^ding ([\-+]?[0-9]+)$/i", $message, $arr) ||
		preg_match("/^ding ([\-+]?[0-9]+) (.+)$/i", $message, $arr)) {
	if ($arr[1] == 100) {
		$dingText = array(
			"Congratz! <red>Level 100<end> - ".$sender." you rock!\n",
			"Congratulations! Time to twink up for T.I.M!",
			"Gratz, your half way to 200. More missions, MORE!",
			"Woot! Congrats, don't forget to put on your 1k token board.");
	} else if ($arr[1] <= 0) {
		$lvl = (int)round(220 - $arr [1]);
		$dingText = array(
			"Reclaim sure is doing a number on you if your going backwards...",
			"That sounds like a problem... so how are your skills looking?",
			"Wtb negative exp kite teams!",
			"That leaves you with... $lvl more levels until 220, I don't see the problem?",
			"How the hell did you get to $arr[1]?");
	} else if ($arr[1] == 1) {
		$dingText = array(
			"You didn't even start yet...",
			"Did you somehow start from level 0?",
			"Dinged from 1 to 1? Congratz");
	} else if ($arr[1] == 150) {
		$dingText = array(
			"S10 time!!!",
			"Time to ungimp yourself! Horray!. Congrats =)",
			"What starts with A, and ends with Z? <green>ALIUMZ!<end>",
			"Wow, is it that time already? TL 5 really? You sure are moving along! Gratz");
	} else if ($arr[1] == 180) {
		$dingText = array(
			"Congratz! Now go kill some <green>aliumz<end> at S13/28/35!!",
			"Only 20 more froob levels to go! HOORAH!",
			"Yay, only 10 more levels until TL 6! Way to go!");
	} else if ($arr[1] == 190) {
		$dingText = array(
			"Wow holy shiznits! Your TL 6 already? Congrats!",
			"Just a few more steps and your there buddy, keep it up!",
			"Almost party time! just a bit more to go ".$sender.". We'll be sure to bring you a cookie!");
	} else if ($arr[1] == 200) {
		$dingText = array(
			"Congratz! The big Two Zero Zero!!!\nParty at ".$sender."'s place",
			"Best of the best in froob terms, congratulations!",
			"What a day indeed. Finally done with froob levels. Way to go!");
	} else if ($arr[1] > 200 && $arr[1] < 220) {
		$dingText = array(
			"Congratz! Just a few more levels to go!",
			"Enough with the dingin you are making the fr00bs feel bad!",
			"Come on save some dings for the rest!");
	} else if ($arr[1] == 220) {
		$dingText = array(
			"Congratz! You have reached the end of the line! No more fun for you :P",
			"Holy shit, you finally made it! What an accomplishment... Congratulations ".$sender.", for reaching a level reserved for the greatest!",
			"I'm going to miss you a great deal, because after this, we no longer can be together ".$sender.". We must part so you can continue getting your research and AI levels done! Farewell!",
			"How was the inferno grind? I'm glad to see you made it through, and congratulations for finally getting the level you well deserved!",
			"Our congratulations, to our newest level 220 member, ".$sender.", for his dedication. We present him with his new honorary rank, Chuck Norris!");
	} else if ($arr[1] > 220) {
		$dingText = array(
			"Umm...no.",
			"You must be high, because that number is to high...",
			"Ha, ha... ha, yeah... no...",
			"You must be a GM or one hell of an exploiter, that number it too high!",
			"Yeah, and I'm Chuck Norris...",
			"Not now, not later, not ever... find a more reasonable level!");
	} else {
		$lvl = (int)round(220 - $arr [1]);
		$dingText = array(
			"Ding ding ding... now ding some more!",
			"Keep em coming!",
			"Don't stop now, your getting there!",
			"Come on, COME ON! Only $lvl more levels to go until 220!");
	}

 	$chatBot->send(Util::rand_array_value($dingText), $sendto);
} else {
	$syntax_error = true;
}

?>