<?PHP
   /*
   Spirits Module Ver 1.1
   Written By Jaqueme
   For Budabot
   Database Adapted From One Originally
   Compiled by Wolfbiter For BeBot
   Spirits Database Module
   Written 5/11/07
   Last Modified 5/27/07
   */

	//If searched by Name or Slot
if (preg_match("/^spirits ([^0-9,]+)$/i", $message, $arr)) {
	$name = $arr[1];
	$name = ucwords(strtolower($name));
	$title = "Spirits Database for $name";
	$data = $db->query("SELECT * FROM spiritsdb WHERE name LIKE ? OR spot LIKE ? ORDER BY level", '%'.$name.'%', '%'.$name.'%');
	if (count($data) == 0) {
		$spirits .="<red>There were no matches found for $name.\nTry putting a comma between search values.\n\n";
		$spirits .= getValidSlotTypes();
	} else {
		$spirits .= formatSpiritOutput($data);
	}
}
	//If searched by name and slot
else if (preg_match("/^spirits ([^0-9]+),([^0-9]+)$/i", $message, $arr)) {
	if (preg_match("/(chest|ear|eye|feet|head|larm|legs|lhand|lwrist|rarm|rhand|rwrist|waist)/i", $arr[1])) {
		$slot = $arr[1];
		$name = $arr[2];
		$title = "Spirits Database for $name $slot";
	} else if (preg_match("/(chest|ear|eye|feet|head|larm|legs|lhand|lwrist|rarm|rhand|rwrist|waist)/i", $arr[2])) {
		$name = $arr[1];
		$slot = $arr[2];
		$title = "Spirits Database for $name $slot";
	} else {
		$title = "Spirits Database <red>Error<end>";
		$spirits .= "<red>No matches were found for $name $slot\n\n";
		$spirits .= getValidSlotTypes();
	}
	$name = ucwords(strtolower($name));
	$name = trim($name);
	$slot = ucwords(strtolower($slot));
	$slot = trim($slot);
	$data = $db->query("SELECT * FROM spiritsdb WHERE name LIKE ? AND spot = ? ORDER BY level", '%'.$name.'%', $slot);
	$spirits .= formatSpiritOutput($data);
}
	// If searched by ql
else if (preg_match("/^spirits ([0-9]+)$/i", $message, $arr)) {
	$ql = $arr[1];
    if ($ql <= 1 OR $ql >= 300) {
        $msg = "Invalid Ql specified(1-300)";
		$sendto->reply($msg);
        return;
    }
	$title = "Spirits QL $ql";
	$data = $db->query("SELECT * FROM spiritsdb where ql = ? ORDER BY ql", $ql);
	$spirits .= formatSpiritOutput($data);
}
	// If searched by ql range
else if (preg_match("/^spirits ([0-9]+)-([0-9]+)$/i", $message, $arr)) {
	$qllorange = $arr[1];
	$qlhirange = $arr[2];
	if ($qllorange < 1 OR $qlhirange > 219 OR $qllorange >= $qlhirange) {
		$msg = "Invalid Ql range specified(1-219)";
        $sendto->reply($msg);
        return;
	}
	$title = "Spirits QL $qllorange to $qlhirange";
	$data = $db->query("SELECT * FROM spiritsdb where ql >= ? AND ql <= ? ORDER BY ql", $qllorange, $qlhirange);
	$spirits .= formatSpiritOutput($data);
}
	// If searched by ql and slot
else if (preg_match("/^spirits ([0-9]+) (.+)$/i", $message, $arr)) {
	$ql = $arr[1];
	$slot = ucwords(strtolower($arr[2]));
	$title = "$slot Spirits QL $ql";
    if ($ql < 1 OR $ql > 300) {
        $msg = "Invalid Ql specified(1-300)";
        $sendto->reply($msg);
        return;
    } else if (preg_match("/[^chest|ear|eye|feet|head|larm|legs|lhand|lwrist|rarm|rhand|rwrist|waist]/i", $slot)) {
		$spirits .= "<red>Invalid Input\n\n";
		$spirits .= getValidSlotTypes();
	} else {
		$data = $db->query("SELECT * FROM spiritsdb where spot = ? AND ql = ? ORDER BY ql", $slot, $ql);
		$spirits .= formatSpiritOutput($data);
	}
}
	// If searched by ql range and slot
