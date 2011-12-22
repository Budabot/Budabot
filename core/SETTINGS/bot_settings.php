<?php

if (preg_match("/^settings$/i", $message)) {
  	$blob  = "<header> :::::: Bot Settings :::::: <end>\n\n";
 	$blob .= "<highlight>Changing any of these settings will take effect immediately. Please note that some of these settings are read-only and can't be changed.\n\n<end>";
 	$data = $db->query("SELECT * FROM settings_<myname> WHERE `mode` != 'hide' ORDER BY `module`");
	$cur = '';
 	forEach ($data as $row) {
		if ($row->module != $cur) {
			$blob .= "\n<pagebreak><highlight><u>".str_replace("_", " ", $row->module)."</u><end>\n";
			$cur = $row->module;
		}	
		$blob .= "  *" . $row->description;

		if ($row->mode == "edit") {
			$editLink = Text::make_chatcmd('Modify', "/tell <myname> settings change {$row->name}");
			$blob .= " ($editLink)";
		}

		$blob .= ": " . Setting::displayValue($row);
	}

  	$msg = Text::make_blob("Bot Settings", $blob);
 	$chatBot->send($msg, $sendto);
} else if (preg_match("/^settings change ([a-z0-9_]+)$/i", $message, $arr)) {
	$setting = strtolower($arr[1]);
 	$row = $db->queryRow("SELECT * FROM settings_<myname> WHERE `name` = ?", $setting);
	if ($row === null) {
		$msg = "Could not find setting <highlight>{$setting}<end>.";
	} else {
		if ($row->options != '') {
			$options = explode(";", $row->options);
		}
		if ($row->intoptions != '') {
			$intoptions = explode(";", $row->intoptions);
			$options_map = array_combine($intoptions, $options);
		}

		$blob = "<header> :::::: Settings Info for {$setting} :::::: <end>\n\n";
		$blob .= "Name: <highlight>{$row->name}<end>\n";
		$blob .= "Module: <highlight>{$row->module}<end>\n";
		$blob .= "Descrption: <highlight>{$row->description}<end>\n";
		$blob .= "Current Value: " . Setting::displayValue($row) . "\n";

		if ($row->type == 'color') {
		  	$blob .= "For this setting you can set any Color in the HTML Hexadecimal Color Format.\n";
		  	$blob .= "You can change it manually with the command: \n\n";
		  	$blob .= "/tell <myname> settings save {$row->name} #'HTML-Color'\n\n";
		  	$blob .= "Or you can choose one of the following Colors\n\n";
		  	$blob .= "Red: <font color='#ff0000'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #ff0000'>Save it</a>) \n";
		  	$blob .= "White: <font color='#FFFFFF'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #FFFFFF'>Save it</a>) \n";
			$blob .= "Grey: <font color='#808080'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #808080'>Save it</a>) \n";			
			$blob .= "Light Grey: <font color='#DDDDDD'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #DDDDDD'>Save it</a>) \n";
			$blob .= "Dark Grey: <font color='#9CC6E7'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #9CC6E7'>Save it</a>) \n";
		  	$blob .= "Black: <font color='#000000'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #000000'>Save it</a>) \n";
		  	$blob .= "Yellow: <font color='#FFFF00'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #FFFF00'>Save it</a>) \n";
		  	$blob .= "Blue: <font color='#8CB5FF'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #8CB5FF'>Save it</a>) \n";
		  	$blob .= "Deep Sky Blue: <font color='#00BFFF'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #00BFFF'>Save it</a>) \n";
		  	$blob .= "Green: <font color='#00DE42'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #00DE42'>Save it</a>) \n";			  			  			  		  	
		  	$blob .= "Orange: <font color='#FCA712'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #FCA712'>Save it</a>) \n";
		  	$blob .= "Gold: <font color='#FFD700'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #FFD700'>Save it</a>) \n";
		  	$blob .= "Deep Pink: <font color='#FF1493'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #FF1493'>Save it</a>) \n";
		  	$blob .= "Violet: <font color='#EE82EE'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #EE82EE'>Save it</a>) \n";
		  	$blob .= "Brown: <font color='#8B7355'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #8B7355'>Save it</a>) \n";
		  	$blob .= "Cyan: <font color='#00FFFF'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #00FFFF'>Save it</a>) \n";
		  	$blob .= "Navy Blue: <font color='#000080'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #000080'>Save it</a>) \n";
		  	$blob .= "Dark Orange: <font color='#FF8C00'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #FF8C00'>Save it</a>) \n";
		} else if ($row->type == 'text') {
	  		$blob .= "For this setting you can enter any text you want (max. 255 chararacters).\n";
		  	$blob .= "To change this setting:\n\n";
		  	$blob .= "<highlight>/tell <myname> settings save {$row->name} 'text'<end>\n\n";
		} else if ($row->type == 'number') {
			$blob .= "For this setting you can set any number.\n";
		  	$blob .= "To change this setting: \n\n";
		  	$blob .= "<highlight>/tell <myname> settings save {$row->name} 'number'<end>\n\n";
		} else if ($row->type == 'options') {
		  	$blob .= "For this setting you must choose one of the options from the list below.\n\n";
		} else if ($row->type == 'time') {
			$blob .= "For this setting you must enter a time value. See <a href='chatcmd:///tell <myname> help budatime'>budatime</a> for info on the format of the 'time' parameter.\n\n";
			$blob .= "To change this setting:\n\n";
			$blob .= "<highlight>/tell <myname> settings save {$row->name} 'time'<end>\n\n";
		}
		
		if ($options) {
			$blob .= "Predefined Options:\n";
			if ($intoptions) {
				forEach ($options_map as $key => $label) {
					$save_link = Text::make_chatcmd('Select', "/tell <myname> settings save {$row->name} {$key}");
					$blob .= "<tab> <highlight>{$label}<end> ({$save_link})\n";
				}
			} else {
				forEach ($options as $char) {
					$save_link = Text::make_chatcmd('Select', "/tell <myname> settings save {$row->name} {$char}");
					$blob .= "<tab> <highlight>{$char}<end> ({$save_link})\n";
				}
			}
		}

		// show help topic if there is one
		if ($row->help != '') {
			$help = Help::find($row->help, null);
			if ($help === false) {
				Logger::log('ERROR', 'Settings', "Help command <highlight>{$row->help}<end> for setting <highlight>{$setting}<end> could not be found.");
			}
		} else {
			$help = Help::find($setting, null);
		}

		if ($help !== false) {
			$blob .= "\n\n" . $help;
		}
		
		$msg = Text::make_blob("Settings Info for {$setting}", $blob);
	}

 	$chatBot->send($msg, $sendto);
} else if (preg_match("/^settings save ([a-z0-9_]+) (.+)$/i", $message, $arr)) {
  	$name_setting = strtolower($arr[1]);
  	$change_to_setting = $arr[2];
 	$row = $db->queryRow("SELECT * FROM settings_<myname> WHERE `name` = ?", $name_setting);
	if ($row === null) {
		$msg = "Could not find setting <highlight>{$name_setting}<end>.";
	} else {
		$options = explode(";", $row->options);
		$new_setting = "";
		if ($row->type == "color") {
			if (preg_match("/^#([0-9a-f]{6})$/i", $change_to_setting)) {
				$new_setting = "<font color='$change_to_setting'>";
			} else {
				$msg = "<highlight>{$change_to_setting}<end> isn't a valid HTML-Color (example: '#FF33DD').";
			}
		} else if ($row->type == "text") {
			if (strlen($change_to_setting) > 255) {
				$msg = "Your text can't be longer than 255 characters.";
			} else {
				$new_setting = $change_to_setting;
			}
		} else if ($row->type == "number") {
			if (preg_match("/^[0-9]+$/i", $change_to_setting)) {
				$new_setting = $change_to_setting;
			} else {
				$msg = "You must enter a number for this setting.";
			}
		} else if ($row->type == "options") {
			if ($row->intoptions != '') {
				$intoptions = explode(";", $row->intoptions);
				if (in_array($change_to_setting, $intoptions)) {
					$new_setting = $change_to_setting;
				} else {
					$msg = "This isn't a correct option for this setting.";
				}
			} else {
				if (in_array($change_to_setting, $options)) {
					$new_setting = $change_to_setting;
				} else {
					$msg = "This isn't a correct option for this setting.";
				}
			}
		} else if ($row->type == "time") {
			$time = Util::parseTime($change_to_setting);
			if ($time > 0) {
				$new_setting = $time;
			} else {
				$msg = "This isn't a valid time for this setting.";
			}
		}
	}
	if ($new_setting != "") {
		Setting::save($name_setting, $new_setting);
		$msg = "Setting successfull saved.";
	}
 	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}
?>