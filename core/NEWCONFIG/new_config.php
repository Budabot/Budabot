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

if (preg_match("/^newconfig$/i", $message)) {
	$list = "<header>::::: Module Config :::::<end>\n";
	
	$sql = "
		SELECT
			module
		FROM
			(SELECT module FROM settings_<myname> WHERE module <> 'Basic Settings'
				UNION
			SELECT module AS module FROM cmdcfg_<myname> WHERE module <> 'none'
				UNION
			SELECT module FROM hlpcfg_<myname>)
		GROUP BY module
		ORDER BY module ASC";

	$db->query($sql);
	$data = $db->fObject("all");
	forEach ($data as $row) {
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
	
		$on = "<a href='chatcmd:///tell <myname> config mod $row->module enable all'>On</a>";
		$off = "<a href='chatcmd:///tell <myname> config mod $row->module disable all'>Off</a>";
		$list .= strtoupper($row->module)." $a ($on/$off) $c $b\n";
	}

	$msg = bot::makeLink("Module Config", $list);
	bot::send($msg, $sender);  
} else if (preg_match("/^newconfig cmd (enable|disable) (all|guild|priv|msg)$/i", $message, $arr)) {
	$status = ($arr[1] == "enable" ? 1 : 0);
	$typeSql = ($arr[2] == "all" ? "`type` = 'guild' OR `type` = 'priv' OR `type` = 'msg'" : "`type` = '{$arr[2]}'");
	
	$sql = "UPDATE cmdcfg_<myname> SET `status` = $status WHERE `cmdevent` = 'cmd' OR `cmdevent` = 'subcmd' AND ($typeSql)";
	$db->update($sql);
	
	bot::send("Module(s) updated successfully.", $sendto);	
} else if (preg_match("/^newconfig (.*)$/i", $message, $arr)) {
	$module = $arr[1];
	$list  = "<header>::::: Bot Settings :::::<end>\n\n";
 	$list .= "<highlight>You can see here a list of all Settings that can be changed without a restart of the bot. Please note that not all can be changed only the ones that have a 'Change this' behind their name, on the rest you can see only the current setting but can´t change it. When you click on 'Change it' a new poopup cames up and you see a list of allowed options for this setting. \n\n<end>";
	$list .= "\n<highlight><u>" . strtoupper($module) . "</u><end>\n";

 	$db->query("SELECT * FROM settings_<myname> WHERE `mode` != 'hide' AND `module` = '$module'");
	if ($db->numrows() > 0) {
		$list .= "\n<i>Settings</i>\n";
	}
 	while ($row = $db->fObject()){
		$cur = $row->mod;	
		
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

	$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'cmd' AND `type` != 'setup' AND `module` = '$module' GROUP BY cmd");
	if ($db->numrows() > 0) {
		$list .= "\n<i>Commands</i>\n";
	}
	$data = $db->fObject("all");
	forEach ($data as $row) {
		$priv = "";
        $guild = "";
        $tell = "";

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

		if ($row->description != "none") {
			$list .= "$row->cmd ($row->description) - ($adv$tell$guild$priv): $on  $off\n";
		} else {
			$list .= "$row->cmd - ($adv$tell$guild$priv): $on  $off\n";
		}
	}
	
	$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'event' AND `type` != 'setup' AND `module` = '$module'");
	if ($db->numrows() > 0) {
		$list .= "\n<i>Events</i>\n";
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