else if (preg_match("/^spirits ([0-9]+)-([0-9]+) (.+)$/i", $message, $arr)) {
	$qllorange = $arr[1];
	$qlhirange = $arr[2];
	$slot = ucwords(strtolower($arr[3]));
	$title = "$slot Spirits QL $qllorange to $qlhirange";
	if ($qllorange < 1 OR $qlhirange > 300 OR $qllorange >= $qlhirange) {
		$msg = "Invalid Ql range specified(1-300)";
		$sendto->reply($msg);
        return;
    } else if (preg_match("/[^chest|ear|eye|feet|head|larm|legs|lhand|lwrist|rarm|rhand|rwrist|waist]/i", $slot)) {
		$spirits .= "<red>Invalid Input\n\n";
		$spirits .= getValidSlotTypes();
	} else {
		$data = $db->query("SELECT * FROM spiritsdb where spot = ? AND ql >= ? AND ql <= ? ORDER BY ql", $slot, $qllorange, $qlhirange);
		$spirits .= formatSpiritOutput($data);
	}
}
	// If searched by minimum level
else if (preg_match ("/^spiritslvl ([0-9]+)$/i", $message, $arr)) {
	$lvl = $arr[1];
    if ($lvl < 1 OR $lvl > 219) {
        $msg = "Invalid Level specified(1-219)";
        $sendto->reply($msg);
        return;
    }
	$title = "Spirits Level $lvl";
	$lolvl = $lvl-10;
	$data = $db->query("SELECT * FROM spiritsdb where level < ? AND level > ? ORDER BY level", $lvl, $lolvl);
	$spirits .= formatSpiritOutput($data);
}
	// If searched by minimum level range
else if (preg_match("/^spiritslvl ([0-9]+)-([0-9]+)$/i", $message, $arr)) {
	$lvllorange = $arr[1];
	$lvlhirange = $arr[2];
	if ($lvllorange < 1 OR $lvlhirange > 219 OR $lvllorange >= $lvlhirange) {
		$msg = "Invalid Level range specified(1-219)";
        $sendto->reply($msg);
        return;
	}
	$title = "Spirits Level $lvllorange to $lvlhirange";
	$data = $db->query("SELECT * FROM spiritsdb where level >= ? AND level <= ? ORDER BY level", $lvllorange, $lvlhirange);
	$spirits .= formatSpiritOutput($data);
}
	// If searched by minimum level and slot
else if (preg_match ("/^spiritslvl ([0-9]+) (.+)$/i", $message, $arr)) {
	$lvl = $arr[1];
	$slot = ucwords(strtolower($arr[2]));
	$title = "$slot Spirits Level $lvl";
    if ($lvl < 1 OR $lvl > 219) {
        $msg = "Invalid Level specified(1-219)";
        $sendto->reply($msg);
        return;
    } else if (preg_match("/[^chest|ear|eye|feet|head|larm|legs|lhand|lwrist|rarm|rhand|rwrist|waist]/i", $slot)) {
		$spirits .= "<red>Invalid Input\n\n";
		$spirits .= getValidSlotTypes();
	} else {
		$lolvl = $lvl-10;
		$data = $db->query("SELECT * FROM spiritsdb where spot = ? AND level < ? AND level > ? ORDER BY level", $slot, $lvl, $lolvl);
		$spirits .= formatSpiritOutput($data);
	}
}
	// If searched by minimum level range and slot
