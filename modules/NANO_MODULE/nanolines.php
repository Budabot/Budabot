<?php

if (preg_match("/^nanolines$/i", $message, $arr)) {
	$sql = "SELECT DISTINCT profession FROM nanolines ORDER BY profession ASC";
	$data = $db->query($sql);

	$blob = '';
	forEach ($data as $row) {
		$blob .= Text::make_chatcmd($row->profession, "/tell <myname> <symbol>nanolines $row->profession");
		$blob .= "\n";
	}
	$blob .= "\n\nAO Nanos by Voriuste";
	$blob .= "\nModule created by Tyrence (RK2)";

	$msg = Text::make_blob('Nanolines', $blob);

	$chatBot->send($msg, $sendto);
} else if (preg_match("/^nanolines ([0-9]+)$/i", $message, $arr)) {
	$nanoline_id = $arr[1];

	$sql = "SELECT * FROM nanolines WHERE id = ?";
	$row = $db->queryRow($sql, $nanoline_id);

	$msg = '';
	if ($row !== null) {
		$blob = '';

		$sql = "
			SELECT
				n1.lowid,
				lowql,
				n1.name,
				location
			FROM
				nanos n1
				JOIN nano_nanolines_ref n2
					ON (n1.lowid = n2.lowid)
			WHERE
				n2.nanolineid = ?
			ORDER BY
				lowql DESC, name ASC";
		$data = $db->query($sql, $nanoline_id);

		forEach ($data as $row) {
			$$blob .= Text::make_item($row->lowid, $row->lowid, $row->lowql, $row->name);
			$$blob .= " [$row->lowql] $row->location\n";
		}

		$$blob .= "\n\nAO Nanos by Voriuste";
		$$blob .= "\nModule created by Tyrence (RK2)";

		$msg = Text::make_blob("$row->profession $row->name Nanos", $$blob);

	} else {
		$msg = "No nanoline found.";
	}

	$chatBot->send($msg, $sendto);
} else if (preg_match("/^nanolines (.*)$/i", $message, $arr)) {
	$profession = Util::get_profession_name($arr[1]);
	if ($profession == '') {
		$msg = "Please choose one of these professions: adv, agent, crat, doc, enf, eng, fix, keep, ma, mp, nt, sol, shade, or trader";
		$chatBot->send($msg, $sendto);
		return;
	}

	$sql = "SELECT * FROM nanolines WHERE profession LIKE ? ORDER BY name ASC";
	$data = $db->query($sql, $profession);

	$blob = '';
	forEach ($data as $row) {
		if ($setting->get("shownanolineicons") == "1") {
			$blob .= "<img src='rdb://$row->image_id'>\n";
		}
		$blob .= Text::make_chatcmd("$row->name", "/tell <myname> <symbol>nanolines $row->id");
		$blob .= "\n";
	}
	$blob .= "\n\nAO Nanos by Voriuste";
	$blob .= "\nModule created by Tyrence (RK2)";
	$msg = Text::make_blob("$profession Nanolines", $blob);

	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
