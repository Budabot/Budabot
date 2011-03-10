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
	$msg = "Yeah yeah gratz\nI would give you a better response\nbut you didn't say what you dinged\nUsage: ding 'level'";
 	$chatBot->send($msg, $sendto);
} else if (preg_match("/^ding ([0-9]+)$/i", $message, $arr)) {
	if ($arr[1] == 100) {
		$msg = "Congratz! <red>100<end> ".$sender." you rock!\n";
	} else if ($arr[1] == 180) {
		$msg = "Congratz! Now go kill some <green>Aliumz<end> at APF!!";
	} else if ($arr[1] == 200) {
		$msg =	"Congratz! The big Two Zero Zero!!!\nParty at ".$sender."'s place";
	} else if ($arr[1] > 200 && $arr[1] < 220) {
		$dingText = array(
		"Congratz!",
		"Enough with the dingin you are making the fr00bs feel bad!",
		"Come on save some dings for the rest!");
 		$rndnum = rand(0, count($dingText) - 1);
		$msg = $dingText[$rndnum];
	} else if ($arr[1] == 220) {
		$msg =	"Congratz! You have reached the end of the line! No more fun for you :)";
	} else if ($arr[1] > 220) {
		$msg =	"Umm...no.";
	} else {
		$msg = "Ding ding ding..now ding some more!";
	}
 	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>