<?php
	include 'buffstuffalias.php';
	include 'buffstuffdb.php';
	include 'db_utils.php';

	$header = "<header>::::: Buff item info :::::<end>\n\n";
	$footer = "by Imoutochan, RK1";

	$help = $header;
	$help .= "<font color=#3333CC>Buffitem:</font>\n";
	$help .= "/tell <myname> <symbol>buffitem [<orange>name<end>]\n";
	$help .= "[<orange>name<end>] = full or partial name of the item\n\n";
	$help .= "Example:\n";
	$help .= "You want to look up Nova Dillon armor.\n";
	$help .= "<a href='chatcmd:///tell <myname> <symbol>buffitem nova dil'>/tell <myname> <symbol>buffitem nova dil</a>\n\n";
	$help .= $footer;

	$helplink = $this->makeLink("::How to use buffitem::", $help);

	if (preg_match("/^buffitem (.+)$/i", $message, $arr)) {
		$name = $arr[1];
		
		$results = array();
		$found = 0;
		// search item line database
		foreach ($buffitems as $key => $value) {
			unset($info);
			if (matches($key, $name)) {
				$found++;
				$info =	make_info($key, $value);
				array_unshift($results, array($key, $info));
			}
		}
		// search  item alias database
		foreach ($aliases as $key => $values) {
			unset($info);
			if (contains($values, $name) && !(duplicate($key, $results))) {
				$found++;
				$buffitem = $buffitems[$key];
				$alias = get_alias($values, $name);
				$info =	"Item <green>$alias<end>\nbelongs into the line of ";
				$info .= make_info($key, $buffitem);
				array_unshift($results, array($key, $info, $alias));
			}
		}
		
		if ($found == 0) {
			$this->send("No matches, sorry.", $sendto);
			return;
		} else {
			$inside = $header;
			$inside .= "Your query of <yellow>".$name."<end> returned the following item line(s):\n\n";
			if ($found == 1) {
				$inside .= $results[0][1]."\n\n";
			} else {
				foreach ($results as $result) {
					$inside .= "- <a href='chatcmd:///tell <myname> <symbol>buffitem ".$result[0]."'>".$result[0]."</a>".
					           (sizeof($result) == 3 ? " (".$result[2].")" : "")."\n";
				}
				$inside .= "\n".sizeof($results)." results found, please pick one by clicking it\n\n";
			}
			$inside .= $footer;
			$windowlink = $this->makeLink("Buff item search results", $inside);
		}
		$this->send($windowlink, $sendto);
		$this->send("<highlight>$found<end> result(s) in total", $sendto);
	} else {
		$this->send($helplink, $sendto);
	}
	
?>