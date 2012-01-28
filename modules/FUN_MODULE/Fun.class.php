<?php

class Fun {
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $util;

	public function fcCommand($message, $channel, $sender, $sendto) {
		if (!preg_match("/^fc$/i", $message)) {
			return false;
		}

		$fc = array();
		$fc[] = "[in the same thread that topics got deleted for not agreeing with how moderation was happening] This is what I hate - This prevalant attitude that we restrict or otherwise take issue with people having differing opinions from us. --Kintaii";
		$fc[] = "We're more open and honest than probably pretty much any other game development team out there. --Kintaii";
		$fc[] = "[talking about the new engine] It's done when it's done. --Vhab";
		$fc[] = "[GM]: just don't kill stuff they are already attacking, you aren't a noob. you know how it goes [to person who was \"kill stealing\"]";
		$fc[] = "We are never biased in any GM actions. --Craig \"Silirrion\" Morrison";
		$fc[] = "A few people were suspended for being in contact with those orchestrating the exploit --Geoff Higgins, Funcom Support Manager";
		$fc[] = "It is against policy to knowingly consort with players active in exploits. --Geoff Higgins, Funcom Support Manager";
		$fc[] = "As far as the level of participation in said exploit, it could range anywhere from being the player who is doing the exploit (ie: hopping from org to org to disrupt the timer) to simply being grouped with the person coordinating the execution of the exploit. --Funcom Support Manager, Customer Service";
		$fc[] = "[Player]: He trained us.. [GM]: Payback after asking your team to stop";
		$fc[] = "[GM]: I'll ban everyone 1st then sort it out..";
		$fc[] = "[Player]: You're telling me that this guy admitted to you that he trained us. [GM]: After you messed up his play ... [Player]: But you will do nothing about him admitting to harass us by training us? I just want to make sure, for the record, that I understand how the policy is carried out. [GM]: I will do what is necessary";
		$fc[] = "We do not suspend people for honest mistakes. --Craig \"Silirrion\" Morrison";
		$fc[] = "You are mistaken in several of your details there I am afraid. --Craig \"Silirrion\" Morrison";
		$fc[] = "We deal with them directly, where we have all the facts and those involved know we have all the facts. --Craig \"Silirrion\" Morrison";
		$fc[] = "I can assure you that any actions taken by the GMs were apprpriate. --Craig \"Silirrion\" Morrison";
		$fc[] = "Our Game Masters only ever suspend players when their actions leave us with no choice. --Craig \"Silirrion\" Morrison";
		$fc[] = "We do not suspend accounts on hearsay or rumour, but only when our staff witness and can verify that exploiting was taking place. --Craig \"Silirrion\" Morrison";
		$fc[] = "What is does mean is that every player involved was at least in contact with those doing the exploit, whether by game chat channels or otherwise. --Craig \"Silirrion\" Morrison";
		$fc[] = "Our apologies. Your accounts have been closed due to unauthorized actives. Thank you for your understanding. --Lead GM Sojourn, Customer Satisfaction Manager";
		$fc[] = "I disagree with everything you say in this thread...even including the idea that this is the \"most stupid\" idea we've ever had. We have done way dumber things than this. --Colin \"Means\" Cragg";
		$fc[] = "Dear Arguru, You have received a warning at Anarchy Online Bulletin Board.  Reason: ------- Excessive Profanities  Joking or not, this is an inappropriate level of obscenities. Please make your point in other ways. Thank you.  --Anarrina";
		$fc[] = "I usually try to be diplomatic and avoid insults, but I'm not particularly in that mood today. --Vhab";
		$fc[] = "This sort of non-constructive trolling is not necessary to begin a thread. Should you have an issue with a particular policy, it is within forum rules to state so in a more polite manner. I have edited the objectional content in the post, you may edit it to include a calmer and more constructive criticism. --Anarrina";
		
		$dmg = rand(100,999);
		$cred = rand(10000,9999999);
		$msg = $this->util->rand_array_value($fc);
		$msg = str_replace("*name*", $sender, $msg);
		$msg = str_replace("*dmg*", $dmg, $msg);
		$msg = str_replace("*creds*", $cred, $msg);
		$sendto->reply($msg);
	}
}

?>