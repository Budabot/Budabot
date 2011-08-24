<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows and changes Settings
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 05.02.2006
   ** Date(last modified): 24.11.2006
   ** 
   ** Copyright (C) 2006 Carsten Lohmann
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

if (preg_match("/^settings$/i", $message)) {
  	$link  = "<header> :::::: Bot Settings :::::: <end>\n\n";
 	$link .= "<highlight>Changing any of these settings will take effect immediately. Please note that some of these settings are read-only and can't be changed.\n\n<end>";
 	$db->query("SELECT * FROM settings_<myname> WHERE `mode` != 'hide' ORDER BY `module`");
	$data = $db->fObject("all");
 	forEach ($data as $row) {
		if ($row->module == "Basic Settings" && $row->module != $cur) {
			$link .= "\n<highlight><u>Basic Settings</u><end>\n";
		} else if ($row->module != "" && $row->module != $cur) {
			$link .= "\n<highlight><u>".str_replace("_", " ", $row->module)."</u><end>\n";
		}	

		$cur = $row->module;	
		$link .= "  *";

		$link .= $row->description;

		if ($row->mode == "edit") {
			$editLink = Text::make_chatcmd('Modify', "/tell <myname> settings change {$row->name}");
			$link .= " ($editLink)";
		}
	
		$link .= ": ";

		$options = explode(";", $row->options);
		if ($row->type == "color") {
			$link .= $row->value."Current Color</font>\n";
		} elseif ($row->intoptions != "") {
			$intoptions = explode(";", $row->intoptions);
			$intoptions2 = array_flip($intoptions);
			$key = $intoptions2[$row->value];
			$link .= "<highlight>{$options[$key]}<end>\n";
		} else {
			$link .= "<highlight>{$row->value}<end>\n";
		}
	}

  	$msg = Text::make_blob("Bot Settings", $link);
 	$chatBot->send($msg, $sendto);
} else if (preg_match("/^settings change ([a-z0-9_]+)$/i", $message, $arr)) {
	$setting = strtolower($arr[1]);
    $link = "<header> :::::: Settings for {$setting} :::::: <end>\n\n";
 	$db->query("SELECT * FROM settings_<myname> WHERE `name` = '{$setting}'");
	if ($db->numrows() == 0) {
		$msg = "Could not find setting <highlight>{$setting}<end>.";
	} else {
		$row = $db->fObject();
		
		if ($row->options != '') {
			$options = explode(";", $row->options);
		}
		if ($row->intoptions != '') {
			$intoptions = explode(";", $row->intoptions);
			$options_map = array_combine($intoptions, $options);
		}
		
		$link .= "Name: <highlight>{$row->name}<end>\n";
		$link .= "Module: <highlight>{$row->module}<end>\n";
		$link .= "Descrption: <highlight>{$row->description}<end>\n";
		if ($intoptions) {
			$link .= "Current Value: <highlight>{$options_map[$row->value]}<end>\n\n";
		} else {
			$link .= "Current Value: <highlight>{$row->value}<end>\n\n";
		}
		if ($row->type == "color") {
		  	$link .= "For this setting you can set any Color in the HTML Hexadecimal Color Format.\n";
		  	$link .= "You can change it manually with the command: \n\n";
		  	$link .= "/tell <myname> settings save {$row->name} 'HTML-Color'\n\m";
		  	$link .= "(Allowed chars for the HTML-Color is 0-9 and A-F, max 4.chars)\n";
		  	$link .= "Or you can use also one of the following Pregiven Colors\n\n";
		  	$link .= "Red: <font color='#ff0000'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #ff0000'>Save it</a>) \n";
		  	$link .= "White: <font color='#FFFFFF'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #FFFFFF'>Save it</a>) \n";
			$link .= "Grey: <font color='#808080'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #808080'>Save it</a>) \n";			
			$link .= "Light Grey: <font color='#DDDDDD'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #DDDDDD'>Save it</a>) \n";
			$link .= "Dark Grey: <font color='#9CC6E7'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #9CC6E7'>Save it</a>) \n";
		  	$link .= "Black: <font color='#000000'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #000000'>Save it</a>) \n";
		  	$link .= "Yellow: <font color='#FFFF00'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #FFFF00'>Save it</a>) \n";
		  	$link .= "Blue: <font color='#8CB5FF'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #8CB5FF'>Save it</a>) \n";
		  	$link .= "Deep Sky Blue: <font color='#00BFFF'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #00BFFF'>Save it</a>) \n";
		  	$link .= "Green: <font color='#00DE42'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #00DE42'>Save it</a>) \n";			  			  			  		  	
		  	$link .= "Orange: <font color='#FCA712'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #FCA712'>Save it</a>) \n";
		  	$link .= "Gold: <font color='#FFD700'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #FFD700'>Save it</a>) \n";
		  	$link .= "Deep Pink: <font color='#FF1493'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #FF1493'>Save it</a>) \n";
		  	$link .= "Violet: <font color='#EE82EE'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #EE82EE'>Save it</a>) \n";
		  	$link .= "Brown: <font color='#8B7355'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #8B7355'>Save it</a>) \n";
		  	$link .= "Cyan: <font color='#00FFFF'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #00FFFF'>Save it</a>) \n";
		  	$link .= "Navy Blue: <font color='#000080'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #000080'>Save it</a>) \n";
		  	$link .= "Dark Orange: <font color='#FF8C00'>Example Text</font> (<a href='chatcmd:///tell <myname> settings save {$row->name} #FF8C00'>Save it</a>) \n";
		} else if ($row->type == "text") {
	  		$link .= "For this setting you can enter any text you want (max. 255 chararacters).\n";
		  	$link .= "To change this setting do:\n\n";
		  	$link .= "<highlight>/tell <myname> settings save {$row->name} 'Your text'<end>\n\n";
		} else if ($row->type == "number") {
			$link .= "For this setting you can set any number.\n";
		  	$link .= "You can change it manually with the command: \n\n";
		  	$link .= "<highlight>/tell <myname> settings save {$row->name} 'Number'<end>\n\n";
		} else if ($row->type == "options") {
		  	$link .= "For this setting you must choose one of the options from the list below.\n\n";
		}
		
		if ($options) {
			$link .= "Predefined Options:\n";
			if ($intoptions) {
				forEach ($options_map as $key => $label) {
					$save_link = Text::make_chatcmd('Select', "/tell <myname> settings save {$row->name} {$key}");
					$link .= "<tab> <highlight>{$label}<end> ({$save_link})\n";
				}
			} else {
				forEach ($options as $char) {
					$save_link = Text::make_chatcmd('Select', "/tell <myname> settings save {$row->name} {$char}");
					$link .= "<tab> <highlight>{$char}<end> ({$save_link})\n";
				}
			}
		}

		// show help topic if there is one
		if ($row->help != '') {
			$help = Help::find($row->help, null, false);
			if ($help === false) {
				Logger::log('ERROR', 'Settings', "Help command <highlight>{$row->help}<end> for setting <highlight>{$setting}<end> could not be found.");
			}
		} else {
			$help = Help::find($setting, null, false);
		}

		if ($help !== false) {
			$link .= "\n\n" . $help;
		}
		
		$msg = Text::make_blob("Settings Info for {$setting}", $link);
	}

 	$chatBot->send($msg, $sendto);
} else if (preg_match("/^settings save ([a-z0-9_]+) (.+)$/i", $message, $arr)) {
  	$name_setting = strtolower($arr[1]);
  	$change_to_setting = $arr[2];
 	$db->query("SELECT * FROM settings_<myname> WHERE `name` = '$name_setting'");
	if ($db->numrows() == 0) {
		$msg = "Could not find setting <highlight>{$name_setting}<end>.";
	} else {
		$row = $db->fObject();
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
		}
	}
	if ($new_setting != "") {
		$db->exec("UPDATE settings_<myname> SET `value` = '".str_replace("'", "''", $new_setting)."' WHERE `name` = '$name_setting'");	  	
		$chatBot->settings[$name_setting] = $new_setting;
		$msg = "Setting successfull saved.";
		
		//If the source is the config file renew it
		if ($row->source == "cfg") {
			global $config_file;
			$lines = file($config_file);
			forEach ($lines as $key => $line) {
			  	if (preg_match("/^(.+)vars\[('|\")(.+)('|\")](.*)=(.*)\"(.*)\";(.*)$/i", $line, $arr) && ($arr[3] == $name_setting)) {
  					$lines[$key] = "$arr[1]vars['$arr[3]']$arr[5]=$arr[6]\"{$chatBot->vars[$arr[3]]}\"; $arr[8]";
				} else if (preg_match("/^(.+)vars\[('|\")(.+)('|\")](.*)=(.*)([0-9]+)(.*);(.*)$/i", $line, $arr) && ($arr[3] == $name_setting)) {
  					$lines[$key] = "$arr[1]vars['$arr[3]']$arr[5]=$arr[6]{$chatBot->vars[$arr[3]]}; $arr[9]";
			  	} else if (preg_match("/^(.+)settings\[('|\")(.+)('|\")](.*)=(.*)\"(.*)\";(.*)$/i", $line, $arr)  && ($arr[3] == $name_setting)) {
					$lines[$key] = "$arr[1]settings['$arr[3]']$arr[5]=$arr[6]\"{$chatBot->settings[$arr[3]]}\"; $arr[8]";
				} else if (preg_match("/^(.+)settings\[('|\")(.+)('|\")](.*)=([ 	]+)([0-9]+);(.*)$/i", $line, $arr)  && ($arr[3] == $name_setting)) {
					$lines[$key] = "$arr[1]settings['$arr[3]']$arr[5]=$arr[6]{$chatBot->settings[$arr[3]]}; $arr[8]";
				}
			}
			file_put_contents($config_file, $lines);
		}
	}
 	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}
?>