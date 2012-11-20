<?php

/**
 * Author:
 *  - Neksus (RK2)
 *  - Mdkdoc420 (RK2)
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'ding',
 *		accessLevel = 'all',
 *		description = 'Shows a random ding gratz message',
 *		help        = 'fun_module.txt'
 *	)
 */
class DingController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/**
	 * @HandlesCommand("ding")
	 * @Matches("/^ding$/i")
	 */
	public function ding1Command($message, $channel, $sender, $sendto, $args) {
		$dingText = array(
			"Yeah yeah gratz, I would give you a better response but you didn't say what level you dinged.",
			"Hmmm, I really want to know what level you dinged, but gratz anyways nub.",
			"When are you people going to start using me right! Gratz for your level though.",
			"Gratz! But what are we looking at? I need a level next time.");

		$sendto->reply($this->util->rand_array_value($dingText));
	}
	
	/**
	 * @HandlesCommand("ding")
	 * @Matches("/^ding dong$/i")
	 */
	public function ding2Command($message, $channel, $sender, $sendto, $args) {
		$msg =	"Ditch, Bitch!";
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("ding")
	 * @Matches("/^ding ([\-+]?[0-9]+)$/i")
	 * @Matches("/^ding ([\-+]?[0-9]+) (.+)$/i")
	 */
	public function ding3Command($message, $channel, $sender, $sendto, $args) {
		$level = $args[1];
		if ($level <= 0) {
			$lvl = (int)round(220 - $level);
			$dingText = array(
				"Reclaim sure is doing a number on you if your going backwards...",
				"That sounds like a problem... so how are your skills looking?",
				"Wtb negative exp kite teams!",
				"That leaves you with... $lvl more levels until 220, I don't see the problem?",
				"How the hell did you get to $level?");
		} else if ($level == 1) {
			$dingText = array(
				"You didn't even start yet...",
				"Did you somehow start from level 0?",
				"Dinged from 1 to 1? Congratz");
		} else if ($level == 100) {
			$dingText = array(
				"Congratz! <red>Level 100<end> - ".$sender." you rock!\n",
				"Congratulations! Time to twink up for T.I.M!",
				"Gratz, your half way to 200. More missions, MORE!",
				"Woot! Congrats, don't forget to put on your 1k token board.");
		} else if ($level == 150) {
			$dingText = array(
				"S10 time!!!",
				"Time to ungimp yourself! Horray!. Congrats =)",
				"What starts with A, and ends with Z? <green>ALIUMZ!<end>",
				"Wow, is it that time already? TL 5 really? You sure are moving along! Gratz");
		} else if ($level == 180) {
			$dingText = array(
				"Congratz! Now go kill some <green>aliumz<end> at S13/28/35!!",
				"Only 20 more froob levels to go! HOORAH!",
				"Yay, only 10 more levels until TL 6! Way to go!");
		} else if ($level == 190) {
			$dingText = array(
				"Wow holy shiznits! Your TL 6 already? Congrats!",
				"Just a few more steps and your there buddy, keep it up!",
				"Almost party time! just a bit more to go ".$sender.". We'll be sure to bring you a cookie!");
		} else if ($level == 200) {
			$dingText = array(
				"Congratz! The big Two Zero Zero!!! Party at ".$sender."'s place",
				"Best of the best in froob terms, congratulations!",
				"What a day indeed. Finally done with froob levels. Way to go!");
		} else if ($level > 200 && $level < 220) {
			$dingText = array(
				"Congratz! Just a few more levels to go!",
				"Enough with the dingin you are making the fr00bs feel bad!",
				"Come on save some dings for the rest!");
		} else if ($level == 220) {
			$dingText = array(
				"Congratz! You have reached the end of the line! No more fun for you :P",
				"Holy shit, you finally made it! What an accomplishment... Congratulations ".$sender.", for reaching a level reserved for the greatest!",
				"I'm going to miss you a great deal, because after this, we no longer can be together ".$sender.". We must part so you can continue getting your research and AI levels done! Farewell!",
				"How was the inferno grind? I'm glad to see you made it through, and congratulations for finally getting the level you well deserved!",
				"Our congratulations, to our newest level 220 member, ".$sender.", for his dedication. We present him with his new honorary rank, Chuck Norris!");
		} else if ($level > 220) {
			$dingText = array(
				"Umm...no.",
				"You must be high, because that number is too high...",
				"Ha, ha... ha, yeah... no...",
				"You must be a GM or one hell of an exploiter, that number it too high!",
				"Yeah, and I'm Chuck Norris...",
				"Not now, not later, not ever... find a more reasonable level!");
		} else {
			$lvl = (int)round(220 - $level);
			$dingText = array(
				"Ding ding ding... now ding some more!",
				"Keep em coming!",
				"Don't stop now, your getting there!",
				"Come on, COME ON! Only $lvl more levels to go until 220!");
		}

		$sendto->reply($this->util->rand_array_value($dingText));
	}
}
