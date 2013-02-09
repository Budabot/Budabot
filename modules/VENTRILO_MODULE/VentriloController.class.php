<?php

namespace budabot\user\modules;

require_once "vent.inc.php";
require_once "ventrilostatus.php";

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'vent',
 *		accessLevel = 'all', 
 *		description = 'Show Ventrilo Server info', 
 *		help        = 'vent.txt'
 *	)
 */
class VentriloController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $settingManager;
	
	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Setup */
	public function setup() {
		$this->settingManager->add($this->moduleName, "ventaddress", "Ventrilo Server Address", "edit", "text", "unknown");
		$this->settingManager->add($this->moduleName, "ventport", "Ventrilo Server Port", "edit", "number", "unknown");
		$this->settingManager->add($this->moduleName, "ventpass", "Ventrilo Server Password", "edit", "text", "unknown");

		$this->settingManager->add($this->moduleName, "showventpassword", "Show password with vent info?", "edit", "options", "1", "true;false", "1;0");
		$this->settingManager->add($this->moduleName, "showextendedinfo", "Show extended vent server info?", "edit", "options", "1", "true;false", "1;0");
	}
	
	/**
	 * @Event("logOn")
	 * @Description("Sends Vent status to org members logging on")
	 * @DefaultStatus("0")
	 */
	public function sendVentStatusLogonEvent($eventObj) {
		if ($this->chatBot->is_ready() && isset($this->chatBot->guildmembers[$eventObj->sender])) {
			$msg = $this->getVentStatus();
			$this->chatBot->sendTell($msg, $eventObj->sender);
		}
	}

	/**
	 * @HandlesCommand("vent")
	 * @Matches("/^vent$/i")
	 */
	public function ventCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->getVentStatus());
	}
	
	public function getVentStatus() {
		$stat = new CVentriloStatus;
		$stat->m_cmdcode	= "2";					// Detail mode. 1=General Status, 2=Detail

		// change config below this line only
		$stat->m_cmdhost	= $this->settingManager->get("ventaddress");	// enter your vent server ip or hostname here
		$stat->m_cmdport	= $this->settingManager->get("ventport");		// enter your vent server port number
		$stat->m_cmdpass	= $this->settingManager->get("ventpass");		// Status password if necessary.

		$lobby = new CVentriloChannel;
		$lobby->m_cid = 0;			// Channel ID.
		$lobby->m_pid = 0 ;			// Parent channel ID.
		$lobby->m_prot = 0;			// Password protected flag.
		$lobby->m_name = "Lobby";	// Channel name.
		$lobby->m_comm = "This is the lobby";	// Channel comment.
		$stat->m_channellist[] = $lobby;

		$msg = '';

		$rc = $stat->Request();
		if ($rc !== 0) {
			$msg = "<orange>Could not get ventrilo info: $stat->m_error<end>";
		} else {
			$page .= "Channels highlighted <orange>ORANGE<end> are password protected.\n\n";
			$page .= "Hostname: <highlight>{$stat->m_cmdhost}<end>\n";
			$page .= "Port Number: <highlight>{$stat->m_cmdport}<end>\n";

			if ($this->settingManager->get("showventpassword") == 1) {
				$page .= "Password: <highlight>{$stat->m_cmdpass}<end>\n";
			}

			$page .= "\nServer Name: <highlight>{$stat->m_name}<end>\n";
			$page .= "Users: <highlight>{$stat->m_clientcount} / {$stat->m_maxclients}<end>\n";

			if ($this->settingManager->get("showextendedinfo") == 1) {
				$page .= "Voice Encoder: <highlight>{$stat->m_voicecodec_code}<end> - {$stat->m_voicecodec_desc}\n";
				$page .= "Voice Format: <highlight>{$stat->m_voiceformat_code}<end> - {$stat->m_voiceformat_desc}\n";
				$page .= "Server Uptime: " . $this->util->unixtime_to_readable($stat->m_uptime, false) . "\n";
				$page .= "Server Platform: <highlight>{$stat->m_platform}<end>\n";
				$page .= "Server Version: <highlight>{$stat->m_version}<end>\n";
				$page .= "Number of channels: <highlight>{$stat->m_channelcount}<end>\n";
			}
			$page .= "\nChannels:\n";

			forEach ($stat->m_channellist as $channel) {
				$this->displayChannel($channel, $stat->m_clientlist, "", $page);
			}

			$msg = $this->text->make_blob("Ventrilo Info ({$stat->m_clientcount})", $page);
		}
		return $msg;
	}
	
	public function displayChannel(&$channel, &$clientlist, $prefix, &$output) {
		$prefix .= "    ";
		$output .= "<grey>--+<end> ";

		if ($channel->m_prot == 1) {
			$output .= "<orange>{$channel->m_name}<end>\n";
		} else {
			$output .= "{$channel->m_name}\n";
		}
		forEach($clientlist as $user) {
			if ($channel->m_cid == $user->m_cid) {
				$output .= "     <grey>|---<end> <highlight>{$user->m_name}<end> \n";
			}
		}
	}
}
