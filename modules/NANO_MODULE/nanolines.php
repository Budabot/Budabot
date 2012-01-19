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

	$sendto->reply($msg);
} else if (preg_match("/^nanolines ([0-9]+)$/i", $message, $arr)) {
	$nanoline_id = $arr[1];

	$sql = "SELECT * FROM nanolines WHERE id = ?";
	$nanoline = $db->queryRow($sql, $nanoline_id);

	$msg = '';
	if ($nanoline !== null) {
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

		forEach ($data as $nano) {
			$$blob .= Text::make_item($nano->lowid, $nano->lowid, $nano->lowql, $nano->name);
			$$blob .= " [$nano->lowql] $nano->location\n";
		}

		$$blob .= "\n\nAO Nanos by Voriuste";
		$$blob .= "\nModule created by Tyrence (RK2)";

		$msg = Text::make_blob("$nanoline->profession $nanoline->name Nanos", $$blob);

	} else {
		$msg = "No nanoline found.";
	}

	$sendto->reply($msg);
} else if (preg_match("/^nanolines (.*)$/i", $message, $arr)) {
	$profession = Util::get_profession_name($arr[1]);
	if ($profession == '') {
		$msg = "Please choose one of these professions: adv, agent, crat, doc, enf, eng, fix, keep, ma, mp, nt, sol, shade, or trader";
		$sendto->reply($msg);
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

	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
