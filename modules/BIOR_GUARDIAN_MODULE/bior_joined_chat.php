<?php

$whois = Player::get_by_name($sender);
if ($whois !== null && ($whois->profession == "Adventurer" || $whois->profession == "Keeper" || $whois->profession == "Enforcer" || $whois->profession == "Engineer") && $whois->level >= 201) {
	$chatBot->data['bior'][$sender]["b"] = "ready";
	$chatBot->data['bior'][$sender]["lvl"] = $whois->level;
}
?>
