<?php

$header = "<header>::::: Nano Delta :::::<end>\n\n"	;
$footer = "<img src=tdb://id:GFX_GUI_FRIENDLIST_SPLITTER>\n\nby Imoutochan, RK1";
	
if (preg_match("/^nd$/i", $message)) {
	$inside = $header;
	$inside .= "Psychic  -> nd tick delay\n<img src=tdb://id:GFX_GUI_FRIENDLIST_SPLITTER>\n";
	for ($i = 0; $i < 14; $i++) {
		$inside .= "<font color=#".($i % 2 == 0 ? "339999>" : "66cccc>").(strlen($i * 60) < 2 ? "0" : "").(strlen($i * 60) < 3 ? "0" : "").($i * 60).
				   "<tab><tab>-><tab>".(strlen(28 - 2 * $i) < 2 ? "0" : "").(28 - 2 * $i)."s</font>\n".
				   ($i % 3 == 2 ? "<img src=tdb://id:GFX_GUI_FRIENDLIST_SPLITTER>\n" : "");
	}
	$msg = bot::makeLink("Nano delta tick info", $inside.$footer);
} elseif (preg_match("/^nd ([0-9]+)$/i", $message, $arr)) {
	$tick = (28-floor($arr[1]/30)); 
	if ($tick < 2) $tick = 2;
	$msg = "\nWith <highlight>".$arr[1]." psychic<end> your nano delta will tick every <highlight>".$tick."s<end>.";
	$msg .= ($tick > 2 ? "\nWith <highlight>".((30 - $tick) * 30)." psychic<end>, your can reduce the tick speed to <highlight>".($tick - 2)."s<end>." :
			 "\nYou have <highlight>capped<end> your nd tick speed.");
} else {
	$msg = "Correct !nd usage:\n <highlight>!nd &lt;your psychic><end>, e.g. <highlight>!nd 350<end>.";
}

bot::send($msg, $sendto);

?>