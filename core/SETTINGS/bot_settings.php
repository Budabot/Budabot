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
  	$link  = "<header>::::: Bot Settings :::::<end>\n\n";
 	$link .= "<highlight>These changing any of these settings will take effect immediately. Please note that some of these settings are read-only and can't be changed.\n\n<end>";
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

		if ($row->help != "") {
			$helpLink = bot::makeLink('Help', "/tell <myname> settings help {$row->name}", 'chatcmd');
			$link .= "$row->description ($helpLink)";
		} else {
			$link .= $row->description;
		}

		if ($row->mode == "edit") {
			$editLink = bot::makeLink('Modify', "/tell <myname> settings change {$row->name}", 'chatcmd');
			$link .= " ($editLink)";
		}
	
		$link .= ":  ";

		$options = explode(";", $row->options);
		if ($options[0] == "color") {
			$link .= $row->setting."Current Color</font>\n";
		} elseif ($row->intoptions != "0") {
			$intoptions = explode(";", $row->intoptions);
			$intoptions2 = array_flip($intoptions);
			$key = $intoptions2[$row->setting];
			$link .= "<highlight>{$options[$key]}<end>\n";
		} else {
			$link .= "<highlight>{$row->setting}<end>\n";
		}
	}

  	$msg = bot::makeLink("Bot Settings", $link);
 	bot::send($msg, $sendto);
} else if (preg_match("/^settings change ([a-z0-9_]+)$/i", $message, $arr)) {
    $link = "<header>::::: Settings for {$arr[1]} :::::<end>\n\n";
 	$db->query("SELECT * FROM settings_<myname> WHERE `name` = '{$arr[1]}'");
	if ($db->numrows() == 0) {
		$msg = "This setting doesn't exists.";
	} else {
		$row = $db->fObject();
		$options = explode(";", $row->options);
		if ($options[0] == "color") {
		  	$link .= "For this setting you can set any Color in the HTML Hexadecimal Color Format.\n";
		  	$link .= "You can change it manually with the command: \n";
		  	$link .= "/tell <myname> settings save {$row->name} 'HTML-Color'\n";
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
		} else if ($options[0] == "text") {
		  	if($options[1] <= 50 && $options[1] != "")
		  		$link .= "For this setting you can enter any text you want(max. {$options[1]} chararacters).\n";
		  	else
		  		$link .= "For this setting you can enter any text you want(max. 50 chararacters).\n";
		  	$link .= "To change this setting do:\n";
		  	$link .= "/tell <myname> settings save {$row->name} 'Your text'";
		} else if($options[0] == "number") {
		  	if($options[1] != "") {
			  	$num = explode("-", $options[1]);
			  	$link .= "For this setting you can set any number from <highlight>$num[0]<end> to <highlight>$num[1]<end>.\n";
			} else
				$link .= "For this setting you can set any number.\n";
		  	$link .= "You can change it manually with the command: \n";
		  	$link .= "/tell <myname> settings save {$row->name} 'Number'\n";
			if ($options[1] != "") {
			  	$link .= "Or you can use also simply click on one of the following Numbers\n";
			  	for ($i = $num[0]; $i <= $num[1]; $i++) {
					$save_link = bot::makeLink('Save it', "/tell <myname> settings save {$row->name} {$i}", 'chatcmd');
					$link .= "<tab>- <highlight>{$i}<end> ({$save_link})\n";
				}
			}
		} else {
		  	$link .= "For this setting you can set only a range of allowed chars.\n";
		  	$link .= "You can change it manually with the command: \n";
		  	$link .= "/tell <myname> settings save {$row->name} 'Option'\n";
		  	$link .= "Or you can use also simply click on one of the following Options\n";
			if ($row->intoptions != '0') {
				$intoptions = explode(";", $row->intoptions);
				$options_map = array_combine($intoptions, $options);
				forEach ($options_map as $key => $label) {
					$save_link = bot::makeLink('Save it', "/tell <myname> settings save {$row->name} {$key}", 'chatcmd');
					$link .= "<tab> <highlight>{$label}<end> ({$save_link})\n";
				}
			} else {
				forEach ($options as $char) {
					$save_link = bot::makeLink('Save it', "/tell <myname> settings save {$row->name} {$char}", 'chatcmd');
					$link .= "<tab> <highlight>{$char}<end> ({$save_link})\n";
				}
			}
		}

		if ($row->help != '') {
			$link .= "\n\n" . file_get_contents('./core/' . $row->help) . file_get_contents('./modules/' . $row->help);
		}
	}

  	$msg = bot::makeLink("Settings Info for $arr[1]", $link);
 	bot::send($msg, $sendto);
} else if (preg_match("/^settings save ([a-z0-9_]+) (.+)$/i", $message, $arr)) {
  	$name_setting = strtolower($arr[1]);
  	$change_to_setting = $arr[2];
 	$db->query("SELECT * FROM settings_<myname> WHERE `name` = '$name_setting'");
	if ($db->numrows() == 0) {
		$msg = "This setting doesn't exist.";
	} else {
		$row = $db->fObject();
		$options = explode(";", $row->options);
		$new_setting = "";
		if ($options[0] == "color") {
			if (preg_match("/^#([0-9a-f]{6})$/i", $change_to_setting, $col)) {
				$new_setting = "<font color='$col[0]'>";
			} else {
				$msg = "<highlight>{$change_to_setting}<end> isn't a valid HTML-Color.";
			}
		} else if ($options[0] == "text") {
		  	if ($options[1] <= 50 && $options[1] != "") {
			 	if (strlen($change_to_setting) > $options[1]) {
				   	$msg = "Your text can't be longer than {$options[1]} characters.";
				} else {
					$new_setting = $change_to_setting;
				}
			} else {
			 	if (strlen($change_to_setting) > 50) {
				   	$msg = "Your text can't be longer than 50 characters.";
				} else {
					$new_setting = $change_to_setting;
				}
			}
		} else if ($options[0] == "number") {
		  	if ($options[1] != "") {
			  	$num = explode("-", $options[1]);
				if ($change_to_setting >= $num[0] && $change_to_setting <= $num[1]) {
					$new_setting = $change_to_setting;
				} else {
					$msg = "Only numbers between <highlight>{$num[0]}<end> and <highlight>{$num[1]}<end> are allowed.";
				}
			} else {
				$new_setting = $change_to_setting;
			}
		} else if ($row->intoptions != "0" && $row->intoptions != '') {
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
	if ($new_setting != "") {
		$db->exec("UPDATE settings_<myname> SET `setting` = '".str_replace("'", "''", $new_setting)."' WHERE `name` = '$name_setting'");	  	
		$this->settings[$name_setting] = $new_setting;
		$msg = "Setting successfull saved.";
		//If the source is the config file renew it
		if ($row->source == "cfg") {
			$lines = file("config.php");
			forEach ($lines as $key => $line) {
			  	if (preg_match("/^(.+)vars\[('|\")(.+)('|\")](.*)=(.*)\"(.*)\";(.*)$/i", $line, $arr) && ($arr[3] == $name_setting)) {
  					$lines[$key] = "$arr[1]vars['$arr[3]']$arr[5]=$arr[6]\"{$this->vars[$arr[3]]}\"; $arr[8]";
				} else if (preg_match("/^(.+)vars\[('|\")(.+)('|\")](.*)=(.*)([0-9]+)(.*);(.*)$/i", $line, $arr) && ($arr[3] == $name_setting)) {
  					$lines[$key] = "$arr[1]vars['$arr[3]']$arr[5]=$arr[6]{$this->vars[$arr[3]]}; $arr[9]";
			  	} else if (preg_match("/^(.+)settings\[('|\")(.+)('|\")](.*)=(.*)\"(.*)\";(.*)$/i", $line, $arr)  && ($arr[3] == $name_setting)) {
					$lines[$key] = "$arr[1]settings['$arr[3]']$arr[5]=$arr[6]\"{$this->settings[$arr[3]]}\"; $arr[8]";
				} else if (preg_match("/^(.+)settings\[('|\")(.+)('|\")](.*)=([ 	]+)([0-9]+);(.*)$/i", $line, $arr)  && ($arr[3] == $name_setting)) {
					$lines[$key] = "$arr[1]settings['$arr[3]']$arr[5]=$arr[6]{$this->settings[$arr[3]]}; $arr[8]";
				}
			}
			file_put_contents("config.php", $lines);
		}
	}
 	bot::send($msg, $sendto);
} else if (preg_match("/^settings help (.+)$/i", $message, $arr)) {
  	$name = $arr[1];
 	$db->query("SELECT * FROM settings_<myname> WHERE `name` = '{$name}'");  
	if ($db->numrows() != 0) {
	  	$row = $db->fObject();
		if ($row->help == '') {
			$msg = "No help found for this setting.";
		} else {
			$data = file_get_contents('./core/' . $row->help) . file_get_contents('./modules/' . $row->help);
			if ($data === false) {
				$msg = "Help file specified but doesn't exist for this setting.";
			} else {
				$msg = bot::makeLink("Help on setting {$name}", $data);
			}
		}
	} else {
		$msg = "This setting doesn't exist.";
	}

 	bot::send($msg, $sendto);
} else {
	$syntax_error = true;
}
?>