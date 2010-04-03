<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Enable/Disable Command/events and sets Access Level for commands
   ** Version: 0.4
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 15.12.2005
   ** Date(last modified): 10.12.2006
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

if(eregi("^config$", $message)) {
	$msg = "Getting Dynamic command and eventlist. This can take a few seconds.";
  	bot::send($msg, $sender);

	$list = "<header>::::: Command and Event Config :::::<end>\n";
	$list .= "<highlight>Here can you disable or enable Commands/Events and also changing their Access Level\n";
	$list .= "The following options are available:\n";
	$list .= " - Click ON or Off behind the Module Name to Enable or Disable them completly.\n";
	$list .= " - Click ON or Off behind the Command/Eventname to Enable or Disable them.\n";
	$list .= " - Click Adv. behind the name to change their Status for the single Channels \n";
	$list .= "   and to change their Access Limit\n<end>";

	$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'cmd' AND `type` != 'setup' AND `dependson` = 'none' UNION ALL SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'event' AND `type` != 'setup' AND `dependson` = 'none' ORDER BY `module`, `cmd`");
	$data = $db->fObject("all");
	foreach($data as $row) {
  	  	if($oldmodule != $row->module) {
   	  	  	$db->query("SELECT * FROM hlpcfg_<myname> WHERE `module` = '".strtoupper($row->module)."'");
  	  	  	$num = $db->numrows();
  	  	  	if($num > 0)
				$b = "(<a href='chatcmd:///tell <myname> config help $row->module'>Configure Helpfiles</a>)";
			else
				$b = "";
				
			$db->query("SELECT * FROM cmdcfg_<myname> WHERE `module` = '$row->module' AND `status` = 1");
			$num = $db->numrows();
			if($num > 0)
				$a = "(<green>Running<end>)";
			else
				$a = "(<red>Disabled<end>)";
		
  			$on = "<a href='chatcmd:///tell <myname> config mod ".$row->module." enable all'>On</a>";
			$off = "<a href='chatcmd:///tell <myname> config mod ".$row->module." disable all'>Off</a>";
            $list .= "\n<u>".strtoupper($row->module)."</u>$a ($on/$off) $b\n";
            $oldmodule = $row->module;
        }
        
		if($row->cmdevent == "cmd" && $oldcmd != $row->cmd) {
			if($row->grp == "none") {
				$on = "<a href='chatcmd:///tell <myname> config cmd $row->cmd enable all'>ON</a>";
				$off = "<a href='chatcmd:///tell <myname> config cmd $row->cmd disable all'>OFF</a>";
				$adv = "<a href='chatcmd:///tell <myname> config cmd $row->cmd $row->module'>Adv.</a>";
		
				if($row->description != "none")
					$list .= "  $row->description ($adv): $on  $off \n";
				else
					$list .= "  $row->cmd Command ($adv): $on  $off \n";
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
		} elseif ($row->cmdevent == "event") {
				$on = "<a href='chatcmd:///tell <myname> config event ".$row->type." ".$row->file." enable all'>ON</a>";
				$off = "<a href='chatcmd:///tell <myname> config event ".$row->type." ".$row->file." disable all'>OFF</a>";

				if($row->status == 1)
					$status = "<green>Enabled<end>";
				else
					$status = "<red>Disabled<end>";
		
				if($row->description != "none")
					$list .= "  $row->description($status): $on  $off \n";
				else
					$list .= "  $row->type Event($status): $on  $off \n";			  	
		}
	}

	$msg = bot::makeLink("Bot Configuration", $list);
	bot::send($msg, $sender);  
} elseif(eregi("^config (mod|cmd|grp|event) (.+) (enable|disable) (priv|msg|guild|all)$", $message, $arr)) {
	if($arr[1] == "event") {
		$temp = explode(" ", $arr[2]);
	  	$cmdmod = $temp[0];
	  	$file = $temp[1];
	} else {
		$cmdmod = $arr[2];
		$type = $arr[4]; 	
	}
		
	if($arr[3] == "enable")
		$status = 1;
	else
		$status = 0;
	
	if($arr[1] == "mod" && $type == "all")
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `module` = '$cmdmod'");
	elseif($arr[1] == "mod" && $type != "all")
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `module` = '$cmdmod' AND `type` = '$type'");
	elseif($arr[1] == "cmd" && $type != "all")
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$cmdmod' AND `type` = '$type' AND `cmdevent` = 'cmd'");
	elseif($arr[1] == "cmd" && $type == "all")
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$cmdmod' AND `cmdevent` = 'cmd'");
	elseif($arr[1] == "grp" && $type != "all")
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `grp` = '$cmdmod' AND `type` = '$type' AND `cmdevent` = 'cmd'");
	elseif($arr[1] == "grp" && $type == "all")
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `grp` = '$cmdmod' AND `cmdevent` = 'cmd'");
	elseif($arr[1] == "event" && $file != "")
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `file` = '$file' AND `cmdevent` = 'event' AND `type` = '$cmdmod'");	
	else
		$msg = "Unknown Syntax for this command. Pls look into the help system for usage of this command.";

	if($db->numrows() == 0) {
		if($arr[1] == "mod" && $type == "all")
			$msg = "Could not find the Module <highlight>$cmdmod<end>";
		elseif($arr[1] == "mod" && $type != "all")
			$msg = "Could not find the Module <highlight>$cmdmod<end> for Channel <highlight>$type<end>";
		elseif($arr[1] == "cmd" && $type != "all")
			$msg = "Could not find the Command <highlight>$cmdmod<end> for Channel <highlight>$type<end>";
		elseif($arr[1] == "cmd" && $type == "all")
			$msg = "Could not find the Command <highlight>$cmdmod<end>";
		elseif($arr[1] == "grp" && $type != "all")
			$msg = "Could not find the Group <highlight>$cmdmod<end> for Channel <highlight>$type<end>";
		elseif($arr[1] == "grp" && $type == "all")
			$msg = "Could not find the Group <highlight>$cmdmod<end>";
		elseif($arr[1] == "event" && $file != "")
			$msg = "Could not find the Event <highlight>$cmdmod<end> for File <highlight>$file<end>";
		bot::send($msg, $sender);
		return;
	}

	if($arr[1] == "mod" && $type == "all") {
		$msg = "Updated status of the module <highlight>$cmdmod<end> to <highlight>".$arr[3]."d<end>";
	} elseif($arr[1] == "mod" && $type != "all") {
		$msg = "Updated status of the module <highlight>$cmdmod<end> in Channel <highlight>$type<end> to <highlight>".$arr[3]."d<end>"; 
	} elseif($arr[1] == "cmd" && $type != "all") {
		$msg = "Updated status of command <highlight>$cmdmod<end> to <highlight>".$arr[3]."d<end> in Channel <highlight>$type<end>";
	} elseif($arr[1] == "cmd" && $type == "all") {
		$msg = "Updated status of command <highlight>$cmdmod<end> to <highlight>".$arr[3]."d<end>";
	} elseif($arr[1] == "grp" && $type != "all") {
		$msg = "Updated status of group <highlight>$cmdmod<end> to <highlight>".$arr[3]."d<end> in Channel <highlight>$type<end>";
	} elseif($arr[1] == "grp" && $type == "all") {
		$msg = "Updated status of group <highlight>$cmdmod<end> to <highlight>".$arr[3]."d<end>";
	} elseif($arr[1] == "event" && $type != "") {
		$msg = "Updated status of event <highlight>$cmdmod<end> to <highlight>".$arr[3]."d<end>";
	}

	bot::send($msg, $sender);

	$data = $db->fObject("all");
	foreach($data as $row) {
	  	if($row->cmdevent != "event") {
		  	if($status == 1)
				bot::regcommand($row->type, $row->file, $row->cmd, $row->admin);
			else
				bot::unregcommand($row->type, $row->file, $row->cmd, $row->admin);
		} else {
		  	if($status == 1)
				bot::regevent($row->type, $row->file);
			else
				bot::unregevent($row->type, $row->file);
		}
	}

	if($arr[1] == "mod" && $type == "all") {
		$db->query("UPDATE cmdcfg_<myname> SET `status` = $status WHERE `module` = '$cmdmod' AND `cmdevent` = 'cmd'");
		$db->query("UPDATE cmdcfg_<myname> SET `status` = $status WHERE `module` = '$cmdmod' AND `cmdevent` = 'event'");
	} elseif($arr[1] == "mod" && $type != "all") {
		$db->query("UPDATE cmdcfg_<myname> SET `status` = $status WHERE `module` = '$cmdmod' AND `type` = '$type' AND `cmdevent` = 'cmd'");
		$db->query("UPDATE cmdcfg_<myname> SET `status` = $status WHERE `module` = '$cmdmod' AND `type` = '$type' AND `cmdevent` = 'event'");
	} elseif($arr[1] == "cmd" && $type != "all") {
		$db->query("UPDATE cmdcfg_<myname> SET `status` = $status WHERE `cmd` = '$cmdmod' AND `type` = '$type' AND `cmdevent` = 'cmd'");
	} elseif($arr[1] == "cmd" && $type == "all") {
		$db->query("UPDATE cmdcfg_<myname> SET `status` = $status WHERE `cmd` = '$cmdmod' AND `cmdevent` = 'cmd'");
	} elseif($arr[1] == "grp" && $type != "all") {
		$db->query("UPDATE cmdcfg_<myname> SET `status` = $status WHERE `grp` = '$cmdmod' AND `type` = '$type' AND `cmdevent` = 'cmd'");
	} elseif($arr[1] == "grp" && $type == "all") {
		$db->query("UPDATE cmdcfg_<myname> SET `status` = $status WHERE `grp` = '$cmdmod' AND `cmdevent` = 'cmd'");
	} elseif($arr[1] == "event" && $file != "") {
		$db->query("UPDATE cmdcfg_<myname> SET `status` = $status WHERE `type` = '$cmdmod' AND `cmdevent` = 'event' AND `file` = '$file'");
	}
} elseif(eregi("^config (subcmd|cmd|grp) ([a-z0-9_]+) admin (msg|priv|guild|all) (rl|mod|guildadmin|guild|leader|all)$", $message, $arr)) {
	$channel = strtolower($arr[1]);
	$command = strtolower($arr[2]);
	$type = strtolower($arr[3]);
	$admin = $arr[4];

	switch($admin) {
	  	case "leader":
	  		$admin = 1;
	  	break;
	  	case "rl":
	  		$admin = 2;
	  	break;
	  	case "mod":
	  		$admin = 3;
	  	break;
	}
	
	if($channel == "cmd") {
		if($type == "all")
			$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$command' AND `cmdevent` = 'cmd'");
		else
			$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$command' AND `type` = '$type' AND `cmdevent` = 'cmd'");
	
		if($db->numrows() == 0) {
			if($type == "all")
				$msg = "Could not find the command <highlight>$command<end>";
			else
				$msg = "Could not find the command <highlight>$command<end> for Channel <highlight>$type<end>";
		  	bot::send($msg, $sender);
		  	return;
		}
			
		switch($type) {
			case "all":
				if($this->tellCmds[$command])
					$this->tellCmds[$command]["admin level"] = $admin;
				if($this->privCmds[$command])
					$this->privCmds[$command]["admin level"] = $admin;
				if($this->guildCmds[$command])
					$this->guildCmds[$command]["admin level"] = $admin;
			break;
		  	case "msg":	
				$this->tellCmds[$command]["admin level"] = $admin;
		  	break;
		  	case "priv":
				$this->privCmds[$command]["admin level"] = $admin;
		  	break;
		  	case "guild":
				$this->guildCmds[$command]["admin level"] = $admin;
		  	break;
		}
		
		if($type == "all") {
			$db->query("UPDATE cmdcfg_<myname> SET `admin` = '$admin' WHERE `cmd` = '$command' AND `cmdevent` = 'cmd'");
			$msg = "Updated access of command <highlight>$command<end> to <highlight>$arr[4]<end>";
		} else {
			$db->query("UPDATE cmdcfg_<myname> SET `admin` = '$admin' WHERE `cmd` = '$command' AND `type` = '$type' AND `cmdevent` = 'cmd'");
			$msg = "Updated access of command <highlight>$command<end> in Channel <highlight>$type<end> to <highlight>$arr[4]<end>";
		}
	} elseif($channel == "grp") {
	  	if($type == "all")
			$db->query("SELECT * FROM cmdcfg_<myname> WHERE `grp` = '$command' AND `cmdevent` = 'cmd'");
		else
			$db->query("SELECT * FROM cmdcfg_<myname> WHERE `grp` = '$command' AND `type` = '$type' AND `cmdevent` = 'cmd'");
	
		if($db->numrows() == 0) {
			if($arr[3] == "all")
				$msg = "Could not find the group <highlight>$command<end>";
			else
				$msg = "Could not find the group <highlight>$command<end> for Channel <highlight>$type<end>";
		  	bot::send($msg, $sender);
		  	return;
		}
		while($row = $db->fObject()) {
			switch($arr[3]) {
				case "all":
					if($this->tellCmds[$row->cmd])
						$this->tellCmds[$row->cmd]["admin level"] = $admin;
					if($this->privCmds[$row->cmd])
						$this->privCmds[$row->cmd]["admin level"] = $admin;
					if($this->guildCmds[$row->cmd])
						$this->guildCmds[$row->cmd]["admin level"] = $admin;
				break;
			  	case "msg":	
					$this->tellCmds[$row->cmd]["admin level"] = $admin;
			  	break;
			  	case "priv":
					$this->privCmds[$row->cmd]["admin level"] = $admin;
			  	break;
			  	case "guild":
					$this->guildCmds[$row->cmd]["admin level"] = $admin;
			  	break;
			}
		}
		
		if($arr[3] == "all") {
			$db->query("UPDATE cmdcfg_<myname> SET `admin` = '$admin' WHERE `grp` = '$command' AND `cmdevent` = 'cmd'");
			$msg = "Updated access of group <highlight>$command<end> to <highlight>$arr[4]<end>";
		} else {
			$db->query("UPDATE cmdcfg_<myname> SET `admin` = '$admin' WHERE `grp` = '$command' AND `type` = '$type' AND `cmdevent` = 'cmd'");
			$msg = "Updated access of group <highlight>$command<end> in Channel <highlight>$type<end> to <highlight>$arr[4]<end>";
		}
	} else {
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `type` = '$type' AND `cmdevent` = 'subcmd' AND `cmd` = '$command'");
		if($db->numrows() == 0) {
			$msg = "Could not find the subcmd <highlight>$command<end> for Channel <highlight>$type<end>";
		  	bot::send($msg, $sender);
		  	return;
		}
		$row = $db->fObject();
		$this->subcommands[$row->file][$row->type]["admin"] = $admin;		
		$db->query("UPDATE cmdcfg_<myname> SET `admin` = '$admin' WHERE `type` = '$type' AND `cmdevent` = 'subcmd' AND `cmd` = '$command'");
		$msg = "Updated access of sub command <highlight>$command<end> in Channel <highlight>$type<end> to <highlight>$arr[4]<end>";
	}
	bot::send($msg, $sender);
} elseif(eregi("^config cmd ([a-z0-9_]+) (.+)$", $message, $arr)) {
	$cmd = strtolower($arr[1]);
	$module = strtolower($arr[2]);
	$found_msg = 0;
	$found_priv = 0;
	$found_guild = 0;	

	$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$cmd' AND `module` = '$module'");
	if($db->numrows() == 0)
		$msg = "Could not find the command <highligh>$cmd<end> in the module <highlight>$module<end>.";
	else {
		$list = "<header>::::: Configure command $cmd :::::<end>\n\n";
		$list .= "<u><highlight>Tells:<end></u>\n";	
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$cmd' AND `type` = 'msg' AND `module` = '$module'");
		if($db->numrows() == 1) {
			$row = $db->fObject();

			$found_msg = 1;
			
			if($row->admin == 1)
				$row->admin = "Leader";
			elseif($row->admin == 2)
				$row->admin = "Raidleader";
			elseif ($row->admin == 3)
				$row->admin = "Moderator";
			elseif ($row->admin == 4)
				$row->admin = "Administrator";
		
			if($row->status == 1)
				$status = "<green>Enabled<end>";
			else
				$status = "<red>Disabled<end>";
			
			$list .= "Current Status: $status (Access: $row->admin) \n";
			$list .= "Enable or Disable Command: ";
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." enable msg'>ON</a>  ";
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." disable msg'>OFF</a>\n";

			$list .= "Set minimum access lvl to use this command: ";
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin msg all'>All</a>  ";
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin msg leader'>Leader</a>  ";
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin msg rl'>RL</a>  ";
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin msg mod'>Mod</a>  ";
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin msg guildadmin'>Guildadmin</a>  ";
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin msg guild'>Guildmembers</a>\n";
		} else 
			$list .= "Current Status: <red>Unused<end>. \n";

		$list .= "\n\n<u><highlight>Private Channel:<end></u>\n";	
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$cmd' AND `type` = 'priv' AND `module` = '$module'");
		if($db->numrows() == 1) {
			$row = $db->fObject();

			$found_priv = 1;

			if($row->admin == 1)
				$row->admin = "Leader";
			elseif($row->admin == 2)
				$row->admin = "Raidleader";
			elseif ($row->admin == 3)
				$row->admin = "Moderator";
			elseif ($row->admin == 4)
				$row->admin = "Administrator";

			if($row->status == 1)
				$status = "<green>Enabled<end>";
			else
				$status = "<red>Disabled<end>";

			$list .= "Current Status: $status (Access: $row->admin) \n";
			$list .= "Enable or Disable Command: ";
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." enable priv'>ON</a>  ";
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." disable priv'>OFF</a>\n";

			$list .= "Set minimum access lvl to use this command: ";
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin priv all'>All</a>  ";
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin priv leader'>Leader</a>  ";
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin priv rl'>RL</a>  ";
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin priv mod'>Mod</a>  ";		
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin priv guildadmin'>Guildadmin</a>  ";
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin priv guild'>Guildmembers</a>\n";
		} else 
			$list .= "Current Status: <red>Unused<end>. \n";

		$list .= "\n\n<u><highlight>Guild Channel:<end></u>\n";
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$cmd' AND `type` = 'guild' AND `module` = '$module'");
		if($db->numrows() == 1) {
			$row = $db->fObject();
			
			$found_guild = 1;
			
			if($row->admin == 1)
				$row->admin = "Raidleader";
			elseif ($row->admin == 2)
				$row->admin = "Moderator";
			elseif ($row->admin == 3)
				$row->admin = "Administrator";
			
			if($row->status == 1)
				$status = "<green>Enabled<end>";
			else
				$status = "<red>Disabled<end>";

			$list .= "Current Status: $status (Access: $row->admin) \n";
			$list .= "Enable or Disable Command: ";
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." enable guild'>ON</a>  ";
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." disable guild'>OFF</a>\n";

			$list .= "Set minimum access lvl to use this command: ";
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin guild all'>All</a>  ";
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin guild rl'>RL</a>  ";
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin guild mod'>Mod</a>  ";
			$list .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin guild guildadmin'>Guildadmin</a>  ";
		} else 
			$list .= "Current Status: <red>Unused<end>. \n";

		$db->query("SELECT * FROM cmdcfg_<myname> WHERE dependson = '$cmd' AND `type` = 'msg' AND `cmdevent` = 'subcmd' AND `module` = '$module'");
		if($db->numrows() != 0) {
			
			$list .= "\n\n<u><highlight>Available Subcommands in tells<end></u>\n";
			while($row = $db->fObject()) {
				if($row->description != "")
					$list .= "Description: $row->description\n";
				else
					$list .= "Command: $row->cmd\n";
					
				if($row->admin == 1)
					$row->admin = "Leader";
				elseif($row->admin == 2)
					$row->admin = "Raidleader";
				elseif ($row->admin == 3)
					$row->admin = "Moderator";
				elseif ($row->admin == 4)
					$row->admin = "Administrator";
				
				$list .= "Current Access: <highlight>$row->admin<end> \n";
				$list .= "Set min. access lvl to use this command: ";
				$list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin msg all'>All</a>  ";
				$list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin msg leader'>Leader</a>  ";
				$list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin msg rl'>RL</a>  ";
				$list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin msg mod'>Mod</a>  ";
				$list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin msg guildadmin'>Guildadmin</a>  ";
				$list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin msg guild'>Guildmembers</a>\n\n";
			}
		}

		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `dependson` = '$cmd' AND `type` = 'priv' AND `cmdevent` = 'subcmd' AND `module` = '$module'");
		if($db->numrows() != 0) {
			$list .= "\n\n<u><highlight>Available Subcommands in Private Channel<end></u>\n";
			while($row = $db->fObject()) {
				if($row->description != "")
					$list .= "Description: $row->description\n";
				else
					$list .= "Command: $row->cmd\n";
					
				if($row->admin == 1)
					$row->admin = "Leader";
				elseif($row->admin == 2)
					$row->admin = "Raidleader";
				elseif ($row->admin == 3)
					$row->admin = "Moderator";
				elseif ($row->admin == 4)
					$row->admin = "Administrator";
				
				$list .= "Current Access: <highlight>$row->admin<end> \n";
				$list .= "Set min. access lvl to use this command: ";
				$list .= "<a href='chatcmd:///tell <myname> subcmd ".$row->cmd." admin priv all'>All</a>  ";
				$list .= "<a href='chatcmd:///tell <myname> subcmd ".$row->cmd." admin priv leader'>Leader</a>  ";
				$list .= "<a href='chatcmd:///tell <myname> subcmd ".$row->cmd." admin priv rl'>RL</a>  ";
				$list .= "<a href='chatcmd:///tell <myname> subcmd ".$row->cmd." admin priv mod'>Mod</a>  ";
				$list .= "<a href='chatcmd:///tell <myname> subcmd ".$row->cmd." admin priv guildadmin'>Guildadmin</a>  ";
				$list .= "<a href='chatcmd:///tell <myname> subcmd ".$row->cmd." admin priv guild'>Guildmembers</a>\n\n";
			}
		}

		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `dependson` = '$cmd' AND `type` = 'guild' AND `cmdevent` = 'subcmd' AND `module` = '$module'");
		if($db->numrows() != 0) {
			$list .= "\n\n<u><highlight>Available Subcommands in Guild Channel<end></u>\n";
			while($row = $db->fObject()) {
				if($row->description != "")
					$list .= "Description: $row->description\n";
				else
					$list .= "Command: $row->cmd\n";
					
				if($row->admin == 1)
					$row->admin = "Leader";
				elseif($row->admin == 2)
					$row->admin = "Raidleader";
				elseif ($row->admin == 3)
					$row->admin = "Moderator";
				elseif ($row->admin == 4)
					$row->admin = "Administrator";
				
				$list .= "Current Access: <highlight>$row->admin<end> \n";
				$list .= "Set min. access lvl to use this command: ";
				$list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin guild all'>All</a>  ";
				$list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin guild rl'>RL</a>  ";
				$list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin guild mod'>Mod</a>  ";
				$list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin guild guildadmin'>Guildadmin</a>  ";
				$list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin guild guild'>Guildmembers</a>\n\n";
			}
		}		
		$msg = bot::makeLink(ucfirst($cmd)." config", $list);
	}
	bot::send($msg, $sender);
} elseif(eregi("^config grp (.+)$", $message, $arr)) {
	$grp = strtolower($arr[1]);

	$db->query("SELECT * FROM cmdcfg_<myname> WHERE `grp` = '$grp' AND `cmdevent` = 'cmd' ORDER BY `cmd`");
	if($db->numrows() == 0)
		$msg = "Could not find the group <highligh>$grp<end>";
	else {
		$list = "<header>::::: Configure group $grp :::::<end>\n\n";
		$list .= "<highlight><u>Commands of this group</u><end> \n";
		while($row = $db->fObject()) {
	  	  	if($oldcmd != $row->cmd) {
				$adv = "<a href='chatcmd:///tell <myname> config cmd $row->cmd $row->module'>Adv.</a>";
				if($row->description != "none")
				    $list .= "$row->description (Cmd: $row->cmd)($adv): $on  $off \n";
				else
				    $list .= "$row->cmd Cmd ($adv): $on  $off \n";
	            $oldcmd = $row->cmd;
	        }
		}
			
		$list .= "\n\n<u><highlight>Enable or disable group for seperate Channels</u><end> \n";	
		$list .= "Tells: <a href='chatcmd:///tell <myname> config grp ".$grp." enable msg'>ON</a>  ";
		$list .= "<a href='chatcmd:///tell <myname> config grp ".$grp." disable msg'>OFF</a>\n";
	
		$list .= "Private Channel: <a href='chatcmd:///tell <myname> config grp ".$grp." enable priv'>ON</a>  ";
		$list .= "<a href='chatcmd:///tell <myname> config grp ".$grp." disable priv'>OFF</a>\n";
		
		$list .= "Guild Channel: <a href='chatcmd:///tell <myname> config grp ".$grp." enable guild'>ON</a>  ";
		$list .= "<a href='chatcmd:///tell <myname> config grp ".$grp." disable guild'>OFF</a>\n";
		$list .= "\n\n";
		$list .= "<highlight><u>Set permissions for the group</u><end>\n";
		$list .= "Tells: <a href='chatcmd:///tell <myname> config grp ".$grp." admin msg all'>All</a>  ";
		$list .= "<a href='chatcmd:///tell <myname> config grp ".$grp." admin msg leader'>Leader</a>  ";
		$list .= "<a href='chatcmd:///tell <myname> config grp ".$grp." admin msg rl'>RL</a>  ";
		$list .= "<a href='chatcmd:///tell <myname> config grp ".$grp." admin msg mod'>Mod</a>  ";
		$list .= "<a href='chatcmd:///tell <myname> config grp ".$grp." admin msg guildadmin'>Guildadmin</a>  ";
		$list .= "<a href='chatcmd:///tell <myname> config grp ".$grp." admin msg guild'>Guildmembers</a>\n";
	
		$list .= "Private Channel: <a href='chatcmd:///tell <myname> config grp ".$grp." admin priv all'>All</a>  ";
		$list .= "<a href='chatcmd:///tell <myname> config grp ".$grp." admin priv leader'>Leader</a>  ";
		$list .= "<a href='chatcmd:///tell <myname> config grp ".$grp." admin priv rl'>RL</a>  ";
		$list .= "<a href='chatcmd:///tell <myname> config grp ".$grp." admin priv mod'>Mod</a>  ";
		$list .= "<a href='chatcmd:///tell <myname> config grp ".$grp." admin priv ga'>Guildadmin</a>\n";
	
		$list .= "Guild Channel: <a href='chatcmd:///tell <myname> config grp ".$grp." admin guild all'>All</a>  ";
		$list .= "<a href='chatcmd:///tell <myname> config grp ".$grp." admin guild rl'>RL</a>  ";
		$list .= "<a href='chatcmd:///tell <myname> config grp ".$grp." admin guild mod'>Mod</a>  ";
		$list .= "<a href='chatcmd:///tell <myname> config grp ".$grp." admin guild ga'>Guildadmin</a>  ";
		
		$msg = bot::makeLink(ucfirst($grp)." group config", $list);
	} 
	bot::send($msg, $sender);
} elseif(eregi("^config mod (.+)$", $message, $arr)) {
  	$mod = strtolower($arr[1]);
	$list = "<header>::::: Config for module $mod :::::<end>\n";
	$list .= "Here can you disable or enable Commandos/Events and also changing their Access Level\n";
	$list .= "The following options are available:\n";
	$list .= " - Click ON or Off behind the Modulename to Enable or Disable them completly.\n";
	$list .= " - Click ON or Off behind the Command/Eventname to Enable or Disable them.\n";
	$list .= " - Click Adv. behind the name to change their Status for the single Channels \n";
	$list .= "   and to change their Access Limit\n\n";

	$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'cmd' AND `type` != 'setup' AND `dependson` = 'none' AND `module` = '$mod'"
        ." UNION ALL"
        ." SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'event' AND `type` != 'setup' AND `dependson` = 'none' AND `module` = '$mod'"
        ." ORDER BY `cmd`");
	$data = $db->fObject("all");
	foreach($data as $row) {
        if($row->cmdevent == "cmd" && $oldcmd != $row->cmd) {
			if($row->grp == "none") {
				$on = "<a href='chatcmd:///tell <myname> config cmd ".$row->cmd." enable all'>ON</a>";
				$off = "<a href='chatcmd:///tell <myname> config cmd ".$row->cmd." disable all'>OFF</a>";
				$adv = "<a href='chatcmd:///tell <myname> config cmd ".$row->cmd."'>Adv.</a>";
		
				if($row->description != "none")
				    $list .= "$row->description ($adv): $on  $off \n";
				else
				    $list .= "$row->cmd Command ($adv): $on  $off \n";
				$oldcmd = $row->cmd;
			} elseif($group[$row->grp] == false) {
				$on = "<a href='chatcmd:///tell <myname> config grp ".$row->grp." enable all'>ON</a>";
				$off = "<a href='chatcmd:///tell <myname> config grp ".$row->grp." disable all'>OFF</a>";
				$adv = "<a href='chatcmd:///tell <myname> config grp ".$row->grp."'>Adv.</a>";

				$db->query("SELECT * FROM cmdcfg_<myname> WHERE `module` = 'none' AND `cmdevent` = 'group' AND `type` = '$row->grp'");
				if($db->numrows() == 1) {
				  	$temp = $db->fObject();
					if($temp->description != "none")
				    	$list .= "$temp->description ($adv): $on  $off \n";
					else
				    	$list .= "$temp->grp group ($adv): $on  $off \n";				 	 	
				}		
				$group[$row->grp] = true;
			}
		} elseif ($row->cmdevent == "event") {
				$on = "<a href='chatcmd:///tell <myname> config event ".$row->type." ".$row->file." enable all'>ON</a>";
				$off = "<a href='chatcmd:///tell <myname> config event ".$row->type." ".$row->file." disable all'>OFF</a>";

				if($row->status == 1)
					$status = "<green>Enabled<end>";
				else
					$status = "<red>Disabled<end>";
		
				if($row->description != "none")
				    $list .= "$row->description($status): $on  $off \n";
				else
				    $list .= "$row->type Event($status): $on  $off \n";			  	
		}
	}

	$msg = bot::makeLink("Configuration for module $mod", $list);
	bot::send($msg, $sender);
} elseif(eregi("^config help (.+) admin (all|leader|rl|mod|guildadmin|guild)$", $message, $arr)) {
  	$help = strtolower($arr[1]);
	$admin = $arr[2];

	switch($admin) {
	  	case "leader":
	  		$admin = 1;
	  	break;
	  	case "rl":
	  		$admin = 2;
	  	break;
	  	case "mod":
	  		$admin = 3;
	  	break;
	}
	
	$db->query("SELECT * FROM hlpcfg_<myname> WHERE `name` = '$help' ORDER BY `name`");
	if($db->numrows() == 0) {
		bot::send("The helpfile <highlight>$help<end> doesn´t exists!", $sender);		  	
		return;
	}
	$row = $db->fObject();
	$db->query("UPDATE hlpcfg_<myname> SET `admin` = '$admin' WHERE `name` = '$help'");
	$this->helpfiles[$row->cat][$row->name]["admin level"] = $admin;
	bot::send("Updated access for helpfile <highlight>$help<end> to <highlight>".ucfirst(strtolower($arr[2]))."<end>.", $sender);
} elseif(eregi("^config help (.+)$", $message, $arr)) {
  	$mod = strtoupper($arr[1]);
	$list = "<header>::::: Configure helpfiles for module $mod :::::<end>\n\n";

	$db->query("SELECT * FROM hlpcfg_<myname> WHERE module = '$mod' ORDER BY name");
	$data = $db->fObject("all");
	foreach($data as $row) {
	  	$list .= "<highlight><u>Helpfile</u><end>: $row->name\n";
	  	$list .= "<highlight><u>Description</u><end>: $row->description\n";
	  	$list .= "<highlight><u>Category</u><end>: $row->cat\n";
	  	$list .= "<highlight><u>Set Permission</u><end>: ";
		$list .= "<a href='chatcmd:///tell <myname> config help $row->name admin all'>All</a>  ";
		$list .= "<a href='chatcmd:///tell <myname> config help $row->name admin leader'>Leader</a>  ";
		$list .= "<a href='chatcmd:///tell <myname> config help $row->name admin rl'>RL</a>  ";
		$list .= "<a href='chatcmd:///tell <myname> config help $row->name admin mod'>Mod</a>  ";
		$list .= "<a href='chatcmd:///tell <myname> config help $row->name admin guildadmin'>Guildadmin</a>  ";
		$list .= "<a href='chatcmd:///tell <myname> config help $row->name admin guild'>Guildmembers</a>\n";	  
	  	$list .= "\n\n";
	}

	$msg = bot::makeLink("Configurate helpfiles for module $mod", $list);
	bot::send($msg, $sender);	
} else
	$syntax_error = true;

?>