<?php

if (!function_exists('get_xan_loot')) {
	function get_xan_loot($raid, $category) {
		$blob = Raid::find_raid_loot($raid, $category);
		$blob .= "\n\nXan Loot By Morgo (RK2)";
		return Text::make_blob("$raid $category Loot", $blob);
	}
}

if (preg_match("/^xan$/i", $message)){
	$list = Text::make_chatcmd("Vortexx", "/tell <myname> <symbol>vortexx") . "\n";
	$list .= "<tab>General\n";
	$list .= "<tab>Symbiants (Beta)\n";
	$list .= "<tab>Spirits (Beta)\n\n";

	$list .= Text::make_chatcmd("Mitaar Hero", "/tell <myname> <symbol>mitaar") . "\n";
	$list .= "<tab>General\n";
	$list .= "<tab>Symbiants (Beta)\n";
	$list .= "<tab>Spirits (Beta)\n\n";

	$list .= Text::make_chatcmd("12 Man", "/tell <myname> <symbol>12m") . "\n";
	$list .= "<tab>General\n";
	$list .= "<tab>Symbiants (Beta)\n";
	$list .= "<tab>Spirits (Beta)\n";
	$list .= "<tab>Profession Gems\n";

	$list .= "\n\nXan Loot By Morgo (RK2)";

	$msg = Text::make_blob("Legacy of the Xan Loot", $list);
	$sendto->reply($msg);
} else if (preg_match("/^vortexx$/i", $message)){
	$sendto->reply(get_xan_loot('Vortexx', 'General'));
	$sendto->reply(get_xan_loot('Vortexx', 'Symbiants'));
	$sendto->reply(get_xan_loot('Vortexx', 'Spirits'));
} else if (preg_match("/^mitaar$/i", $message)){
	$sendto->reply(get_xan_loot('Mitaar', 'General'));
	$sendto->reply(get_xan_loot('Mitaar', 'Symbiants'));
	$sendto->reply(get_xan_loot('Mitaar', 'Spirits'));
} else if (preg_match("/^12m$/i", $message)){
	$sendto->reply(get_xan_loot('12Man', 'General'));
	$sendto->reply(get_xan_loot('12Man', 'Symbiants'));
	$sendto->reply(get_xan_loot('12Man', 'Spirits'));
	$sendto->reply(get_xan_loot('12Man', 'Profession Gems'));
} else {
	$syntax_error = true;
}

?>
