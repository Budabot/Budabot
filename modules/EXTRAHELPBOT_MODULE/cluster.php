<?php
	include 'clusterdb.php';
	include 'db_utils.php';

	// help screen
	$header = "<header>::::: Cluster location helper - Version 1.00 :::::<end>\n\n";
	$footer = "by Imoutochan, RK1";

	$help = $header;
	$help .= "<font color=#3333CC>Cluster lookup usage:</font>\n";
	$help .= "/tell <myname> <symbol>cluster [<orange>name<end>]\n";
	$help .= "[<orange>name<end>] = full or partial name of the cluster\n\n";
	$help .= "Example:\n";
	$help .= "You want to know the locations of computer literacy clusters.\n";
	$help .= "<a href='chatcmd:///tell <myname> <symbol>cluster comp lit'>/tell <myname> <symbol>cluster comp lit</a>\n\n";
	$help .= $footer;

	$helplink = $this->makeLink("::How to use cluster::", $help);

	if (preg_match("/^cluster (.+)$/i", $message, $arr)) {
		$name = trim($arr[1]);
		
		$info = "";
		$found = 0;
		foreach ($cl_list as $key => $value) {
			if ($found < 10 && matches($key, $name)) {
				$found++;
				$info .= "<u>$key Cluster</u>:\n<tab><font color=#ffcc33>Shiny</font>: ".$value[0].
						 "<tab><font color=#ffff55>Bright</font>: ".$value[1].
						 "<tab><font color=#FFFF99>Faded</font>: ".$value[2]."--";
			}
		}
		if ($found == 0) { 
			$this->send("No matches, sorry.", $sendto); return; 
		} elseif ($found == 1) {
			$windowlink = str_replace("--", "", $info);
		} else {
			$inside = $header;
			$inside .= "Your query of <yellow>".$name."<end> returned the following results:\n\n";
			$inside .= str_replace("--", "\n\n", $info);
			$inside .= $footer;
		
			$windowlink = $this->makeLink("::Cluster search results::", $inside);
		}
		$this->send($windowlink, $sendto);
		if ($found >= 10) { $this->send("<highlight>More than 10 matches found!<end>\n<tab>Please specify your key words for better results.", $sendto);}
		elseif ($found > 1) $this->send("<highlight>$found<end> matches in total.", $sendto);
	} else {
		$this->send($helplink, $sendto);
	}
?>