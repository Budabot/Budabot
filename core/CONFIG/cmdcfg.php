<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Enable/Disable Command/events and sets Access Level for commands
   ** Version: 0.4
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 15.12.2005
   ** Date(last modified): 03.02.2007
   ** 
   ** Copyright (C) 2006, 2007 Carsten Lohmann
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

// show config pages
//  config cmd name
//  config subcmd name
//  config event type filename
//  config mod name
//  config help name

// change status
//  config cmd (enable|disable) [channel|module|name]
//  config subcmd (enable|disable) [channel|module|name]
//  config event (enable|disable) [type|module|filename]
//  config mod (enable|disable) module_name
//  config help (enable|disable) name

// change admin
//  config cmd (admin) [type|module|filename]
//  config subcmd (admin) [type|module|filename]
//  config help (admin) name

if (!function_exists('get_admin_description')) {
	function get_admin_description($admin) {
		$admin = strtolower($admin);
		if ($admin == 1 || $admin == "leader") {
			return "Leader";
		} else if ($admin == 2 || $admin == "rl") {
			return "Raidleader";
		} else if ($admin == 3 || $admin == "mod") {
			return "Moderator";
		} else if ($admin == 4 || $admin == "admin") {
			return "Administrator";
		} else {
			return ucfirst(strtolower($admin));
		}
	}
}

