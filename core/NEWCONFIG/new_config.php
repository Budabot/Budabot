<?
   /*
   ** Author: Tyrence (RK2)
   ** Description: New format for configuring the bot
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 26-MAR-2010
   ** 
   ** Copyright (C) 2010 Jason Wheeler
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

if (preg_match("/^newconfig$/", $message)) {
	$list = "<header>::::: Command and Event Config :::::<end>\n";
	$list .= "<highlight>Here can you disable or enable Modules and also changing their Access Level\n";
	$list .= "The following options are available:\n";
	$list .= " - Click ON or Off behind the Module Name to Enable or Disable them completly.\n";
	$list .= " - Click Adv. behind the name to see more options for the modules<end> \n\n";
	
	$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'cmd' AND `type` != 'setup' AND `dependson` = 'none' UNION ALL SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'event' AND `type` != 'setup' AND `dependson` = 'none' ORDER BY `module`, `cmd`");
	$data = $db->fObject("all");
	foreach($data as $row) {
  	  	if($oldmodule != $row->module) {
   	  	  	$db->query("SELECT * FROM hlpcfg_<myname> WHERE `module` = '".strtoupper($row->module)."'");
  	  	  	$num = $db->numrows();
  	  	  	if($num > 0)
				$b = "(<a href='chatcmd:///tell <myname> config help $row->module'>Helpfiles</a>)";
			else
				$b = "";
				
			$db->query("SELECT * FROM cmdcfg_<myname> WHERE `module` = '$row->module' AND `status` = 1");
			$num = $db->numrows();
			if($num > 0)
				$a = "(<green>Running<end>)";
			else
				$a = "(<red>Disabled<end>)";
				
			$c = "(<a href='chatcmd:///tell <myname> newconfig $row->module'>Configure</a>)";
		
  			$on = "<a href='chatcmd:///tell <myname> config mod ".$row->module." enable all'>On</a>";
			$off = "<a href='chatcmd:///tell <myname> config mod ".$row->module." disable all'>Off</a>";
            $list .= "<u>".strtoupper($row->module)."</u> $a ($on/$off) $c $b\n";
            $oldmodule = $row->module;
        }
	}

	$msg = bot::makeLink("Bot Configuration", $list);
	bot::send($msg, $sender);  
} else if (preg_match("/^newconfig (.*)$/", $message, $arr)) {
	$module = strtolower($arr[1]);
	$list  = "<header>::::: Bot Settings :::::<end>\n\n";
 	$list .= "<highlight>You can see here a list of all Settings that can be changed without a restart of the bot. Please note that not all can be changed only the ones that have a 'Change this' behind their name, on the rest you can see only the current setting but can´t change it. When you click on 'Change it' a new poopup cames up and you see a list of allowed options for this setting. \n\n<end>";
	$list .= "\n<highlight><u>" . strtoupper($module) . "</u><end>\n";

 	$db->query("SELECT * FROM settings_<myname> WHERE `mode` != 'hide' AND `mod` = '$module'");
	if ($db->numrows() > 0) {
		$list .= "\n<tab>Settings\n";
	}
 	while ($row = $db->fObject()){
		$cur = $row->mod;	
		$list .= "  *";
		
		if($row->help != "")
			$list .= "$row->description (<a href='chatcmd:///tell <myname> settings help $row->name'>Help</a>)";
		else
			$list .= $row->description;

		if($row->mode == "edit")
			$list .= " (<a href='chatcmd:///tell <myname> settings change $row->name'>Change this</a>)";
	
		$list .= ":  ";

		$options = explode(";", $row->options);
		if($options[0] == "color")
			$list .= $row->setting."Current Color</font>\n";
		elseif($row->intoptions != "0") {
			$intoptions = explode(";", $row->intoptions);
			$intoptions2 = array_flip($intoptions);
			$key = $intoptions2[$row->setting];
			$list .= "<highlight>{$options[$key]}<end>\n";
		} else
			$list .= "<highlight>$row->setting<end>\n";	
	}

	$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'cmd' AND `type` != 'setup' AND `dependson` = 'none' AND `module` = '$module'");
	if ($db->numrows() > 0) {
		$list .= "\n<tab>Commands\n";
	}
	while ($row = $db->fObject()) {
		$priv = "";
        $guild = "";
        $tell = "";
		if($oldcmd != $row->cmd) {
			if($row->grp == "none") {
				$on = "<a href='chatcmd:///tell <myname> config cmd $row->cmd enable all'>ON</a>";
				$off = "<a href='chatcmd:///tell <myname> config cmd $row->cmd disable all'>OFF</a>";
				$adv = "<a href='chatcmd:///tell <myname> config cmd $row->cmd $row->module'>Adv.</a>";
		
				$db->query("SELECT * FROM cmdcfg_<myname> WHERE `module` = '$row->module' AND `cmd` = '$row->cmd'");
				while($row1 = $db->fObject()) {
					if($row1->type == "msg" && $row->status == 1)
						$tell = "|<green>T<end>";
					elseif($row1->type == "msg" && $row->status == 0)
						$tell = "|<red>T<end>";
					elseif($row1->type == "guild" && $row->status == 1)
						$guild = "|<green>G<end>";
					elseif($row1->type == "guild" && $row->status == 0)
						$guild = "|<red>G<end>";
					elseif($row1->type == "priv" && $row->status == 1)
						$priv = "|<green>P<end>";
					elseif($row1->type == "priv" && $row->status == 0)
						$priv = "|<red>P<end>";
				}

				if($row->description != "none")
					$list .= "  $row->cmd ($row->description) - ($adv$tell$guild$priv): $on  $off\n";
				else
					$list .= "  $row->cmd - ($adv$tell$guild$priv): $on  $off\n";
				$oldcmd = $row->cmd;
			} elseif($group[$row->grp] == false) {
				$on = "<a href='chatcmd:///tell <myname> config grp ".$row->grp." enable all'>ON</a>";
				$off = "<a href='chatcmd:///tell <myname> config grp ".$row->grp." disable all'>OFF</a>";
				$adv = "<a href='chatcmd:///tell <myname> config grp ".$row->grp."'>Adv.</a>";

				$db->query("SELECT * FROM cmdcfg_<myname> WHERE `module` = 'none' AND `cmdevent` = 'group' AND `type` = '$row->grp'");
				if($db->numrows() == 1) {
					$temp = $db->fObject();
					if($temp->description != "none")
						$list .= "  $temp->description ($adv): $on  $off \n";
					else
						$list .= "  $temp->grp group ($adv): $on  $off \n";
				}
				$group[$row->grp] = true;
			}
		}
	}
	
	$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'event' AND `type` != 'setup' AND `dependson` = 'none' AND `module` = '$module'");
	if ($db->numrows() > 0) {
		$list .= "\n<tab>Events\n";
	}
	while ($row = $db->fObject()) {
		$on = "<a href='chatcmd:///tell <myname> config event ".$row->type." ".$row->file." enable all'>ON</a>";
		$off = "<a href='chatcmd:///tell <myname> config event ".$row->type." ".$row->file." disable all'>OFF</a>";

		if($row->status == 1)
			$status = "<green>Enabled<end>";
		else
			$status = "<red>Disabled<end>";

		if($row->description != "none")
			$list .= "  $row->type ($row->description) - ($status): $on  $off \n";
		else
			$list .= "  $row->type - ($status): $on  $off \n";
	}

  	$msg = bot::makeLink("Bot Settings", $list);
 	bot::send($msg, $sender);
}

?>