<?php

if (preg_match("/^leprocs (.+)$/i", $message, $arr)) {
	$profession = Util::get_profession_name($arr[1]);
	if ($profession == '') {
		$msg = "Please choose one of these professions: adv, agent, crat, doc, enf, eng, fix, keep, ma, mp, nt, sol, shade, or trader";
		$chatBot->send($msg, $sendto);
		return;
	}

	$db->query("SELECT * FROM leprocs WHERE profession LIKE '$profession' ORDER BY proc_type ASC, research_lvl DESC");
	$data = $db->fObject('all');
	if ($db->numrows() == 0) {
	    $msg = "No procs found for profession '$profession'.";
	} else {
		$blob = "<header> :::::: LE Procs '$profession' :::::: <end>\n\n";
		$type = '';
		forEach ($data as $row) {
			if ($type != $row->proc_type) {
				$type = $row->proc_type;
				$blob .= "\n<tab>$type\n";
			}
			$blob .= "<yellow>$row->name<end> $row->duration <orange>$row->modifiers<end>\n";
		}
		
		$blob .= "\n\nProc info provided by Wolfbiter (RK1)";

		$msg = Text::make_link("$profession LE Procs", $blob, 'blob');
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>