if (!function_exists('get_admin_value')) {
	function get_admin_value($admin) {
		$admin = strtolower($admin);
		switch ($admin) {
			case "rl":
				return 2;
			case "mod":
				return 3;
			case "admin":
				return 4;
			default:
				return $admin;
		}
	}
}
   
   
if (preg_match("/^config$/i", $message)) {
	$list = array();
	$list[] = array("header" => "<header>::::: Module Config :::::<end>\n\n", 
	"content" => "Org Commands - " .
		Text::make_chatcmd('Enable All', '/tell <myname> config cmd enable guild') . " " . 
		Text::make_chatcmd('Disable All', '/tell <myname> config cmd disable guild') . "\n" . 
	"Private Channel Commands - " . 
		Text::make_chatcmd('Enable All', '/tell <myname> config cmd enable priv') . " " . 
		Text::make_chatcmd('Disable All', '/tell <myname> config cmd disable priv') . "\n" . 
	"Private Message Commands - " .
		Text::make_chatcmd('Enable All', '/tell <myname> config cmd enable msg') . " " . 
		Text::make_chatcmd('Disable All', '/tell <myname> config cmd disable msg') . "\n" .
	"ALL Commands - " .
		Text::make_chatcmd('Enable All', '/tell <myname> config cmd enable all') . " " . 
		Text::make_chatcmd('Disable All', '/tell <myname> config cmd disable all') . "\n\n\n");
	
	$sql = "
		SELECT
			module,
			SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) count_enabled,
			SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) count_disabled
		FROM
			(SELECT module, status FROM cmdcfg_<myname> WHERE `cmdevent` = 'cmd'
			UNION
			SELECT module, status FROM eventcfg_<myname>) t
		GROUP BY
			module
		ORDER BY
			module ASC";

	$db->query($sql);
	$data = $db->fObject("all");
	forEach ($data as $row) {
		$db->query("SELECT * FROM hlpcfg_<myname> WHERE `module` = '".strtoupper($row->module)."'");
		$num = $db->numrows();
		if ($num > 0) {
			$b = "(<a href='chatcmd:///tell <myname> config help $row->module'>Helpfiles</a>)";
		} else {
			$b = "";
		}
			
		if ($row->count_enabled > 0 && $row->count_disabled > 0) {
			$a = "(<yellow>Partial<end>)";
		} else if ($row->count_disabled == 0) {
			$a = "(<green>Running<end>)";
		} else {
			$a = "(<red>Disabled<end>)";
		}
			
		$c = "(<a href='chatcmd:///tell <myname> config $row->module'>Configure</a>)";
	
		$on = "<a href='chatcmd:///tell <myname> config mod $row->module enable all'>On</a>";
		$off = "<a href='chatcmd:///tell <myname> config mod $row->module disable all'>Off</a>";
		$list[] = strtoupper($row->module)." $a ($on/$off) $c $b\n";
	}

	$msg = Text::make_structured_blob("Module Config", $list);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^config cmd (enable|disable) (all|guild|priv|msg)$/i", $message, $arr)) {
	$status = ($arr[1] == "enable" ? 1 : 0);
	$typeSql = ($arr[2] == "all" ? "`type` = 'guild' OR `type` = 'priv' OR `type` = 'msg'" : "`type` = '{$arr[2]}'");
	
	$sql = "SELECT type, file, cmd, admin FROM cmdcfg_<myname> WHERE `cmdevent` = 'cmd' AND ($typeSql)";
	$db->query($sql);
	$data = $db->fObject('all');
	forEach ($data as $row) {
	  	if ($status == 1) {
			Command::activate($row->type, $row->file, $row->cmd, $row->admin);
		} else {
			Command::deactivate($row->type, $row->file, $row->cmd);
		}
	}
	
	$sql = "UPDATE cmdcfg_<myname> SET `status` = $status WHERE (`cmdevent` = 'cmd' OR `cmdevent` = 'subcmd') AND ($typeSql)";
	$db->exec($sql);
	
	$chatBot->send("Command(s) updated successfully.", $sendto);	
} else if (preg_match("/^config (subcmd|mod|cmd|event) (.+) (enable|disable) (priv|msg|guild|all)$/i", $message, $arr)) {
	if ($arr[1] == "event") {
		$temp = explode(" ", $arr[2]);
	  	$event_type = $temp[0];
	  	$file = $temp[1];
	} else if ($arr[1] == 'cmd' || $arr[1] == 'subcmd') {
		$cmd = strtolower($arr[2]);
		$type = $arr[4];
	} else {
		$module = strtoupper($arr[2]);
		$type = $arr[4];
	}
		
	if ($arr[3] == "enable") {
		$status = 1;
	} else {
		$status = 0;
	}
	
	if ($arr[1] == "mod" && $type == "all") {
		$db->query("SELECT status, type, file, cmd, admin, cmdevent FROM cmdcfg_<myname> WHERE `module` = '$module'
					UNION
					SELECT status, type, file, '' AS cmd, '' AS admin, 'event' AS cmdevent FROM eventcfg_<myname> WHERE `module` = '$module' AND `type` <> 'setup'");
	} else if ($arr[1] == "mod" && $type != "all") {
		$db->query("SELECT status, type, file, cmd, admin, cmdevent FROM cmdcfg_<myname> WHERE `module` = '$module' AND `type` = '$type'
					UNION
					SELECT status, type, file, cmd AS '', admin AS '', cmdevent AS 'event' FROM eventcfg_<myname> WHERE `module` = '$module' AND `type` = '$event_type' AND `type` <> 'setup'");
	} else if ($arr[1] == "cmd" && $type != "all") {
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$cmd' AND `type` = '$type' AND `cmdevent` = 'cmd'");
	} else if ($arr[1] == "cmd" && $type == "all") {
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$cmd' AND `cmdevent` = 'cmd'");
	} else if ($arr[1] == "subcmd" && $type != "all") {
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$cmd' AND `type` = '$type' AND `cmdevent` = 'subcmd'");
	} else if ($arr[1] == "subcmd" && $type == "all") {
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$cmd' AND `cmdevent` = 'subcmd'");
	} else if ($arr[1] == "event" && $file != "") {
		$db->query("SELECT *, 'event' AS cmdevent FROM eventcfg_<myname> WHERE `file` = '$file' AND `type` = '$event_type' AND `type` <> 'setup'");	
	} else {
		$syntax_error = true;
		return;
	}

	if ($db->numrows() == 0) {
		if ($arr[1] == "mod" && $type == "all") {
			$msg = "Could not find the Module <highlight>$module<end>";
		} else if ($arr[1] == "mod" && $type != "all") {
			$msg = "Could not find the Module <highlight>$module<end> for Channel <highlight>$type<end>";
		} else if ($arr[1] == "cmd" && $type != "all") {
			$msg = "Could not find the Command <highlight>$cmd<end> for Channel <highlight>$type<end>";
		} else if ($arr[1] == "cmd" && $type == "all") {
			$msg = "Could not find the Command <highlight>$cmd<end>";
		} else if ($arr[1] == "subcmd" && $type != "all") {
			$msg = "Could not find the Subcommand <highlight>$cmd<end> for Channel <highlight>$type<end>";
		} else if ($arr[1] == "subcmd" && $type == "all") {
			$msg = "Could not find the Subcommand <highlight>$cmd<end>";
		} else if ($arr[1] == "event" && $file != "") {
			$msg = "Could not find the Event <highlight>$event_type<end> for File <highlight>$file<end>";
		}
		$chatBot->send($msg, $sendto);
		return;
	}

	if ($arr[1] == "mod" && $type == "all") {
		$msg = "Updated status of the module <highlight>$module<end> to <highlight>".$arr[3]."d<end>";
	} else if ($arr[1] == "mod" && $type != "all") {
		$msg = "Updated status of the module <highlight>$module<end> in Channel <highlight>$type<end> to <highlight>".$arr[3]."d<end>"; 
	} else if ($arr[1] == "cmd" && $type != "all") {
		$msg = "Updated status of command <highlight>$cmd<end> to <highlight>".$arr[3]."d<end> in Channel <highlight>$type<end>";
	} else if ($arr[1] == "cmd" && $type == "all") {
		$msg = "Updated status of command <highlight>$cmd<end> to <highlight>".$arr[3]."d<end>";
	} else if ($arr[1] == "subcmd" && $type != "all") {
		$msg = "Updated status of subcommand <highlight>$cmd<end> to <highlight>".$arr[3]."d<end> in Channel <highlight>$type<end>";
	} else if ($arr[1] == "subcmd" && $type == "all") {
		$msg = "Updated status of subcommand <highlight>$cmd<end> to <highlight>".$arr[3]."d<end>";
	} else if ($arr[1] == "event" && $type != "") {
		$msg = "Updated status of event <highlight>$event_type<end> to <highlight>".$arr[3]."d<end>";
	}

	$chatBot->send($msg, $sendto);

	$data = $db->fObject("all");
	forEach ($data as $row) {
		// only update the status if the status is different
		if ($row->status != $status) {
			if ($row->cmdevent == "event") {
				if ($status == 1) {
					Event::activate($row->type, $row->file);
				} else {
					Event::deactivate($row->type, $row->file);
				}
			} else if ($row->cmdevent == "cmd") {
				if ($status == 1) {
					Command::activate($row->type, $row->file, $row->cmd, $row->admin);
				} else {
					Command::deactivate($row->type, $row->file, $row->cmd, $row->admin);
				}
			}
		}
	}

	if ($arr[1] == "mod" && $type == "all") {
		$db->exec("UPDATE cmdcfg_<myname> SET `status` = $status WHERE `module` = '$module'");
		$db->exec("UPDATE eventcfg_<myname> SET `status` = $status WHERE `module` = '$module' AND `type` <> 'setup'");
	} else if ($arr[1] == "mod" && $type != "all") {
		$db->exec("UPDATE cmdcfg_<myname> SET `status` = $status WHERE `module` = '$module' AND `type` = '$type'");
		$db->exec("UPDATE eventcfg_<myname> SET `status` = $status WHERE `module` = '$module' AND `type` = '$event_type' AND `type` <> 'setup'");
	} else if ($arr[1] == "cmd" && $type != "all") {
		$db->exec("UPDATE cmdcfg_<myname> SET `status` = $status WHERE `cmd` = '$cmd' AND `type` = '$type' AND `cmdevent` = 'cmd'");
	} else if ($arr[1] == "cmd" && $type == "all") {
		$db->exec("UPDATE cmdcfg_<myname> SET `status` = $status WHERE `cmd` = '$cmd' AND `cmdevent` = 'cmd'");
	} else if ($arr[1] == "subcmd" && $type != "all") {
		$db->exec("UPDATE cmdcfg_<myname> SET `status` = $status WHERE `cmd` = '$cmd' AND `type` = '$type' AND `cmdevent` = 'subcmd'");
	} else if ($arr[1] == "subcmd" && $type == "all") {
		$db->exec("UPDATE cmdcfg_<myname> SET `status` = $status WHERE `cmd` = '$cmd' AND `cmdevent` = 'subcmd'");
	} else if ($arr[1] == "event" && $file != "") {
		$db->exec("UPDATE eventcfg_<myname> SET `status` = $status WHERE `type` = '$event_type' AND `file` = '$file' AND `type` <> 'setup'");
	}
	
	$data = $db->fObject("all");
	forEach ($data as $row) {
		// only update the status if the status is different
		if ($row->status != $status) {
			if ($row->cmdevent == "event") {
				if ($status == 1) {
					Event::activate($row->type, $row->file);
				} else {
					Event::deactivate($row->type, $row->file);
				}
			} else if ($row->cmdevent == "cmd") {
				if ($status == 1) {
					Command::activate($row->type, $row->file, $row->cmd, $row->admin);
				} else {
					Command::deactivate($row->type, $row->file, $row->cmd, $row->admin);
				}
			}
		}
	}

	// for subcommands which are handled differently
	$chatBot->subcommands = array();
	Subcommand::loadSubcommands();
} else if (preg_match("/^config (subcmd|cmd) (.+) admin (msg|priv|guild|all) (all|leader|rl|mod|admin|guildadmin|guild)$/i", $message, $arr)) {
	$category = strtolower($arr[1]);
	$command = strtolower($arr[2]);
	$channel = strtolower($arr[3]);
	$admin = strtolower($arr[4]);

	$admin = get_admin_value($admin);

	if ($category == "cmd") {
		if ($channel == "all") {
			$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$command' AND `cmdevent` = 'cmd'");
		} else {
			$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$command' AND `type` = '$channel' AND `cmdevent` = 'cmd'");
		}

		if ($db->numrows() == 0) {
			if ($channel == "all") {
				$msg = "Could not find the command <highlight>$command<end>";
			} else {
				$msg = "Could not find the command <highlight>$command<end> for Channel <highlight>$channel<end>";
			}
		  	$chatBot->send($msg, $sendto);
		  	return;
		}

		if ($channel == 'all') {
			if ($chatBot->commands['msg'][$command]) {
				$chatBot->commands['msg']["admin"] = $admin;
			}
			if ($chatBot->commands['priv'][$command]) {
				$chatBot->commands['priv'][$command]["admin"] = $admin;
			}
			if ($chatBot->commands['guild'][$command]) {
				$chatBot->commands['guild'][$command]["admin"] = $admin;
			}
		} else {
			if ($chatBot->commands[$channel][$command]) {
				$chatBot->commands[$channel][$command]["admin"] = $admin;
			}
		}

		if ($channel == "all") {
			$db->exec("UPDATE cmdcfg_<myname> SET `admin` = '$admin' WHERE `cmd` = '$command' AND `cmdevent` = 'cmd'");
			$msg = "Updated access of command <highlight>$command<end> to <highlight>$admin<end>";
		} else {
			$db->exec("UPDATE cmdcfg_<myname> SET `admin` = '$admin' WHERE `cmd` = '$command' AND `type` = '$channel' AND `cmdevent` = 'cmd'");
			$msg = "Updated access of command <highlight>$command<end> in Channel <highlight>$channel<end> to <highlight>$admin<end>";
		}
	} else {  // if ($category == 'subcmd')
		$sql = "SELECT * FROM cmdcfg_<myname> WHERE `type` = '$channel' AND `cmdevent` = 'subcmd' AND `cmd` = '$command'";
		$db->query($sql);
		if ($db->numrows() == 0) {
			$msg = "Could not find the subcmd <highlight>$command<end> for Channel <highlight>$channel<end>";
		  	$chatBot->send($msg, $sendto);
		  	return;
		}
		$row = $db->fObject();
		$db->exec("UPDATE cmdcfg_<myname> SET `admin` = '$admin' WHERE `type` = '$channel' AND `cmdevent` = 'subcmd' AND `cmd` = '$command'");
		$chatBot->subcommands = array();
		Subcommand::loadSubcommands();
		$msg = "Updated access of sub command <highlight>$command<end> in Channel <highlight>$channel<end> to <highlight>$admin<end>";
	}
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^config cmd ([a-z0-9_]+)$/i", $message, $arr)) {
	$cmd = strtolower($arr[1]);
	$found_msg = 0;
	$found_priv = 0;
	$found_guild = 0;

	$alias_cmd = CommandAlias::get_command_by_alias($cmd);
	if ($alias_cmd != null) {
		$cmd = $alias_cmd;
	}

	$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '{$cmd}'");
	if ($db->numrows() == 0) {
		$msg = "Could not find the command '<highlight>$cmd<end>'";
	} else {
		$list = array();
		$list[] = "<header>::::: Configure command $cmd :::::<end>\n\n";
		$aliases = CommandAlias::find_aliases_by_command($cmd);
		$count = 0;
		forEach ($aliases as $row) {
			if ($row->status == 1) {
				$count++;
				$aliases_blob .= "{$row->alias}, ";
			}
		}
		
		if ($count > 0) {
			$list[] = "<highlight>Aliases:<end> $aliases_blob \n\n";
		}
		
		$l = "";
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$cmd' AND `type` = 'msg'");
		if ($db->numrows() == 1) {
			$row = $db->fObject();

			$found_msg = 1;
			
			$row->admin = get_admin_description($row->admin);
		
			if ($row->status == 1) {
				$status = "<green>Enabled<end>";
			} else {
				$status = "<red>Disabled<end>";
			}
			
			$l .= "Current Status: $status (Access: $row->admin) \n";
			$l .= "Enable or Disable Command: ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." enable msg'>ON</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." disable msg'>OFF</a>\n";

			$l .= "Set minimum access lvl to use this command: ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin msg all'>All</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin msg leader'>Leader</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin msg rl'>RL</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin msg mod'>Mod</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin msg admin'>Admin</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin msg guildadmin'>Guildadmin</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin msg guild'>Guild</a>\n";
		} else {
			$l .= "Current Status: <red>Unused<end>. \n";
		}
		$list[] = array("header" => "<u><highlight>Tells:<end></u>\n", "content" => $l, "footer" => "\n\n");
		
		$l = "";
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$cmd' AND `type` = 'priv'");
		if ($db->numrows() == 1) {
			$row = $db->fObject();

			$found_priv = 1;
			
			$row->admin = get_admin_description($row->admin);

			if ($row->status == 1) {
				$status = "<green>Enabled<end>";
			} else {
				$status = "<red>Disabled<end>";
			}

			$l .= "Current Status: $status (Access: $row->admin) \n";
			$l .= "Enable or Disable Command: ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." enable priv'>ON</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." disable priv'>OFF</a>\n";

			$l .= "Set minimum access lvl to use this command: ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin priv all'>All</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin priv leader'>Leader</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin priv rl'>RL</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin priv mod'>Mod</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin priv admin'>Admin</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin priv guildadmin'>Guildadmin</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin priv guild'>Guild</a>\n";
		} else {
			$l .= "Current Status: <red>Unused<end>. \n";
		}
		$list[] = array("header" => "<u><highlight>Private Channel:<end></u>\n", "content" => $l, "footer" => "\n\n");

		$l = "";
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$cmd' AND `type` = 'guild'");
		if ($db->numrows() == 1) {
			$row = $db->fObject();
			
			$found_guild = 1;
			
			$row->admin = get_admin_description($row->admin);
				
			if ($row->status == 1) {
				$status = "<green>Enabled<end>";
			} else {
				$status = "<red>Disabled<end>";
			}

			$l .= "Current Status: $status (Access: $row->admin) \n";
			$l .= "Enable or Disable Command: ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." enable guild'>ON</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." disable guild'>OFF</a>\n";

			$l .= "Set minimum access lvl to use this command: ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin guild all'>All</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin guild rl'>RL</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin guild mod'>Mod</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin guild admin'>Admin</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd ".$cmd." admin guild guildadmin'>Guildadmin</a>  ";
		} else {
			$l .= "Current Status: <red>Unused<end>. \n";
		}
		$list[] = array("header" => "<u><highlight>Guild Channel:<end></u>\n", "content" => $l, "footer" => "\n\n");
		
		$subcmd_list = '';

		$db->query("SELECT * FROM cmdcfg_<myname> WHERE dependson = '$cmd' AND `type` = 'msg' AND `cmdevent` = 'subcmd'");
		if ($db->numrows() != 0) {
			
			$subcmd_list .= "<u><highlight>Available Subcommands in tells<end></u>\n";
			while ($row = $db->fObject()) {
				$subcmd_list .= "Command: $row->cmd\n";
				if ($row->description != "") {
					$subcmd_list .= "Description: $row->description\n";
				}
				
				$row->admin = get_admin_description($row->admin);
				
				if ($row->status == 1) {
					$status = "<green>Enabled<end>";
				} else {
					$status = "<red>Disabled<end>";
				}

				$subcmd_list .= "Current Status: $status (Access: $row->admin) \n";
				$subcmd_list .= "Enable or Disable Command: ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." enable guild'>ON</a>  ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." disable guild'>OFF</a>\n";
				
				$subcmd_list .= "Set min. access lvl to use this command: ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin msg all'>All</a>  ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin msg leader'>Leader</a>  ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin msg rl'>RL</a>  ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin msg mod'>Mod</a>  ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin msg admin'>Admin</a>  ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin msg guildadmin'>Guildadmin</a>  ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin msg guild'>Guild</a>\n\n";
			}
		}

		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `dependson` = '$cmd' AND `type` = 'priv' AND `cmdevent` = 'subcmd'");
		if ($db->numrows() != 0) {
			$subcmd_list .= "<u><highlight>Available Subcommands in Private Channel<end></u>\n";
			while ($row = $db->fObject()) {
				$subcmd_list .= "Command: $row->cmd\n";
				if ($row->description != "") {
					$subcmd_list .= "Description: $row->description\n";
				}
					
				$row->admin = get_admin_description($row->admin);
				
				if ($row->status == 1) {
					$status = "<green>Enabled<end>";
				} else {
					$status = "<red>Disabled<end>";
				}

				$subcmd_list .= "Current Status: $status (Access: $row->admin) \n";
				$subcmd_list .= "Enable or Disable Command: ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." enable guild'>ON</a>  ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." disable guild'>OFF</a>\n";

				$subcmd_list .= "Set min. access lvl to use this command: ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin priv all'>All</a>  ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin priv leader'>Leader</a>  ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin priv rl'>RL</a>  ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin priv mod'>Mod</a>  ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin priv admin'>Admin</a>  ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin priv guildadmin'>Guildadmin</a>  ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin priv guild'>Guild</a>\n\n";
			}
		}

		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `dependson` = '$cmd' AND `type` = 'guild' AND `cmdevent` = 'subcmd'");
		if ($db->numrows() != 0) {
			$subcmd_list .= "<u><highlight>Available Subcommands in Guild Channel<end></u>\n";
			while ($row = $db->fObject()) {
				$subcmd_list .= "Command: $row->cmd\n";
				if ($row->description != "") {
					$subcmd_list .= "Description: $row->description\n";
				}
					
				$row->admin = get_admin_description($row->admin);
				
				if ($row->status == 1) {
					$status = "<green>Enabled<end>";
				} else {
					$status = "<red>Disabled<end>";
				}

				$subcmd_list .= "Current Status: $status (Access: $row->admin) \n";
				$subcmd_list .= "Enable or Disable Command: ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." enable guild'>ON</a>  ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." disable guild'>OFF</a>\n";
				
				$subcmd_list .= "Set min. access lvl to use this command: ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin guild all'>All</a>  ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin guild rl'>RL</a>  ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin guild mod'>Mod</a>  ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin guild admin'>Admin</a>  ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin guild guildadmin'>Guildadmin</a>  ";
				$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd ".$row->cmd." admin guild guild'>Guild</a>\n\n";
			}
		}
		
		if ($subcmd_list) {
			$list[] = array("header" => "<header> ::: Subcommands ::: <end>\n\n", "content" => $subcmd_list);
		}
		
		$help = Help::find($cmd, $sender);
		if ($help) {
			$list[] = $help;
		}
		
		$msg = Text::make_structured_blob(ucfirst($cmd)." config", $list);
	}
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^config help (.+) admin (all|leader|rl|mod|admin|guildadmin|guild)$/i", $message, $arr)) {
  	$help = strtolower($arr[1]);
	$admin = $arr[2];

	$admin = get_admin_value($admin);
	
	$db->query("SELECT * FROM hlpcfg_<myname> WHERE `name` = '$help' ORDER BY `name`");
	if ($db->numrows() == 0) {
		$chatBot->send("The helpfile <highlight>$help<end> doesn't exist!", $sendto);		  	
		return;
	}
	$row = $db->fObject();
	$db->exec("UPDATE hlpcfg_<myname> SET `admin` = '$admin' WHERE `name` = '$help'");
	$chatBot->helpfiles[$row->name]["admin"] = $admin;
	$chatBot->send("Updated access for helpfile <highlight>$help<end> to <highlight>".ucfirst(strtolower($arr[2]))."<end>.", $sendto);
} else if (preg_match("/^config help (.+)$/i", $message, $arr)) {
  	$mod = strtoupper($arr[1]);
	$list = array();
	$list[] = "<header> :::::: Configure helpfiles for module $mod :::::: <end>\n\n";

	$db->query("SELECT * FROM hlpcfg_<myname> WHERE module = '$mod' ORDER BY name");
	$data = $db->fObject("all");
	if (count($data) == 0) {
		$msg = "Could not file any help files for module '<highlight>$mod<end>'";
	} else {
		forEach ($data as $row) {
			$l = "";
			$l .= "<highlight><u>Helpfile</u><end>: $row->name\n";
			$l .= "<highlight><u>Description</u><end>: $row->description\n";
			$l .= "<highlight><u>Module</u><end>: $row->module\n";
			$l .= "<highlight><u>Set Permission</u><end>: ";
			$l .= "<a href='chatcmd:///tell <myname> config help $row->name admin all'>All</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config help $row->name admin leader'>Leader</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config help $row->name admin rl'>RL</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config help $row->name admin mod'>Mod</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config help $row->name admin admin'>Admin</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config help $row->name admin guildadmin'>Guildadmin</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config help $row->name admin guild'>Guild</a>\n";
			$list[] = array("content" => $l, "footer" => "\n\n");
		}
		$msg = Text::make_structured_blob("Configure helpfiles for module $mod", $list);
	}
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^config ([a-z0-9_]*)$/i", $message, $arr)) {
	$module = strtoupper($arr[1]);
	$found = false;

	$on = "<a href='chatcmd:///tell <myname> config mod {$module} enable all'>On</a>";
	$off = "<a href='chatcmd:///tell <myname> config mod {$module} disable all'>Off</a>";
	
	$list = array();
	$list[] = "<header> :::::: $module Configuration :::::: <end>\n\n";
	$list[] = "<highlight>{$module}<end> - Enable/disable: ($on/$off)\n";
	$l = "";
	$lh = "";

 	$db->query("SELECT * FROM settings_<myname> WHERE `module` = '$module'");
	if ($db->numrows() > 0) {
		$found = true;
		$lh = "\n<i>Settings</i>\n";
	}
 	while ($row = $db->fObject()) {
		$l .= $row->description;

		if ($row->mode == "edit") {
			$l .= " (<a href='chatcmd:///tell <myname> settings change $row->name'>Modify</a>)";
		}
	
		$l .= ":  ";

		$options = explode(";", $row->options);
		if ($row->type == "color") {
			$l .= $row->value."Current Color</font>\n";
		} else if ($row->intoptions != "") {
			$intoptions = explode(";", $row->intoptions);
			$intoptions2 = array_flip($intoptions);
			$key = $intoptions2[$row->value];
			$l .= "<highlight>{$options[$key]}<end>\n";
		} else {
			$l .= "<highlight>{$row->value}<end>\n";	
		}
	}
	
	if ($lh != "") {
		$list[] = array("header" => $lh, "content" => $l);
	}

	$sql = 
		"SELECT
			*,
			SUM(CASE WHEN type = 'guild' THEN 1 ELSE 0 END) guild_avail,
			SUM(CASE WHEN type = 'guild' AND status = 1 THEN 1 ELSE 0 END) guild_status,
			SUM(CASE WHEN type ='priv' THEN 1 ELSE 0 END) priv_avail,
			SUM(CASE WHEN type = 'priv' AND status = 1 THEN 1 ELSE 0 END) priv_status,
			SUM(CASE WHEN type ='msg' THEN 1 ELSE 0 END) msg_avail,
			SUM(CASE WHEN type = 'msg' AND status = 1 THEN 1 ELSE 0 END) msg_status
		FROM
			cmdcfg_<myname> c
		WHERE
			(`cmdevent` = 'cmd' OR `cmdevent` = 'subcmd')
			AND `module` = '$module'
		GROUP BY
			cmd";
	$db->query($sql);
	$l = "";
	$lh = "";
	if ($db->numrows() > 0) {
		$found = true;
		$lh = "\n<i>Commands</i>\n";
	}
	$data = $db->fObject("all");
	forEach ($data as $row) {
		$guild = '';
		$priv = '';
		$msg = '';

		if ($row->cmdevent == 'cmd') {
			$on = "<a href='chatcmd:///tell <myname> config cmd $row->cmd enable all'>ON</a>";
			$off = "<a href='chatcmd:///tell <myname> config cmd $row->cmd disable all'>OFF</a>";
			$adv = "<a href='chatcmd:///tell <myname> config cmd $row->cmd'>Adv.</a>";
		} else if ($row->cmdevent == 'subcmd') {
			$on = "<a href='chatcmd:///tell <myname> config subcmd $row->cmd enable all'>ON</a>";
			$off = "<a href='chatcmd:///tell <myname> config subcmd $row->cmd disable all'>OFF</a>";
			//$adv = "<a href='chatcmd:///tell <myname> config subcmd $row->cmd'>Adv.</a>";
		}
		
		if ($row->msg_avail == 0) {
			$tell = "|_";
		} else if ($row->msg_status == 1) {
			$tell = "|<green>T<end>";
		} else {
			$tell = "|<red>T<end>";
		}
		
		if ($row->guild_avail == 0) {
			$guild = "|_";
		} else if ($row->guild_status == 1) {
			$guild = "|<green>G<end>";
		} else {
			$guild = "|<red>G<end>";
		}
		
		if ($row->priv_avail == 0) {
			$priv = "|_";
		} else if ($row->priv_status == 1) {
			$priv = "|<green>P<end>";
		} else {
			$priv = "|<red>P<end>";
		}

		if ($row->description != "") {
			$l .= "$row->cmd ($adv$tell$guild$priv): $on  $off - ($row->description)\n";
		} else {
			$l .= "$row->cmd - ($adv$tell$guild$priv): $on  $off\n";
		}
	}
	if ($lh != "") {
		$list[] = array("header" => $lh, "content" => $l);
	}
	
	$l = "";
	$lh = "";
	$db->query("SELECT * FROM eventcfg_<myname> WHERE `type` <> 'setup' AND `module` = '$module'");
	if ($db->numrows() > 0) {
		$found = true;
		$lh = "\n<i>Events</i>\n";
	}
	while ($row = $db->fObject()) {
		$on = "<a href='chatcmd:///tell <myname> config event ".$row->type." ".$row->file." enable all'>ON</a>";
		$off = "<a href='chatcmd:///tell <myname> config event ".$row->type." ".$row->file." disable all'>OFF</a>";

		if ($row->status == 1) {
			$status = "<green>Enabled<end>";
		} else {
			$status = "<red>Disabled<end>";
		}

		if ($row->description != "none") {
			$l .= "$row->type ($row->description) - ($status): $on  $off \n";
		} else {
			$l .= "$row->type - ($status): $on  $off \n";
		}
	}
	if ($lh != "") {
		$list[] = array("header" => $lh, "content" => $l);
	}

	if ($found) {
		$msg = Text::make_structured_blob("$module Configuration", $list);
	} else {
		$msg = "Could not find module '<highlight>$module<end>'";
	}
 	$chatBot->send($msg, $sendto);
} else
	$syntax_error = true;

?>