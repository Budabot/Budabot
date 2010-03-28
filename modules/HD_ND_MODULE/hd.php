<?php

$header = "<header>::::: Heal Delta :::::<end>\n\n"	;
$footer = "<tab><img src=tdb://id:GFX_GUI_FRIENDLIST_SPLITTER>\n\nby Imoutochan, RK1";
	
if (eregi("^hd$", $message)) {
	$inside = $header;
	$inside .= "Stamina  -> HD tick standing/sitting\n<tab><img src=tdb://id:GFX_GUI_FRIENDLIST_SPLITTER>\n";
	for ($i = 0; $i < 28; $i++) {
		$inside .= "<font color=#".($i % 2 == 0 ? "ff9999>" : "ffcccc>").(strlen($i * 30) < 2 ? "0" : "").(strlen($i * 30) < 3 ? "0" : "").($i * 30).
				   "<tab><tab>-><tab>".(strlen(29 - $i) < 2 ? "0" : "").(29 - $i)."s / ".(strlen(floor((29 - $i)/2)) < 2 ? "0" : "").floor((29 - $i)/2)."s</font>\n".
				   ($i % 3 == 2 ? "<tab><img src=tdb://id:GFX_GUI_FRIENDLIST_SPLITTER>\n" : "");
	}
	$msg = bot::makeLink("Heal delta tick info", $inside.$footer);
} elseif (eregi("^hd ([0-9]+)$", $message, $arr)) {
	$tick = (29-floor($arr[1]/30)); 
	if ($tick < 2) $tick = 2;
	$msg = "\nWith <highlight>".$arr[1]." stamina<end> you will have <highlight>".$tick."s<end> standing and <highlight>".floor($tick/2)."s<end> sitting HD tick.";
	$msg .= ($tick > 2 ? "\nWith <highlight>".((30 - $tick) * 30)." stamina<end>, you can reduce the standing tick speed to <highlight>".($tick - 1)."s<end>." :
			 "\nYou have <highlight>capped<end> your HD tick speed.");
} else {
	$msg = "Correct !hd usage:\n <highlight>!hd &lt;your stamina><end>, e.g. <highlight>!hd 350<end>.";
}

bot::send($msg, $sendto);

?>