<?php

include 'clusterdb.php';

if (preg_match("/^cluster (.+)$/i", $message, $arr)) {
	$name = trim($arr[1]);
	
	$info = "";
	$found = 0;
	forEach ($cl_list as $key => $value) {
		if (matches($key, $name)) {
			$found++;
			if ($found > 1) {
				$info .= "\n\n<pagebreak>";
			}
			$info .= "<u>$key Cluster</u>:\n<tab><font color=#ffcc33>Shiny</font>: ".$value[0].
					 "<tab><font color=#ffff55>Bright</font>: ".$value[1].
					 "<tab><font color=#FFFF99>Faded</font>: ".$value[2];
		}
	}
	if ($found == 0) { 
		$sendto->reply("No matches found.");
		return; 
	} else if ($found == 1) {
		$sendto->reply($info);
	} else {
		$inside = "Your query of <yellow>".$name."<end> returned the following results:\n\n";
		$inside .= $info;
		$inside .= "\n\nby Imoutochan (RK1)";
	
		$windowlink = Text::make_blob("::Cluster search results ($found)::", $inside);
		$sendto->reply($windowlink);
	}
} else {
	$syntax_error = true;
}

?>