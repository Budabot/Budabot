<?php

if (preg_match("/^opentimes$/i", $message)) {
	
	$sql = "
		SELECT
			*
		FROM
			tower_site t
			JOIN scout_info s ON (t.playfield_id = s.playfield_id AND s.site_number = t.site_number)
			JOIN playfields p ON (t.playfield_id = p.id)
		ORDER BY
			guild_name ASC,
			ct_ql DESC";
	$db->query($sql);
	$data = $db->fObject('all');
	
	if (count($data) > 0) {
		$blob = "<header> :::::: Scouted Bases :::::: <end>\n";
		$currentGuildName = '';
		forEach ($data as $row) {
			if ($row->guild_name != $currentGuildName) {
				$blob .= "\n";
				$currentGuildName = $row->guild_name;
			}
			$gas_level = getGasLevel($row->close_time);
			$gas_change_string = "$gas_level->color $gas_level->gas_level - $gas_level->next_state in " . date('H:i:s', $gas_level->gas_change) . "<end>";

			$site_link = Text::make_chatcmd("$row->short_name $row->site_number", "/tell <myname> lc $row->short_name $row->site_number");
			$open_time = $row->close_time - (3600 * 6);
			if ($open_time < 0) {
				$open_time += 86400;
			}
			
			$faction = strtolower($row->faction);
			$blob .= "$site_link <white>- {$row->min_ql}-{$row->max_ql}, $row->ct_ql CT, <$faction>$row->guild_name<end>, $gas_change_string [by $row->scouted_by]<end>\n";
		}
		
		$sql = "
			SELECT
				guild_name,
				sum(ct_ql) AS total_ql
			FROM
				scout_info
			GROUP BY
				guild_name
			ORDER BY
				guild_name ASC";
		$db->query($sql);
		$data = $db->fObject('all');
		$blob .= "\n\n<header> ::: Contract QLs ::: <end>\n\n";
		forEach ($data as $row) {
			$blob .= "{$row->guild_name}: <highlight>" . ($row->total_ql * 2) . "<end>\n";
		}

		$msg = Text::make_blob("Scouted Bases", $blob);
	} else {
		$msg = "No sites currently scouted.";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>