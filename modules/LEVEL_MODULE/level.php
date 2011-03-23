<?php

if (preg_match("/^level ([0-9]+)$/i", $message, $arr)) {
	$level = $arr[1];
	if (($row = Level::get_level_info($level)) != false) {
        $msg = "<white>L $row->level: team {$row->teamMin}-{$row->teamMax}<end><highlight> | <end><cyan>PvP {$row->pvpMin}-{$row->pvpMax}<end><highlight> | <end><yellow>".number_format($row->xpsk)." XP/SK<end><highlight> | <end><orange>Missions {$row->missions}<end><highlight> | <end><blue>{$row->tokens} token(s)<end>";
    } else {
        $msg = "The level must be between <highlight>1<end> and <highlight>220<end>";
    }

    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>