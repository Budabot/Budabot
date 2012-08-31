<?php
/**
 * @Instance
 */
class ConfigController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $text;

	/** @Inject */
	public $db;

	/** @Inject */
	public $commandManager;

	/** @Inject */
	public $eventManager;

	/** @Inject */
	public $subcommand;

	/** @Inject */
	public $commandAlias;

	/** @Inject */
	public $help;

	/** @Inject */
	public $setting;

	/**
	 * @Setup
	 * This handler is called on bot startup.
	 */
	public function setup() {

		// construct list of command handlers
		$filename = array();
		$reflectedClass = new ReflectionClass($this);
		forEach ($reflectedClass->getMethods() as $reflectedMethod) {
			if (preg_match('/command$/i', $reflectedMethod->name)) {
				$filename []= "{$reflectedMethod->class}.{$reflectedMethod->name}";
			}
		}
		$filename = implode(',', $filename);

		$this->commandManager->activate("msg", $filename, "config", "mod");
		$this->commandManager->activate("guild", $filename, "config", "mod");
		$this->commandManager->activate("priv", $filename, "config", "mod");

		$this->help->register($this->moduleName, "config", "config.txt", "mod", "Configure Commands/Events of the Bot");
	}

	/**
	 * This command handler lists list of modules which can be configured.
	 * Note: This handler has not been not registered, only activated.
	 *
	 * @Matches("/^config$/i")
	 */
	public function configCommand($message, $channel, $sender, $sendto, $args) {
		$list = array();
		$list[] = array("header" => "<header>::::: Module Config :::::<end>\n\n",
		"content" => "Org Commands - " .
			$this->text->make_chatcmd('Enable All', '/tell <myname> config cmd enable guild') . " " .
			$this->text->make_chatcmd('Disable All', '/tell <myname> config cmd disable guild') . "\n" .
		"Private Channel Commands - " .
			$this->text->make_chatcmd('Enable All', '/tell <myname> config cmd enable priv') . " " .
			$this->text->make_chatcmd('Disable All', '/tell <myname> config cmd disable priv') . "\n" .
		"Private Message Commands - " .
			$this->text->make_chatcmd('Enable All', '/tell <myname> config cmd enable msg') . " " .
			$this->text->make_chatcmd('Disable All', '/tell <myname> config cmd disable msg') . "\n" .
		"ALL Commands - " .
			$this->text->make_chatcmd('Enable All', '/tell <myname> config cmd enable all') . " " .
			$this->text->make_chatcmd('Disable All', '/tell <myname> config cmd disable all') . "\n\n\n");
	
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
	
		$data = $this->db->query($sql);
		forEach ($data as $row) {
			$data = $this->db->query("SELECT * FROM hlpcfg_<myname> WHERE `module` = ?", strtoupper($row->module));
			if (count($data) > 0) {
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
	
		$msg = $this->text->make_structured_blob("Module Config", $list);
		$sendto->reply($msg);
	}

	/**
	 * This command handler turns a channel of all modules on or off.
	 * Note: This handler has not been not registered, only activated.
	 *
	 * @Matches("/^config cmd (enable|disable) (all|guild|priv|msg)$/i")
	 */
	public function toggleChannelOfAllModulesCommand($message, $channel, $sender, $sendto, $args) {
		$status = ($args[1] == "enable" ? 1 : 0);
		$typeSql = ($args[2] == "all" ? "`type` = 'guild' OR `type` = 'priv' OR `type` = 'msg'" : "`type` = '{$args[2]}'");
	
		$sql = "SELECT type, file, cmd, admin FROM cmdcfg_<myname> WHERE `cmdevent` = 'cmd' AND ($typeSql)";
		$data = $this->db->query($sql);
		forEach ($data as $row) {
			if ($status == 1) {
				$this->commandManager->activate($row->type, $row->file, $row->cmd, $row->admin);
			} else {
				$this->commandManager->deactivate($row->type, $row->file, $row->cmd);
			}
		}
	
		$sql = "UPDATE cmdcfg_<myname> SET `status` = $status WHERE (`cmdevent` = 'cmd' OR `cmdevent` = 'subcmd') AND ($typeSql)";
		$this->db->exec($sql);
	
		$sendto->reply("Command(s) updated successfully.");
	}

	/**
	 * This command handler turns a channel of a single command, subcommand,
	 * module or event on or off.
	 * Note: This handler has not been not registered, only activated.
	 *
	 * @Matches("/^config (subcmd|mod|cmd|event) (.+) (enable|disable) (priv|msg|guild|all)$/i")
	 */
	public function toggleChannelCommand($message, $channel, $sender, $sendto, $args) {
		if ($args[1] == "event") {
			$temp = explode(" ", $args[2]);
			$event_type = strtolower($temp[0]);
			$file = $temp[1];
		} else if ($args[1] == 'cmd' || $args[1] == 'subcmd') {
			$cmd = strtolower($args[2]);
			$type = $args[4];
		} else { // $args[1] == 'mod'
			$module = strtoupper($args[2]);
			$type = $args[4];
		}
	
		if ($args[3] == "enable") {
			$status = 1;
		} else {
			$status = 0;
		}
	
		if ($args[1] == "mod" && $type == "all") {
			$sql = "SELECT status, type, file, cmd, admin, cmdevent FROM cmdcfg_<myname> WHERE `module` = '$module'
						UNION
					SELECT status, type, file, '' AS cmd, '' AS admin, 'event' AS cmdevent FROM eventcfg_<myname> WHERE `module` = '$module' AND `type` <> 'setup'";
		} else if ($args[1] == "mod" && $type != "all") {
			$sql = "SELECT status, type, file, cmd, admin, cmdevent FROM cmdcfg_<myname> WHERE `module` = '$module' AND `type` = '$type'
						UNION
					SELECT status, type, file, cmd AS '', admin AS '', cmdevent AS 'event' FROM eventcfg_<myname> WHERE `module` = '$module' AND `type` = '$event_type' AND `type` <> 'setup'";
		} else if ($args[1] == "cmd" && $type != "all") {
			$sql = "SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$cmd' AND `type` = '$type' AND `cmdevent` = 'cmd'";
		} else if ($args[1] == "cmd" && $type == "all") {
			$sql = "SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$cmd' AND `cmdevent` = 'cmd'";
		} else if ($args[1] == "subcmd" && $type != "all") {
			$sql = "SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$cmd' AND `type` = '$type' AND `cmdevent` = 'subcmd'";
		} else if ($args[1] == "subcmd" && $type == "all") {
			$sql = "SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$cmd' AND `cmdevent` = 'subcmd'";
		} else if ($args[1] == "event" && $file != "") {
			$sql = "SELECT *, 'event' AS cmdevent FROM eventcfg_<myname> WHERE `file` = '$file' AND `type` = '$event_type' AND `type` <> 'setup'";
		} else {
			$syntax_error = true;
			return;
		}
	
		$data = $this->db->query($sql);
	
		if (count($data) == 0) {
			if ($args[1] == "mod" && $type == "all") {
				$msg = "Could not find the Module <highlight>$module<end>";
			} else if ($args[1] == "mod" && $type != "all") {
				$msg = "Could not find the Module <highlight>$module<end> for Channel <highlight>$type<end>";
			} else if ($args[1] == "cmd" && $type != "all") {
				$msg = "Could not find the Command <highlight>$cmd<end> for Channel <highlight>$type<end>";
			} else if ($args[1] == "cmd" && $type == "all") {
				$msg = "Could not find the Command <highlight>$cmd<end>";
			} else if ($args[1] == "subcmd" && $type != "all") {
				$msg = "Could not find the Subcommand <highlight>$cmd<end> for Channel <highlight>$type<end>";
			} else if ($args[1] == "subcmd" && $type == "all") {
				$msg = "Could not find the Subcommand <highlight>$cmd<end>";
			} else if ($args[1] == "event" && $file != "") {
				$msg = "Could not find the Event <highlight>$event_type<end> for File <highlight>$file<end>";
			}
			$sendto->reply($msg);
			return;
		}
	
		if ($args[1] == "mod" && $type == "all") {
			$msg = "Updated status of the module <highlight>$module<end> to <highlight>".$args[3]."d<end>";
		} else if ($args[1] == "mod" && $type != "all") {
			$msg = "Updated status of the module <highlight>$module<end> in Channel <highlight>$type<end> to <highlight>".$args[3]."d<end>";
		} else if ($args[1] == "cmd" && $type != "all") {
			$msg = "Updated status of command <highlight>$cmd<end> to <highlight>".$args[3]."d<end> in Channel <highlight>$type<end>";
		} else if ($args[1] == "cmd" && $type == "all") {
			$msg = "Updated status of command <highlight>$cmd<end> to <highlight>".$args[3]."d<end>";
		} else if ($args[1] == "subcmd" && $type != "all") {
			$msg = "Updated status of subcommand <highlight>$cmd<end> to <highlight>".$args[3]."d<end> in Channel <highlight>$type<end>";
		} else if ($args[1] == "subcmd" && $type == "all") {
			$msg = "Updated status of subcommand <highlight>$cmd<end> to <highlight>".$args[3]."d<end>";
		} else if ($args[1] == "event" && $file != "") {
			$msg = "Updated status of event <highlight>$event_type<end> to <highlight>".$args[3]."d<end>";
		}
	
		$sendto->reply($msg);
	
		forEach ($data as $row) {
			// only update the status if the status is different
			if ($row->status != $status) {
				if ($row->cmdevent == "event") {
					if ($status == 1) {
						$this->eventManager->activate($row->type, $row->file);
					} else {
						$this->eventManager->deactivate($row->type, $row->file);
					}
				} else if ($row->cmdevent == "cmd") {
					if ($status == 1) {
						$this->commandManager->activate($row->type, $row->file, $row->cmd, $row->admin);
					} else {
						$this->commandManager->deactivate($row->type, $row->file, $row->cmd, $row->admin);
					}
				}
			}
		}
	
		if ($args[1] == "mod" && $type == "all") {
			$this->db->exec("UPDATE cmdcfg_<myname> SET `status` = ? WHERE `module` = ?", $status, $module);
			$this->db->exec("UPDATE eventcfg_<myname> SET `status` = ? WHERE `module` = ? AND `type` <> 'setup'", $status, $module);
		} else if ($args[1] == "mod" && $type != "all") {
			$this->db->exec("UPDATE cmdcfg_<myname> SET `status` = ? WHERE `module` = ? AND `type` = ?", $status, $module, $type);
			$this->db->exec("UPDATE eventcfg_<myname> SET `status` = ? WHERE `module` = ? AND `type` = ? AND `type` <> 'setup'", $status, $module, $event_type);
		} else if ($args[1] == "cmd" && $type != "all") {
			$this->db->exec("UPDATE cmdcfg_<myname> SET `status` = ? WHERE `cmd` = ? AND `type` = ? AND `cmdevent` = 'cmd'", $status, $cmd, $type);
		} else if ($args[1] == "cmd" && $type == "all") {
			$this->db->exec("UPDATE cmdcfg_<myname> SET `status` = ? WHERE `cmd` = ? AND `cmdevent` = 'cmd'", $status, $cmd);
		} else if ($args[1] == "subcmd" && $type != "all") {
			$this->db->exec("UPDATE cmdcfg_<myname> SET `status` = ? WHERE `cmd` = ? AND `type` = ? AND `cmdevent` = 'subcmd'", $status, $cmd, $type);
		} else if ($args[1] == "subcmd" && $type == "all") {
			$this->db->exec("UPDATE cmdcfg_<myname> SET `status` = ? WHERE `cmd` = ? AND `cmdevent` = 'subcmd'", $status, $cmd);
		} else if ($args[1] == "event" && $file != "") {
			$this->db->exec("UPDATE eventcfg_<myname> SET `status` = ? WHERE `type` = ? AND `file` = ? AND `type` <> 'setup'", $status, $event_type, $file);
		}
	
		// for subcommands which are handled differently
		$this->subcommand->loadSubcommands();
	}

	/**
	 * This command handler sets command's access level on a particular channel.
	 * Note: This handler has not been not registered, only activated.
	 *
	 * @Matches("/^config (subcmd|cmd) (.+) admin (msg|priv|guild|all) (all|rl|mod|admin|guild|member)$/i")
	 */
	public function setAccessLevelOfChannelCommand($message, $channel, $sender, $sendto, $args) {
		$category = strtolower($args[1]);
		$command = strtolower($args[2]);
		$channel = strtolower($args[3]);
		$admin = strtolower($args[4]);
	
		if ($category == "cmd") {
			if ($channel == "all") {
				$sql = "SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$command' AND `cmdevent` = 'cmd'";
			} else {
				$sql = "SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '$command' AND `type` = '$channel' AND `cmdevent` = 'cmd'";
			}
			$data = $this->db->query($sql);
	
			if (count($data) == 0) {
				if ($channel == "all") {
					$msg = "Could not find the command <highlight>$command<end>";
				} else {
					$msg = "Could not find the command <highlight>$command<end> for Channel <highlight>$channel<end>";
				}
				$sendto->reply($msg);
				return;
			}
	
			$this->commandManager->update_status($channel, $command, null, 1, $admin);
	
			if ($channel == "all") {
				$msg = "Updated access of command <highlight>$command<end> to <highlight>$admin<end>";
			} else {
				$msg = "Updated access of command <highlight>$command<end> in Channel <highlight>$channel<end> to <highlight>$admin<end>";
			}
		} else {  // if ($category == 'subcmd')
			$sql = "SELECT * FROM cmdcfg_<myname> WHERE `type` = ? AND `cmdevent` = 'subcmd' AND `cmd` = ?";
			$data = $this->db->query($sql, $channel, $command);
			if (count($data) == 0) {
				$msg = "Could not find the subcmd <highlight>$command<end> for Channel <highlight>$channel<end>";
				$sendto->reply($msg);
				return;
			}
	
			$this->db->exec("UPDATE cmdcfg_<myname> SET `admin` = ? WHERE `type` = ? AND `cmdevent` = 'subcmd' AND `cmd` = ?", $admin, $channel, $command);
			$this->subcommand->loadSubcommands();
			$msg = "Updated access of sub command <highlight>$command<end> in Channel <highlight>$channel<end> to <highlight>$admin<end>";
		}
		$sendto->reply($msg);
	}

	/**
	 * This command handler shows information and controls of a command in
	 * each channel.
	 * Note: This handler has not been not registered, only activated.
	 *
	 * @Matches("/^config cmd ([a-z0-9_]+)$/i")
	 */
	public function configCommandCommand($message, $channel, $sender, $sendto, $args) {
		$cmd = strtolower($args[1]);
		$found_msg = 0;
		$found_priv = 0;
		$found_guild = 0;
	
		$alias_cmd = $this->commandAlias->get_command_by_alias($cmd);
		if ($alias_cmd != null) {
			$cmd = $alias_cmd;
		}
	
		$data = $this->db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = ?", $cmd);
		if (count($data) == 0) {
			$msg = "Could not find the command '<highlight>$cmd<end>'";
		} else {
			$list = array();
			$list[] = "<header>::::: Configure command $cmd :::::<end>\n\n";
			$aliases = $this->commandAlias->find_aliases_by_command($cmd);
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
	
			$list[] = array("header" => "<u><highlight>Tells:<end></u>\n", "content" => $this->getCommandInfo($cmd, 'msg'), "footer" => "\n\n");
			$list[] = array("header" => "<u><highlight>Private Channel:<end></u>\n", "content" => $this->getCommandInfo($cmd, 'priv'), "footer" => "\n\n");
			$list[] = array("header" => "<u><highlight>Guild Channel:<end></u>\n", "content" => $this->getCommandInfo($cmd, 'guild'), "footer" => "\n\n");
	
			$subcmd_list = '';
			$output = $this->getSubCommandInfo($cmd, 'msg');
			if ($output) {
				$subcmd_list .= "<u><highlight>Available Subcommands in tells<end></u>\n";
				$subcmd_list .= $output;
			}
	
			$output = $this->getSubCommandInfo($cmd, 'priv');
			if ($output) {
				$subcmd_list .= "<u><highlight>Available Subcommands in Private Channel<end></u>\n";
				$subcmd_list .= $output;
			}
	
			$output = $this->getSubCommandInfo($cmd, 'guild');
			if ($output) {
				$subcmd_list .= "<u><highlight>Available Subcommands in Guild Channel<end></u>\n";
				$subcmd_list .= $output;
			}
	
			if ($subcmd_list) {
				$list[] = array("header" => "<header> ::: Subcommands ::: <end>\n\n", "content" => $subcmd_list);
			}
	
			$help = $this->help->find($cmd, $sender);
			if ($help) {
				$list[] = "<header> ::: Help ($cmd) ::: <end>\n\n" . $help;
			}
	
			$msg = $this->text->make_structured_blob(ucfirst($cmd)." config", $list);
		}
		$sendto->reply($msg);
	}

	/**
	 * This command handler sets access level of a help file.
	 * Note: This handler has not been not registered, only activated.
	 *
	 * @Matches("/^config help (.+) admin (all|rl|mod|admin|guild|member)$/i")
	 */
	public function setAccessLevelOfHelpfileCommand($message, $channel, $sender, $sendto, $args) {
		$helpTopic = strtolower($args[1]);
		$admin = $args[2];
	
		$row = $this->db->queryRow("SELECT * FROM hlpcfg_<myname> WHERE `name` = ? ORDER BY `name`", $helpTopic);
		if ($row === null) {
			$sendto->reply("The help topic <highlight>$helpTopic<end> doesn't exist!");
			return;
		}
	
		$this->help->update($helpTopic, $admin);
	
		$sendto->reply("Updated access for helpfile <highlight>$helpTopic<end> to <highlight>".ucfirst(strtolower($admin))."<end>.");
	}

	/**
	 * This command handler shows configuration of a helpfile.
	 * Note: This handler has not been not registered, only activated.
	 *
	 * @Matches("/^config help ([a-z0-9_]+)$/i")
	 */
	public function configHelpfileCommand($message, $channel, $sender, $sendto, $args) {
		$mod = strtoupper($args[1]);
		$blob = '';
	
		$data = $this->db->query("SELECT * FROM hlpcfg_<myname> WHERE module = ? ORDER BY name", $mod);
		if (count($data) == 0) {
			$msg = "Could not find any help files for module '<highlight>$mod<end>'";
		} else {
			forEach ($data as $row) {
				$blob .= "<pagebreak><highlight>Helpfile<end>: $row->name\n";
				$blob .= "<highlight>Description<end>: $row->description\n";
				$blob .= "<highlight>Module<end>: $row->module\n";
				$blob .= "<highlight>Current Permission<end>: $row->admin\n";
				$blob .= "<highlight>Set Permission<end>: ";
				$blob .= "<a href='chatcmd:///tell <myname> config help $row->name admin all'>All</a>  ";
				$blob .= "<a href='chatcmd:///tell <myname> config help $row->name admin member'>Member</a>  ";
				$blob .= "<a href='chatcmd:///tell <myname> config help $row->name admin guild'>Guild</a>  ";
				$blob .= "<a href='chatcmd:///tell <myname> config help $row->name admin rl'>RL</a>  ";
				$blob .= "<a href='chatcmd:///tell <myname> config help $row->name admin mod'>Mod</a>  ";
				$blob .= "<a href='chatcmd:///tell <myname> config help $row->name admin admin'>Admin</a>\n\n";
			}
			$msg = $this->text->make_blob("Configure helpfiles for $mod", $blob);
		}
		$sendto->reply($msg);
	}

	/**
	 * This command handler shows configuration and controls for a single module.
	 * Note: This handler has not been not registered, only activated.
	 *
	 * @Matches("/^config ([a-z0-9_]+)$/i")
	 */
	public function configModuleCommand($message, $channel, $sender, $sendto, $args) {
		$module = strtoupper($args[1]);
		$found = false;
	
		$on = "<a href='chatcmd:///tell <myname> config mod {$module} enable all'>Enable</a>";
		$off = "<a href='chatcmd:///tell <myname> config mod {$module} disable all'>Disable</a>";
		$configHelpFiles = $this->text->make_chatcmd('Configure', "/tell <myname> config help {$module}");
	
		$list = array();
		$list[] = "<header> :::::: $module Configuration :::::: <end>\n\n";
		$list[] = "Enable/disable entire module: ($on/$off)\n";
		$list[] = "Helpfiles: ($configHelpFiles)\n";
		$l = "";
		$lh = "";
	
		$data = $this->db->query("SELECT * FROM settings_<myname> WHERE `module` = ?", $module);
		if (count($data) > 0) {
			$found = true;
			$lh = "\n<i>Settings</i>\n";
		}
	
		forEach ($data as $row) {
			$l .= $row->description;
	
			if ($row->mode == "edit") {
				$l .= " (<a href='chatcmd:///tell <myname> settings change $row->name'>Modify</a>)";
			}
	
			$l .= ":  " . $this->setting->displayValue($row);
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
				AND `module` = ?
			GROUP BY
				cmd";
		$data = $this->db->query($sql, $module);
		$l = "";
		$lh = "";
		if (count($data) > 0) {
			$found = true;
			$lh = "\n<i>Commands</i>\n";
		}
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
		$data = $this->db->query("SELECT * FROM eventcfg_<myname> WHERE `type` <> 'setup' AND `module` = ?", $module);
		if (count($data) > 0) {
			$found = true;
			$lh = "\n<i>Events</i>\n";
		}
		forEach ($data as $row) {
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
			$msg = $this->text->make_structured_blob("$module Configuration", $list);
		} else {
			$msg = "Could not find module '<highlight>$module<end>'";
		}
		$sendto->reply($msg);
	}

	/**
	 * This helper method converts given short access level name to long name.
	 * TODO: can this be replaced with AccessLevel's getDisplayName() ?
	 */
	private function get_admin_description($admin) {
		$admin = strtolower($admin);
		if ($admin == "rl") {
			return "Raidleader";
		} else if ($admin == "mod") {
			return "Moderator";
		} else if ($admin == "admin") {
			return "Administrator";
		} else {
			return ucfirst(strtolower($admin));
		}
	}

	/**
	 * This helper method builds information and controls for given command.
	 */
	private function getCommandInfo($cmd, $type) {
		$l = "";
		$data = $this->db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = ? AND `type` = ?", $cmd, $type);
		if (count($data) == 0) {
			$l .= "Current Status: <red>Unused<end>. \n";
		} else if (count($data) == 1) {
			$row = $data[0];

			$found_msg = 1;

			$row->admin = $this->get_admin_description($row->admin);

			if ($row->status == 1) {
				$status = "<green>Enabled<end>";
			} else {
				$status = "<red>Disabled<end>";
			}

			$l .= "Current Status: $status (Access: $row->admin) \n";
			$l .= "Enable or Disable Command: ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd {$cmd} enable {$type}'>ON</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd {$cmd} disable {$type}'>OFF</a>\n";

			$l .= "Set minimum access lvl to use this command: ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd {$cmd} admin {$type} all'>All</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd {$cmd} admin {$type} member'>Member</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd {$cmd} admin {$type} guild'>Guild</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd {$cmd} admin {$type} rl'>RL</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd {$cmd} admin {$type} mod'>Mod</a>  ";
			$l .= "<a href='chatcmd:///tell <myname> config cmd {$cmd} admin {$type} admin'>Admin</a>\n";
		} else {
			LegacyLogger::log("ERROR", "CONFIG", "Multiple rows exists for cmd: '$cmd' and type: '$type'");
		}
		return $l;
	}

	/**
	 * This helper method builds information and controls for given subcommand.
	 */
	private function getSubCommandInfo($cmd, $type) {
		$subcmd_list = '';
		$data = $this->db->query("SELECT * FROM cmdcfg_<myname> WHERE dependson = ? AND `type` = ? AND `cmdevent` = 'subcmd'", $cmd, $type);
		forEach ($data as $row) {
			$subcmd_list .= "Command: $row->cmd\n";
			if ($row->description != "") {
				$subcmd_list .= "Description: $row->description\n";
			}

			$row->admin = $this->get_admin_description($row->admin);

			if ($row->status == 1) {
				$status = "<green>Enabled<end>";
			} else {
				$status = "<red>Disabled<end>";
			}

			$subcmd_list .= "Current Status: $status (Access: $row->admin) \n";
			$subcmd_list .= "Enable or Disable Command: ";
			$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd {$row->cmd} enable {$type}'>ON</a>  ";
			$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd {$row->cmd} disable {$type}'>OFF</a>\n";

			$subcmd_list .= "Set min. access lvl to use this command: ";
			$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd {$row->cmd} admin {$type} all'>All</a>  ";
			$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd {$row->cmd} admin {$type} member'>Member</a>  ";
			$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd {$row->cmd} admin {$type} guild'>Guild</a>  ";
			$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd {$row->cmd} admin {$type} rl'>RL</a>  ";
			$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd {$row->cmd} admin {$type} mod'>Mod</a>  ";
			$subcmd_list .= "<a href='chatcmd:///tell <myname> config subcmd {$row->cmd} admin {$type} admin'>Admin</a>\n\n";
		}
		return $subcmd_list;
	}
}
