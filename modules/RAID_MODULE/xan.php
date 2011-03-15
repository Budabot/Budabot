<?php

global $loot;
$msg = '';

if (!function_exists('get_xan_loot')) {
	function get_xan_loot($raid, $category) {
		$blob = Raid::find_raid_loot($raid, $category);
		$blob .= "\n\nXan Loot By Morgo (RK2)";
		return Text::make_link("$raid $category Loot", $blob);
	}
}

if (preg_match("/^xan$/i", $message)){
	$list = "<header>::::: Legacy of the Xan Loot :::::<end>\n\n";
	
	$list .= Text::make_link("Vortexx", "/tell <myname> <symbol>vortexx", "chatcmd") . "\n";
	$list .= "<tab>General\n";
	$list .= "<tab>Symbiants (Beta)\n";
	$list .= "<tab>Spirits (Beta)\n\n";
	
	$list .= Text::make_link("Mitaar Hero", "/tell <myname> <symbol>mitaar", "chatcmd") . "\n";
	$list .= "<tab>General\n";
	$list .= "<tab>Symbiants (Beta)\n";
	$list .= "<tab>Spirits (Beta)\n\n";
	
	$list .= Text::make_link("12 Man", "/tell <myname> <symbol>12m", "chatcmd") . "\n";
	$list .= "<tab>General\n";
	$list .= "<tab>Symbiants (Beta)\n";
	$list .= "<tab>Spirits (Beta)\n";
	$list .= "<tab>Profession Gems\n";

	$list .= "\n\nXan Loot By Morgo (RK2)";

	$msg = Text::make_link("Xan Loot", $list);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^vortexx$/i", $message)){
	$chatBot->send(get_xan_loot('Vortexx', 'General'), $sendto);
	$chatBot->send(get_xan_loot('Vortexx', 'Symbiants'), $sendto);
	$chatBot->send(get_xan_loot('Vortexx', 'Spirits'), $sendto);
} else if (preg_match("/^mitaar$/i", $message)){
	$chatBot->send(get_xan_loot('Mitaar', 'General'), $sendto);
	$chatBot->send(get_xan_loot('Mitaar', 'Symbiants'), $sendto);
	$chatBot->send(get_xan_loot('Mitaar', 'Spirits'), $sendto);
} else if (preg_match("/^12m$/i", $message)){
	$chatBot->send(get_xan_loot('12 Man', 'General'), $sendto);
	$chatBot->send(get_xan_loot('12 Man', 'Symbiants'), $sendto);
	$chatBot->send(get_xan_loot('12 Man', 'Spirits'), $sendto);
	$chatBot->send(get_xan_loot('12 Man', 'Profession Gems'), $sendto);
} else {
	$syntax_error = true;
}

?>