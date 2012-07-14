<?php

if (preg_match("/^leprocs$/i", $message)) {
	$data = $db->query("SELECT DISTINCT profession FROM leprocs ORDER BY profession ASC");
	$blob = '';
	forEach ($data as $row) {
		$professionLink = Text::make_chatcmd($row->profession, "/tell <myname> leprocs $row->profession");
		$blob .= $professionLink . "\n";
	}

	$blob .= "\n\nProc info provided by Wolfbiter (RK1), Gatester (RK2)";

	$msg = Text::make_blob("LE Procs", $blob);
	$sendto->reply($msg);
} else if (preg_match("/^leprocs (.+)$/i", $message, $arr)) {
	$profession = Util::get_profession_name($arr[1]);
	if ($profession == '') {
		$msg = "Please choose one of these professions: adv, agent, crat, doc, enf, eng, fix, keep, ma, mp, nt, sol, shade, or trader";
		$sendto->reply($msg);
		return;
	}

	$data = $db->query("SELECT * FROM leprocs WHERE profession LIKE ? ORDER BY proc_type ASC, research_lvl DESC", $profession);
	if (count($data) == 0) {
	    $msg = "No procs found for profession '$profession'.";
	} else {
		$blob = '';
		$type = '';
		forEach ($data as $row) {
			if ($type != $row->proc_type) {
				$type = $row->proc_type;
				$blob .= "\n<tab><yellow>$type<end>\n";
			}

			$proc_trigger = "<green>" . substr($row->proc_trigger, 0, 3) . ".<end>";
			$blob .= "$row->name <orange>$row->modifiers<end> $proc_trigger $row->duration\n";
		}

		$blob .= "\n\nNote: Offensive procs have a 5% chance of firing every time you attack; Defensive procs have a 10% chance of firing every time something attacks you.";

		$blob .= "\n\nProc info provided by Wolfbiter (RK1), Gatester (RK2)";

		$msg = Text::make_blob("$profession LE Procs", $blob);
	}
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