else if (preg_match("/^spiritslvl ([0-9]+)-([0-9]+) (.+)$/i", $message, $arr)) {
	$lvllorange = $arr[1];
	$lvlhirange = $arr[2];
	$slot = ucwords(strtolower($arr[3]));
	$title = "$slot Spirits Level $lvllorange to $lvlhirange";
	if ($lvllorange < 1 OR $lvlhirange > 219 OR $lvllorange >= $lvlhirange) {
		$msg = "Invalid Level range specified(1-219)";
        $sendto->reply($msg);
        return;
    } else if (preg_match("/[^chest|ear|eye|feet|head|larm|legs|lhand|lwrist|rarm|rhand|rwrist|waist]/i", $slot)) {
		$spirits .= "<red>Invalid Input\n\n";
		$spirits .= getValidSlotTypes();
	} else {
		$data = $db->query("SELECT * FROM spiritsdb where spot = ? AND level >= ? AND level <= ? ORDER BY level", $slot, $lvllorange, $lvlhirange);
		$spirits .= formatSpiritOutput($data);
	}
}
	//Search by Agility
else if (preg_match ("/^spiritsagi ([0-9]+)$/i", $message, $arr)) {
	$agility = $arr[1];
	if ($agility < 1) {
        $msg = "Invalid Agility specified(1-1276)";
		$sendto->reply($msg);
        return;
    }
	$loagility = $agility - 10;
	$title = "Spirits Database for Agility Requirement of $agility";
	$data = $db->query("SELECT * FROM spiritsdb WHERE agility < ? AND agility > ? ORDER BY level", $agility, $loagility);
	$spirits .= formatSpiritOutput($data);
}
	// If searched by Agility and slot
else if (preg_match ("/^spiritsagi ([0-9]+) (.+)$/i", $message, $arr)) {
	$agility = $arr[1];
	$loagility = $agility - 10;
	$slot = ucwords(strtolower($arr[2]));
	$title = "$slot Spirits With Agility Req of $agility";
    if ($agility < 1) {
        $msg = "Invalid Agility specified(1-1276)";
		$sendto->reply($msg);
        return;
    }
	else if (preg_match("/[^chest|ear|eye|feet|head|larm|legs|lhand|lwrist|rarm|rhand|rwrist|waist]/i", $slot)) {
		$spirits .= "<red>Invalid Input\n\n";
		$spirits .= getValidSlotTypes();
	} else {
		$data = $db->query("SELECT * FROM spiritsdb where spot = ? AND agility < ? AND agility > ? ORDER BY ql", $slot, $agility, $loagility);
		$spirits .= formatSpiritOutput($data);
	}
}
	//Search By Sense
else if (preg_match ("/^spiritssen ([0-9]+)$/i", $message, $arr)) {
	$sense = $arr[1];
	if ($sense < 1) {
        $msg = "Invalid Sense specified(1-1276)";
        $sendto->reply($msg);
        return;
    }
	$losense = $sense - 10;
	$title = "Spirits Database for Sense Requirement of $sense";
	$data = $db->query("SELECT * FROM spiritsdb WHERE sense < ? AND sense > ? ORDER BY level", $sense, $losense);
	$spirits .= formatSpiritOutput($data);
}
	// If searched by Sensel and slot
else if (preg_match ("/^spiritssen ([0-9]+) (.+)$/i", $message, $arr)) {
	$sense = $arr[1];
	$losense = $sense - 10;
	$slot = ucwords(strtolower($arr[2]));
	$title = "$slot Spirits With Sense Req of $sense";
    if ($sense < 1) {
        $msg = "Invalid Sense specified(1-1276)";
        $sendto->reply($msg);
        return;
    } else if (preg_match("/[^chest|ear|eye|feet|head|larm|legs|lhand|lwrist|rarm|rhand|rwrist|waist]/i", $slot)) {
		$spirits .= "<red>Invalid Input\n\n";
		$spirits .= getValidSlotTypes();
	} else {
		$data = $db->query("SELECT * FROM spiritsdb where spot = ? AND sense < ? AND sense > ? ORDER BY ql", $slot, $sense, $losense);
		$spirits .= formatSpiritOutput($data);
	}
} else {
	$syntax_error = true;
	return;
}
		
$spirits = Text::make_blob("Spirits", $spirits, $title);
		
$sendto->reply($spirits);
?>