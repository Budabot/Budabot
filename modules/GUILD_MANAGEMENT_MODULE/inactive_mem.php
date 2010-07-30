<?
   /*
   ** Author: Legendadv (RK2)
   ** Description: Lookup inactive org members
   */
$table = "org_members_<myname>";  //org_members_<myname>
if (preg_match("/^inactivemem ([0-9]+)/i", $message, $arr)) {
	
	if($this->vars["my guild id"] == "") {
	    bot::send("The Bot needs to be in an org to show the orgmembers.", $sender);
		return;
	}
	
	$inactive_deadline = time() - (2592000*$arr[1]);
	$db->query("SELECT * FROM $table LEFT JOIN alts ON name=alt WHERE `mode` != 'del' AND `logged_off` != '0' AND `logged_off` < $inactive_deadline  ORDER BY name");  
	$members = $db->numrows();
  	if($members == 0) {
	    bot::send("No members recorded.", $sender);    
		return;
	}
	$numinactive = 0;
	$highlight = 0;
	$list = "<header>::::: Inactive Members of {$this->vars['my guild']} :::::<end>\n\n";
	$list .="<u>Settings</u>\n";
	$list .="Timespan: Showing members who have been inactive for more than <blue>{$arr[1]}<end> month(s).\n";
	$list .="<red>**Be careful with clicking the Org Kick links.  It will cause you to /org kick, and the bot can't help you undo that.<end>\n";
	$list .="<u>Name [Main], Last seen, Options</u>\n";
	
	$data = $db->fObject("all");
	foreach($data as $row) {
		$kick = 1;
		$logged = 0;
		$main = $row->main;
		if($row->main != "") {
			$db->query("SELECT * FROM alts LEFT JOIN $table ON alt = name WHERE `main` = '{$row->main}'");
	
			while($row1 = $db->fObject()) {
				if($row1->logged_off > $logged) {
					$logged = $row1->logged_off;
					$lasttoon = $row1->name;
				}
				
				if($row1->logged_off > $inactive_deadline)
					$kick = 0;
					
					
				$main = $row1->main;
			}
		}
		
		if($kick) {
			$numinactive++;
			$kick = " [".bot::makeLink("Kick {$row->name}?", "/k {$row->name}", "chatcmd")."]"; ///org kick {$row->name}
			$alts = bot::makeLink("Alts", "/tell <myname> alts {$row->name}", "chatcmd");
			$logged = $row->logged_off;
			$lasttoon = $row->name;
			
			$player = $row->name."; Main: $main; [{$alts}]$kick\nLast seen on [$lasttoon] on ".date("Y-m-d",$logged)."\n--------------------\n";
			if ($highlight == 1) {
				$list .= "<white>$player<end>";
				$highlight = 0;
			}
			else {
				$list .= $player;
				$highlight = 1;
			}
			
		}
	}
	$msg = bot::makeLink("$numinactive Inactive Org Members (Since {$arr[1]} months)",$list);
	if($msg != "")
		bot::send($msg, $sender);
} else {
	$syntax_error = true;
}
?>
