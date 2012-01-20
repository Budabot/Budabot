<?php

global $loot;
global $residual;
if (preg_match("/^loot clear$/i", $message)) {
  	$loot = "";
	$residual = "";
  	$msg = "Loot has been cleared by <highlight>$sender<end>.";
  	$chatBot->sendPrivate($msg);

	if ($type != 'priv') {
		$sendto->reply($msg);
	}
} else if (preg_match("/^loot ([0-9]+)$/i", $message, $arr)) {
	$id = $arr[1];
	
	$sql = "SELECT * FROM raid_loot WHERE id = ?";
	$item = $db->queryRow($sql, $id);
	
	if ($item === null) {
		$msg = "Could not find item with id <highlight>$id<end> to add.";
		$sendto->reply($msg);
		return;
	}
	
	$dontadd = 0;
	forEach ($loot as $key => $value) {
		if ($value["name"] == $item->name){
			$loot[$key]["multiloot"] = $value["multiloot"]+1;
			$total = $value["multiloot"]+1;
			$dontadd = 1;
			$slot = $key;
		}
	}

	if ($dontadd == 0) {
		if (is_array($loot)) {
			$nextloot = count($loot) + 1;
		} else {
			$nextloot = 1;
		}
		$loot[$nextloot]["name"] = $item->name;
		$loot[$nextloot]["linky"] = "<a href='itemref://{$item->lowid}/{$item->highid}/{$item->ql}'>{$item->name}</a>";
		$loot[$nextloot]["icon"] = $item->imageid;
		$loot[$nextloot]["multiloot"] = 1;
		$msg = "<highlight>".$item->name."<end> will be rolled in Slot <highlight>#".$nextloot;
	} else {
		$msg = "<highlight>".$item->name."<end> will be rolled in Slot <highlight>#".$slot."<end> as multiloot. Total: <yellow>".$total."<end>";
	}
	$msg .= "\nTo add use <symbol>add ".$nextloot.", or <symbol>rem to remove yourself";
	$chatBot->sendPrivate($msg);
} else if (preg_match("/^loot (.+)$/i", $message, $arr)) {

	//Check if the item is a link
  	if (preg_match("/^<a href=\"itemref:\/\/([0-9]+)\/([0-9]+)\/([0-9]+)\">(.+)<\/a>(.*)$/i", $arr[1], $item)) {
	    $item_ql = $item[3];
	    $item_highid = $item[1];
	    $item_lowid = $item[2];
	    $item_name = $item[4];
	} else if (preg_match("/^(.+)<a href=\"itemref:\/\/([0-9]+)\/([0-9]+)\/([0-9]+)\">(.+)<\/a>(.*)$/i", $arr[1], $item)){
	    $item_ql = $item[4];
	    $item_highid = $item[2];
	    $item_lowid = $item[3];
	    $item_name = $item[5];
	} else {
		$item_name = $arr[1];
	}
		
	//Check if the item is already on the list (i.e. SMART LOOT)
	forEach ($loot as $key => $item) {
		if (strtolower($item["name"]) == strtolower($item_name)) {
			if ($item["multiloot"]) {
				if ($multiloot) {
					$loot[$key]["multiloot"] = $item["multiloot"]+$multiloot;
				} else {
					$loot[$key]["multiloot"] = $item["multiloot"]+1;
				}
			} else {
				if ($multiloot) {
					$loot[$key]["multiloot"] = 1+$multiloot;
				} else {
					$loot[$key]["multiloot"] = 2;
				}
			}
			$dontadd = 1;
			$itmref = $key;
		}
	}

	//get a slot for the item
  	if (is_array($loot)) {
	  	$num_loot = count($loot);
	  	$num_loot++;
	} else {
		$num_loot = 1;
	}
	
	//Check if max slots is reached
  	if ($num_loot >= 30) {
	    $msg = "You can only roll 30 items max at one time!";
	    $chatBot->sendPrivate($msg);
	    return;
	}

	//Check if there is a icon available
	$row = $db->queryRow("SELECT * FROM aodb WHERE `name` LIKE ?", $item_name);
	if ($row !== null) {
	  	$item_name = $row->name;

		//Save the icon
		$looticon = $row->icon;
		//Save the aoid and ql if not set yet
		if (!isset($item_highid)) {
			$item_lowid = $row->lowid;
			$item_highid = $row->highid;
			$item_ql = $row->highql;	  
		}
	}

	//Save item
	if (!$dontadd) {
		if (isset($item_highid)) {
			$loot[$num_loot]["linky"] = "<a href='itemref://$item_lowid/$item_highid/$item_ql'>$item_name</a>";	
		}
			
		$loot[$num_loot]["name"] = $item_name;
		$loot[$num_loot]["icon"] = $looticon;

		//Save the person who has added the loot item
		$loot[$num_loot]["added_by"] = $sender;
	
		//Save multiloot
		$loot[$num_loot]["multiloot"] = $multiloot;

		//Send info
		if ($multiloot) {
			$chatBot->sendPrivate($multiloot."x <highlight>{$loot[$num_loot]["name"]}<end> will be rolled in Slot <highlight>#$num_loot<end>");
		} else {
			$chatBot->sendPrivate("<highlight>{$loot[$num_loot]["name"]}<end> will be rolled in Slot <highlight>#$num_loot<end>");
		}
		$chatBot->sendPrivate("To add use <symbol>add $num_loot, or <symbol>rem to remove yourself");
	} else {
		//Send info in case of SMART
		if ($multiloot) {
			$chatBot->sendPrivate($multiloot."x <highlight>{$loot[$itmref]["name"]}<end> added to Slot <highlight>#$itmref<end> as multiloot. Total: <yellow>{$loot[$itmref]["multiloot"]}<end>");
		} else {
			$chatBot->sendPrivate("<highlight>{$loot[$itmref]["name"]}<end> added to Slot <highlight>#$itmref<end> as multiloot. Total: <yellow>{$loot[$itmref]["multiloot"]}<end>");
		}
		$chatBot->sendPrivate("To add use <symbol>add $itmref, or <symbol>rem to remove yourself");
		$dontadd = 0;
		$itmref = 0;
		if (is_array($residual)) {
			$residual = "";
		}
	}
} else {
	$syntax_error = true;
}

?>