<?php

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
	public $setting;

	/** @Inject */
	public $text;

	/**
	 * @Setup
	 */
	public function setup() {
		$this->setting->add($this->moduleName, "ts_username", "Username for TS server", "edit", "text", 'serveradmin', 'serveradmin');
		$this->setting->add($this->moduleName, "ts_password", "Password for TS server", "edit", "text", 'password');
		$this->setting->add($this->moduleName, "ts_queryport", "ServerQuery port for the TS server", "edit", "number", '10011', '10011');
		$this->setting->add($this->moduleName, "ts_clientport", "Client port for the TS server", "edit", "number", '9987', '9987');
		$this->setting->add($this->moduleName, "ts_description", "Description for TS server", "edit", "text", 'Teamspeak 3 Server');
		$this->setting->add($this->moduleName, "ts_server", "IP/Domain name of the TS server", "edit", "text", '127.0.0.1', '127.0.0.1');
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
		$ts = new Teamspeak3($this->setting->get('ts_username'), $this->setting->get('ts_password'), $this->setting->get('ts_server'), $this->setting->get('ts_queryport'));

		try {
			$server = $this->setting->get('ts_server');
			$clientPort = $this->setting->get('ts_clientport');
			$serverLink = $this->text->make_chatcmd($server, "/start http://ts3server:://$server:$clientPort");

			$users = $ts->exec('clientlist');
			$count = 0;
			$blob = "Server: $serverLink\n";
			$blob .= "Description: <highlight>" . $this->setting->get('ts_description') . "<end>\n\n";
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
