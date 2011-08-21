<?php

if (preg_match("/^opentimes$/i", $message, $arr)) {

	$title = "Scouted Bases";
	
	$sql = "
		SELECT
			*
		FROM
			tower_site t
			JOIN scout_info s ON (t.playfield_id = s.playfield_id AND s.site_number = t.site_number)
			JOIN playfields p ON (t.playfield_id = p.id)
		ORDER BY
			close_time";
	$db->query($sql);
	$numrows = $db->numrows();
	
	$blob = '';
	while (($row = $db->fObject()) != false) {
		$gas_level = getGasLevel($row->close_time);
		$gas_change_string = "$gas_level->color $gas_level->gas_level - $gas_level->next_state in " . gmdate('H:i:s', $gas_level->gas_change) . "<end>";

		$site_link = Text::make_chatcmd("$row->short_name $row->site_number", "/tell <myname> lc $row->short_name $row->site_number");
		$open_time = $row->close_time - (3600 * 6);
		if ($open_time < 0) {
			$open_time += 86400;
		}
		
		$faction = strtolower($row->faction);
		$blob .= "$site_link <white>- {$row->min_ql}-{$row->max_ql}, $row->ct_ql CT, <$faction>$row->guild_name<end>, $gas_change_string [by $row->scouted_by]<end>\n";
	}
	
	if ($numrows > 0) {
		$msg = Text::make_blob($title, $title . "\n\n" . $blob);
	} else {
		$msg = "No sites found.";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>