<?
   /*
   ** The Majority of this code was written by Derroylo (RK2) for the 
   ** Budabot Items Module.  I just hacked it to use Nano DB from a
   ** Similar Bebot nano Module.
   **
   ** Healnjoo RK2
   */
   
if(eregi("^nano ([0-9]+) (.+)$", $message, $arr)){
    $ql = $arr[1];
    if(!($ql >= 1 && $ql <= 500)) {
        $msg = "No valid Ql specified(1-500)";
        bot::send($msg, $sendto);
        return;
    }
    $name = $arr[2];
} else if(eregi("^nano (.+)$", $message, $arr)){
    $name = $arr[1];
    $ql = false;
} else {
  	$msg = "You need to specify a nano to search for!";
   	bot::send($msg, $sendto);
	return;  	
}

$name = str_replace(":", "&#58;", $name);
$name = str_replace("&", "&amp;", $name);

$tmp = explode(" ", $name);
$first = true;
foreach($tmp as $key => $value) {
	if($first) {
		$query .= "`name` LIKE \"%$value%\"";
		$first = false;
	} else
		$query .= " AND `name` LIKE '%$value%'";		
}

if($ql)
	$query .= " AND `lowql` <= $ql AND `highql` >= $ql";

$db->query("SELECT * FROM nanos WHERE $query ORDER BY name, lowql  LIMIT 0, {$this->settings["maxnano"]}");
$num = $db->numrows();
if($num == 0) {
  	if($ql)
	    $msg = "No nanos found with QL <highlight>$ql<end>. Maybe try fewer keywords.";
	else
	    $msg = "No nanos found. Maybe try fewer keywords.";
   	bot::send($msg, $sendto);
	return;
}

$countitems = 0;

while($row = $db->fObject()) {
	if(!isset($itemlist[$row->name])) {
		$itemlist[$row->name] = array(array("lowid" => $row->lowid, "highid" => $row->highid, "lowql" => $row->lowql, "highql" => $row->highql, "icon" => $row->icon, "location" => $row->location));
		$countitems++;
	} elseif(isset($itemlist[$row->name])) {
	  	if($itemlist[$row->name][0]["lowql"] > $row->lowql) {
		    $itemlist[$row->name][0]["lowql"] = $row->lowql;
		    $itemlist[$row->name][0]["lowid"] = $row->lowid;
		} elseif($itemlist[$row->name][0]["highql"] < $row->highql) {
		    $itemlist[$row->name][0]["highql"] = $row->highql;
		    $itemlist[$row->name][0]["highid"] = $row->highid;		    
		} else {
			$tmp = $itemlist[$row->name];
			$tmp[] = array("lowid" => $row->lowid, "highid" => $row->highid, "lowql" => $row->lowql, "highql" => $row->highql, "icon" => $row->icon, "location" => $row->location);
			$itemlist[$row->name] = $tmp;
			$countitems++;
		}
	}
}

if($countitems == 0) {
  	if($ql)
	    $msg = "No nanos found with QL <highlight>$ql<end>. Maybe try fewer keywords.";
	else
	    $msg = "No nanos found. Maybe try fewer keywords.";
   	bot::send($msg, $sendto);
	return;
}

if($countitems > 1) {
	foreach($itemlist as $name => $item1) {
	 	foreach($item1 as $key => $item) {
			$name = str_replace("\'", "'", $name);
			$name = str_replace("&#58;", ":", $name);
			$name = str_replace("&amp;", "&", $name);
//	        $list .= "<img src=rdb://".$item["icon"]."> \n";
	        if($ql) {
		        $list .= "QL $ql ".bot::makeItem($item["lowid"], $item["highid"], $ql, $name);
			} else {
		        $list .= bot::makeItem($item["lowid"], $item["highid"], $item["highql"], $name);		  
			}
	
	        if($item["lowql"] != $item["highql"])
		        $list .= " (QL".$item["lowql"]." - ".$item["highql"].") ";
	        else
	    	    $list .= " (QL".$item["lowql"].") ";
			
			if($item['location'])
				$list .= "\nLocated: ".$item['location']."\n\n";
			else
				$list .= "\nLocated: Unknown";
	    }
    }
    $list = "<header>::::: Nano Search Result :::::<end>\n\n".$list;
    $link = bot::makeLink("$countitems results in total", $list);
    bot::send($link, $sendto);
      	
	//Show a warning if the maxnano are reached
	if($countitems == $this->settings["maxnano"]) {
	    $msg = "The output has been limited to <highlight>{$this->settings["maxnano"]}<end> items. Specify your search more if your item isn´t listed.";
	    bot::send($msg, $sendto);
	}
} 

else {
    foreach($itemlist as $name => $item1) {
   	 	foreach($item1 as $key => $item) {
		    $name = str_replace("\'", "'", $name);
			$name = str_replace("&#58;", ":", $name);
			$name = str_replace("&amp;", "&", $name); 
	        if($ql)
		        $link .= "\n QL $ql ".bot::makeItem($item["lowid"], $item["highid"], $ql, $name);
			else
		        $link .= "\n".bot::makeItem($item["lowid"], $item["highid"], $item["highql"], $name);
	        
	        if($item["lowql"] != $item["highql"])
		        $link .= " (QL".$item["lowql"]." - ".$item["highql"].")";
	        else
	            $link .= " (QL".$item["lowql"].")";

			if($item['location'])
				$link .= "\nLocated: ".$item['location'];
			else
				$link .= "\nLocated: Unknown";
	    }
    }

	// Send info back
	bot::send($link, $sendto);
}

?>