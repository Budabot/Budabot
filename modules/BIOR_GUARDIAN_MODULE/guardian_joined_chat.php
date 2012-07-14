<?php

$whois = Player::get_by_name($sender);
if ($whois !== null && $whois->profession == "Soldier" && $whois->level >= 205) {
	$chatBot->data['guard'][$sender]["g"] = "ready";
	$chatBot->data['guard'][$sender]["lvl"] = $whois->level;
}
?>
