<?php

if (preg_match("/^opentimes$/i", $message)) {

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
	$data = $db->query($sql);
	$contractQls = array();
	forEach ($data as $row) {
		$contractQls[$row->guild_name] = $row->total_ql;
	}
	
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
	$data = $db->query($sql);
	
	if (count($data) > 0) {
		$blob = '';
		$currentGuildName = '';
		forEach ($data as $row) {
			if ($row->guild_name != $currentGuildName) {
				$contractQl = $contractQls[$row->guild_name];
				$faction = strtolower($row->faction);

				$blob .= "\n<u><$faction>$row->guild_name<end></u> (Total Contract QL: $contractQl)\n";
				$currentGuildName = $row->guild_name;
			}
			$gas_level = getGasLevel($row->close_time);
			$gas_change_string = "$gas_level->color $gas_level->gas_level - $gas_level->next_state in " . gmdate('H:i:s', $gas_level->gas_change) . "<end>";

			$site_link = Text::make_chatcmd("$row->short_name $row->site_number", "/tell <myname> lc $row->short_name $row->site_number");
			$open_time = $row->close_time - (3600 * 6);
			if ($open_time < 0) {
				$open_time += 86400;
			}

			$blob .= "$site_link <white>- {$row->min_ql}-{$row->max_ql}, $row->ct_ql CT, $gas_change_string [by $row->scouted_by]<end>\n";
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