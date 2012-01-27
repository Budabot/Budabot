<?php

$bio_regex = "<a href=\"itemref:\/\/([0-9]+)\/([0-9]+)\/([0-9]+)\">Solid Clump of Kyr\'Ozch Bio-Material<\/a>";
if (preg_match("/^bio(( *${bio_regex})+)$/i", $message, $arr)){
	$bios = explode("*", preg_replace("/> *</", ">*<", $arr[1]));
	
	$blob = '';
	forEach ($bios as $bio) {
		preg_match("/^${bio_regex}$/i", trim($bio), $arr2);
		$highid = $arr2[2];
		$ql = $arr2[3];
		switch ($highid) {
			case 247707:
			case 247708:
				$bioinfo = "1";
				$name = "Kyr'Ozch Bio-Material - Type 1";
				break;
			case 247709:
			case 247710:
				$bioinfo = "2";
				$name = "Kyr'Ozch Bio-Material - Type 2";
				break;
			case 247717:
			case 247718:
				$bioinfo = "3";
				$name = "Kyr'Ozch Bio-Material - Type 3 ";
				break;
			case 247711:
			case 247712:
				$bioinfo = "4";
				$name = "Kyr'Ozch Bio-Material - Type 4";
				break;
			case 247713:
			case 247714:
				$bioinfo = "5";
				$name = "Kyr'Ozch Bio-Material - Type 5";
				break;
			case 247715:
			case 247716:
				$bioinfo = "12";
				$name = "Kyr'Ozch Bio-Material - Type 12 ";
				break;
			case 247719:
			case 247720:
				$bioinfo = "13";
				$name = "Kyr'Ozch Bio-Material - Type 13";
				break;
			case 288699:
			case 288700:
				$bioinfo = "48";
				$name = "Kyr'Ozch Bio-Material - Type 48";
				break;
			case 247697:
			case 247698:
				$bioinfo = "76";
				$name = "Kyr'Ozch Bio-Material - Type 76";
				break;
			case 247699:
			case 247700:
				$bioinfo = "112";
				$name = "Kyr'Ozch Bio-Material - Type 112";
				break;
			case 247701:
			case 247702:
				$bioinfo = "240";
				$name = "Kyr'Ozch Bio-Material - Type 240";
				break;
			case 247703:
			case 247704:
				$bioinfo = "880";
				$name = "Kyr'Ozch Bio-Material - Type 880";
				break;
			case 247705:
			case 247706:
				$bioinfo = "992";
				$name = "Kyr'Ozch Bio-Material - Type 992";
				break;
			case 247102:
			case 247103:
				$bioinfo = "pristine";
				$name = "Pristine Kyr'Ozch Bio-Material";
				break;
			case 247104:
			case 247105:
				$bioinfo = "mutated";
				$name = "Mutated Kyr'Ozch Bio-Material";
				break;
			case 247764:
			case 254804:
				$bioinfo = "serum";
				$name = "Kyr'Ozch Viral Serum";
				break;
			default:
				$bioinfo = "";
				$name = "Unknown Bio-Material";
				continue;
		}
		
		$biotype_link = Text::make_chatcmd($name, "/tell <myname> bioinfo $bioinfo $ql");
		$blob .= $biotype_link . "\n\n";
	}
	
	if (count($bios) == 1) {
		// make the bot think they actually typed the !bioinfo command
		$commandManager = Registry::getInstance('commandManager');
		$commandManager->process($type, "bioinfo $bioinfo $ql", $sender, $sendto);
	} else {
		$msg = Text::make_blob("Identified Bio-Materials", $blob);
		$sendto->reply($msg);
	}
} else {
	$syntax_error = true;
}

?>