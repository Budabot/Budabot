<?php

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'aospeak',
 *		accessLevel = 'all',
 *		description = 'Show org members connected to AOSpeak server',
 *		help        = 'aospeak.txt'
 *	)
 */
class AOSpeakController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $http;

	/** @Inject */
	public $text;

	/** @Inject */
	public $util;
	
	/**
	 * @HandlesCommand("aospeak")
	 * @Matches("/^aospeak org$/i")
	 */
	public function aospeakOrgCommand($message, $channel, $sender, $sendto, $args) {
		$url = "http://api.aospeak.com/org/" . $this->chatBot->vars['dimension'] . "/" . $this->chatBot->vars['my_guild_id'];
		$results = $this->http->get($url)->waitAndReturnResponse()->body;

		if ($results == "ORG_NOT_FOUND") {
			$msg = "Your org is not currently set up on AOSpeak. Please have your org president set up a channel first.";
			$sendto->reply($msg);
			return;
		}

		$users = json_decode($results);
		$count = count($users);
		if ($count == 0) {
			$msg = "No org members currently connected to AOSpeak.";
		} else {
			$blob = "Server: <highlight>voice.aospeak.com<end>";
			$blob .= "\n\nUsers:\n";
			forEach ($users as $user) {
				if ($user->idleTime >= 300000) {
					// if idle for at least 5 minutes
					$blob .= "<highlight>{$user->name}<end> ({$user->country}, idle for " . $this->util->unixtime_to_readable($user->idleTime / 1000, false) . ")\n";
				} else {
					$blob .= "<highlight>{$user->name}<end> ({$user->country})\n";
				}
			}
			$blob .= "\n\nProvided by " . $this->text->make_chatcmd("AOSpeak.com", "/start http://www.aospeak.com");
			$msg = $this->text->make_blob("AOSpeak Org ($count)", $blob);
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("aospeak")
	 * @Matches("/^aospeak$/i")
	 * @Matches("/^aospeak (\\d)$/i")
	 */
	public function aospeakCommand($message, $channel, $sender, $sendto, $args) {
		if (count($args) == 2) {
			$title = "AOSpeak Online RK" . $args[1];
			$url = "http://api.aospeak.com/online/" . $args[1];
		} else {
			$title = "AOSpeak Online";
			$url = "http://api.aospeak.com/online/";
		}
		$results = $this->http->get($url)->waitAndReturnResponse()->body;

		$users = json_decode($results);
		$count = count($users);
		if ($count == 0) {
			$msg = "No players currently connected to AOSpeak.";
		} else {
			$blob = "Server: <highlight>voice.aospeak.com<end>\n";

			$channels = array();
			forEach ($users as $user) {
				$channels[$user->channelName] []= $user;
			}

			forEach ($channels as $name => $users) {
				$blob .= "\n<green>$name<end>\n";
				forEach ($users as $user) {
					if ($user->idleTime >= 300000) {
						// if idle for at least 5 minutes
						$blob .= "<tab><highlight>{$user->name}<end> (RK{$user->dim}, {$user->country}, idle for " . $this->util->unixtime_to_readable($user->idleTime / 1000, false) . ")\n";
					} else {
						$blob .= "<tab><highlight>{$user->name}<end> (RK{$user->dim}, {$user->country})\n";
					}
				}
			}
			$blob .= "\n\nProvided by " . $this->text->make_chatcmd("AOSpeak.com", "/start http://www.aospeak.com");
			$msg = $this->text->make_blob("$title ($count)", $blob);
		}

		$sendto->reply($msg);
	}
}

?>
