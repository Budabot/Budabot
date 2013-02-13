<?php

namespace Budabot\User\Modules;

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'ts',
 *		accessLevel = 'guild',
 *		description = 'Show users connected to Teamspeak3 server',
 *		help        = 'ts.txt'
 *	)
 */
class TeamspeakController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $settingManager;

	/** @Inject */
	public $text;

	/**
	 * @Setup
	 */
	public function setup() {
		$this->settingManager->add($this->moduleName, "ts_username", "TS Server username", "edit", "text", 'serveradmin', 'serveradmin');
		$this->settingManager->add($this->moduleName, "ts_password", "TS Server password", "edit", "text", 'password');
		$this->settingManager->add($this->moduleName, "ts_queryport", "TS Server query port", "edit", "number", '10011', '10011');
		$this->settingManager->add($this->moduleName, "ts_clientport", "TS Server client port", "edit", "number", '9987', '9987');
		$this->settingManager->add($this->moduleName, "ts_description", "TS Server description", "edit", "text", 'Teamspeak 3 Server');
		$this->settingManager->add($this->moduleName, "ts_server", "TS Server IP/domain name", "edit", "text", '127.0.0.1', '127.0.0.1');
		$this->settingManager->add($this->moduleName, "ts_server_id", "TS Server id", "edit", "number", '1', '1');
	}

	/**
	 * @Event("logOn")
	 * @Description("Sends TS status to org members logging on")
	 * @DefaultStatus("0")
	 */
	public function sendTSStatusLogonEvent($eventObj) {
		if ($this->chatBot->is_ready() && isset($this->chatBot->guildmembers[$eventObj->sender])) {
			$msg = $this->getTeamspeak3Status();
			$this->chatBot->sendTell($msg, $eventObj->sender);
		}
	}

	/**
	 * @HandlesCommand("ts")
	 * @Matches("/^ts$/i")
	 */
	public function tsCommand($message, $channel, $sender, $sendto, $args) {
		$msg = $this->getTeamspeak3Status();
		$sendto->reply($msg);
	}

	public function getTeamspeak3Status() {
		$ts = new Teamspeak3(
			$this->settingManager->get('ts_username'),
			$this->settingManager->get('ts_password'),
			$this->settingManager->get('ts_server'),
			$this->settingManager->get('ts_queryport'),
			$this->settingManager->get('ts_server_id'));

		try {
			$server = $this->settingManager->get('ts_server');
			$clientPort = $this->settingManager->get('ts_clientport');
			$serverLink = $this->text->make_chatcmd($server, "/start http://ts3server:://$server:$clientPort");

			$users = $ts->exec('clientlist');
			$count = 0;
			$blob = "Server: $serverLink\n";
			$blob .= "Description: <highlight>" . $this->settingManager->get('ts_description') . "<end>\n\n";
			$blob .= "Users:\n";
			forEach ($users as $user) {
				if ($user['client_type'] == 0) {
					$blob .= "<highlight>{$user['client_nickname']}<end>\n";
					$count++;
				}
			}
			if ($count == 0) {
				$blob .= "<i>No users connected</i>\n";
			}
			$blob .= "\n\nTeamspeak 3 support by Tshaar (RK2)";
			$msg = $this->text->make_blob("{$count} user(s) on Teamspeak", $blob);
		} catch (Exception $e) {
			$msg = "Error! " . $e->getMessage();
		}

		return $msg;
	}
}

?>
