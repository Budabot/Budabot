<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'server', 
 *		accessLevel = 'all', 
 *		description = 'Show the server status', 
 *		help        = 'server.txt'
 *	)
 */
class ServerStatusController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $serverStatusManager;

	/**
	 * @HandlesCommand("server")
	 * @Matches("/^server$/i")
	 * @Matches("/^server (.)$/i")
	 */
	public function playfieldListCommand($message, $channel, $sender, $sendto, $args) {
		if (count($args) == 1) {
			$servernum = $this->chatBot->vars['dimension'];
		} else {
			$servernum = $args[1];
		}
		
		// config file uses '4' to indicate test server
		if ($servernum == '4') {
			$servernum = 't';
		}
		
		if ($servernum != 1 && $servernum != 2 && $servernum != 't') {
			return false;
		}

		$server = $this->serverStatusManager->lookup($servernum);
		if ($server->errorCode != 0) {
			$msg = $server->errorInfo;
		} else {
			$link = '';

			if ($server->servermanager == 1) {
				$link .= "<highlight>Servermanager<end> is <green>UP<end>\n";
			} else {
				$link .= "<highlight>Servermanager<end> is <red>DOWN<end>\n";
			}

			if ($server->clientmanager == 1) {
				$link .= "<highlight>Clientmanager<end> is <green>UP<end>\n";
			} else {
				$link .= "<highlight>Clientmanager<end> is <red>DOWN<end>\n";
			}

			if ($server->chatserver == 1) {
				$link .= "<highlight>Chatserver<end> is <green>UP<end>\n\n";
			} else {
				$link .= "<highlight>Chatserver<end> is <red>DOWN<end>\n\n";
			}

			$link .= "<highlight>Player distribution in % of total players online.<end>\n";
			ksort($server->data);
			forEach ($server->data as $zone => $proz) {
				$link .= "<highlight>$zone<end>: {$proz["players"]} \n";
			}

			$msg = $this->text->make_blob("$server->name Server Status", $link);
		}

		$sendto->reply($msg);
	}
}
