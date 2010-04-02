<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Items DB search
   ** Version: 0.8
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.11.2005
   ** Date(last modified): 22.11.2006
   ** 
   ** Copyright (C) 2005, 2006 Carsten Lohmann
   **
   ** Licence Infos: 
   ** This file is part of Budabot.
   **
   ** Budabot is free software; you can redistribute it and/or modify
   ** it under the terms of the GNU General Public License as published by
   ** the Free Software Foundation; either version 2 of the License, or
   ** (at your option) any later version.
   **
   ** Budabot is distributed in the hope that it will be useful,
   ** but WITHOUT ANY WARRANTY; without even the implied warranty of
   ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   ** GNU General Public License for more details.
   **
   ** You should have received a copy of the GNU General Public License
   ** along with Budabot; if not, write to the Free Software
   ** Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   */
   
if(eregi("^items ([0-9]+) (.+)$", $message, $arr)){
    $ql = $arr[1];
    if(!($ql >= 1 && $ql <= 500)) {
        $msg = "No valid Ql specified(1-500)";
        if($type == "msg")
            bot::send($msg, $sender);
        elseif($type == "priv")
        	bot::send($msg);
        elseif($type == "guild")
        	bot::send($msg, "guild");
        return;
    }
    $name = $arr[2];
} else if(eregi("^items (.+)$", $message, $arr)){
    $name = $arr[1];
    $ql = false;
} else {
  	$msg = "You need to specify an item to be searched for!";
   	if($type == "msg")
		bot::send($msg, $sender);
	elseif($type == "priv")
	 	bot::send($msg);
	elseif($type == "guild")
	  	bot::send($msg, "guild");
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
	
$db->query("SELECT * FROM aodb WHERE $query ORDER BY `name` LIMIT 0, {$this->settings["maxitems"]}");
$num = $db->numrows();
if($num == 0) {
  	if($ql)
	    $msg = "No items found with QL <highlight>$ql<end>. Maybe try fewer keywords.";
	else
	    $msg = "No items found. Maybe try fewer keywords.";
   	if($type == "msg")
		bot::send($msg, $sender);
	elseif($type == "priv")
	 	bot::send($msg);
	elseif($type == "guild")
	  	bot::send($msg, "guild");
	return;
}

$countitems = 0;

while($row = $db->fObject()) {
	if(!isset($itemlist[$row->name])) {
		$itemlist[$row->name] = array(array("lowid" => $row->lowid, "highid" => $row->highid, "lowql" => $row->lowql, "highql" => $row->highql, "icon" => $row->icon));
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
			$tmp[] = array("lowid" => $row->lowid, "highid" => $row->highid, "lowql" => $row->lowql, "highql" => $row->highql, "icon" => $row->icon);
			$itemlist[$row->name] = $tmp;
			$countitems++;
		}
	}
}

if($countitems == 0) {
  	if($ql)
	    $msg = "No items found with QL <highlight>$ql<end>. Maybe try fewer keywords.";
	else
	    $msg = "No items found. Maybe try fewer keywords.";
   	if($type == "msg")
		bot::send($msg, $sender);
	elseif($type == "priv")
	 	bot::send($msg);
	elseif($type == "guild")
	  	bot::send($msg, "guild");
	return;
}

if($countitems > 3) {
	foreach($itemlist as $name => $item1) {
	 	foreach($item1 as $key => $item) {
			$name = str_replace("\'", "'", $name);
			$name = str_replace("&#58;", ":", $name);
			$name = str_replace("&amp;", "&", $name);
	        $list .= "<img src=rdb://".$item["icon"]."> \n";
	        if($ql) {
		        $list .= "QL $ql ".bot::makeItem($item["lowid"], $item["highid"], $ql, $name);
			} else {
		        $list .= bot::makeItem($item["lowid"], $item["highid"], $item["highql"], $name);		  
			}
	
	        if($item["lowql"] != $item["highql"])
		        $list .= " (QL".$item["lowql"]." - ".$item["highql"].")\n\n";
	        else
	    	    $list .= " (QL".$item["lowql"].")\n\n";
	    }
    }
    $list = "<header>::::: Item Search Result :::::<end>\n\n".$list;
    $link = bot::makeLink('Click here to see the results', $list);

	//Send Itemslist
	if($type == "msg")
	    bot::send($link, $sender);
	elseif($type == "priv")
	  	bot::send($link);
	elseif($type == "guild")
	  	bot::send($link, "guild");

	//Show how many items found		
    $msg = "<highlight>".$countitems."<end> results in total";
    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
      	bot::send($msg);
    elseif($type == "guild")
      	bot::send($msg, "guild");
      	
	//Show a warning if the maxitems are reached
	if($countitems == $this->settings["maxitems"]) {
	    $msg = "The output has been limited to <highlight>{$this->settings["maxitems"]}<end> items. Specify your search more if your item isn´t listed.";
	    if($type == "msg")
	        bot::send($msg, $sender);
	    elseif($type == "priv")
	      	bot::send($msg);
	    elseif($type == "guild")
	      	bot::send($msg, "guild");
	}
} else {
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
	    }
    }

	// Send info back
	if($type == "msg")
	    bot::send($link, $sender);
	elseif($type == "priv")
	  	bot::send($link);
	elseif($type == "guild")
	  	bot::send($link, "guild");
}
?>