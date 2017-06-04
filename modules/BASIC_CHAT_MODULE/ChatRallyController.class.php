<?php

namespace Budabot\User\Modules;

/**
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'rally',
 *		accessLevel = 'all',
 *		description = 'Shows or sets the rally waypoint',
 *		help        = 'rally.txt'
 *	)
 */
class ChatRallyController {
	/** @Inject */
	public $settingManager;

	/** @Inject */
	public $text;

	/** @Inject */
	public $playfieldController;

	/** @Inject */
	public $chatBot;

	/**
	 * @Setting("rally")
	 * @Description("Rally waypoint for topic")
	 * @Visibility("noedit")
	 * @Type("text")
	 */
	public $defaultRally = "";

	/**
	 * This command handler ...
	 * @HandlesCommand("rally")
	 * @Matches("/^rally$/i")
	 */
	public function rallyCommand($message, $channel, $sender, $sendto, $args) {
		$this->replyCurrentRally($channel, $sendto);
	}
	
	/**
	 * This command handler ...
	 * @HandlesCommand("rally")
	 * @Matches("/^rally clear$/i")
	 */
	public function rallyClearCommand($message, $channel, $sender, $sendto, $args) {
		$this->clear();
		$msg = "Rally has been cleared.";
		$sendto->reply($msg);
	}

	/**
	 * This command handler sets rally waypoint, using following example syntaxes:
	 *  - rally 10.9 x 30 x <playfield id/name>
	 *  - rally 10.9 . 30 . <playfield id/name>
	 *  - rally 10.9, 30, <playfield id/name>
	 *  - etc...
	 *
	 * @HandlesCommand("rally")
	 * @Matches("/^rally ([0-9\.]+)([x,. ]+)([0-9\.]+)([x,. ]+)([^ ]+)$/i")
	 */
	public function rallySet2Command($message, $channel, $sender, $sendto, $args) {
		$xCoords = $args[1];
		$yCoords = $args[3];

		if (is_numeric($args[5])) {
			$playfieldId = $args[5];
			$playfieldName = $playfieldId;

			$playfield = $this->playfieldController->getPlayfieldById($playfieldId);
			if ($playfield !== null) {
				$playfieldName = $playfield->short_name;
			}
		} else {
			$playfieldName = $args[5];
			$playfield = $this->playfieldController->getPlayfieldByName($playfieldName);
			if ($playfield === null) {
				$sendto->reply("Could not find playfield '$playfieldName'");
				return;
			}
			$playfieldId = $playfield->id;
		}
		$this->set($playfieldName, $playfieldId, $xCoords, $yCoords);

		$this->replyCurrentRally($channel, $sendto);
	}
	
	/**
	 * This command handler sets rally waypoint, using following example syntaxes:
	 *  - rally (10.9 30 y 20 2434234)
	 *
	 * @HandlesCommand("rally")
	 * @Matches("/^rally (.+)$/i")
	 */
	public function rallySet1Command($message, $channel, $sender, $sendto, $args) {
		if (preg_match("/(\d+\.\d) (\d+\.\d) y \d+\.\d (\d+)/", $args[1], $matches)) {
			$xCoords = $matches[1];
			$yCoords = $matches[2];
			$playfieldId = $matches[3];
		} else {
			return false;
		}

		$name = $playfieldId;
		$playfield = $this->playfieldController->getPlayfieldById($playfieldId);
		if ($playfield !== null) {
			$name = $playfield->short_name;
		}
		$this->set($name, $playfieldId, $xCoords, $yCoords);

		$this->replyCurrentRally($channel, $sendto);
	}

	/**
	 * @Event("joinpriv")
	 * @Description("Sends rally to players joining the private channel")
	 */
	public function sendRally($eventObj) {
		$sender = $eventObj->sender;

		$rally = $this->get();
		if ('' != $rally) {
			$this->chatBot->sendTell($rally, $sender);
		}
	}

	public function set($name, $playfieldId, $xCoords, $yCoords) {
		$link = $this->text->makeChatcmd("Rally: {$xCoords}x{$yCoords} {$name}", "/waypoint {$xCoords} {$yCoords} {$playfieldId}");
		$blob = "Click here to use rally: $link";
		$rally = $this->text->makeBlob("Rally: {$xCoords}x{$yCoords} {$name}", $blob);

		$this->settingManager->save("rally", $rally);

		return $rally;
	}

	public function get() {
		return $this->settingManager->get("rally");
	}

	public function clear() {
		$this->settingManager->save("rally", '');
	}
	
	public function replyCurrentRally($channel, $sendto) {
		$rally = $this->get();
		if ('' == $rally) {
			$msg = "No rally set.";
			$sendto->reply($msg);
			return;
		}
		$sendto->reply($rally);
	}
}
