<?php

include 'clusterdb.php';

if (preg_match("/^cluster (.+)$/i", $message, $arr)) {
	$name = trim($arr[1]);
	
	$info = "";
	$found = 0;
	forEach ($cl_list as $key => $value) {
		if ($found < 10 && matches($key, $name)) {
			$found++;
			$info .= "<u>$key Cluster</u>:\n<tab><font color=#ffcc33>Shiny</font>: ".$value[0].
					 "<tab><font color=#ffff55>Bright</font>: ".$value[1].
					 "<tab><font color=#FFFF99>Faded</font>: ".$value[2]."--";
		}
	}
	if ($found == 0) { 
		bot::send("No matches, sorry.", $sendto); return; 
	} else if ($found == 1) {
		$windowlink = str_replace("--", "", $info);
	} else {
		$inside = "<header>::::: Cluster location helper :::::<end>\n\n";
		$inside .= "Your query of <yellow>".$name."<end> returned the following results:\n\n";
		$inside .= str_replace("--", "\n\n", $info);
		$inside .= "by Imoutochan, RK1";
	
		$windowlink = Text::make_link("::Cluster search results::", $inside);
	}
	bot::send($windowlink, $sendto);
	if ($found >= 10) {
		bot::send("<highlight>More than 10 matches found!<end>\n<tab>Please specify your key words for better results.", $sendto);
	} else if ($found > 1) {
		bot::send("<highlight>$found<end> matches in total.", $sendto);
	}
} else {
	$syntax_error = true;
}
?>