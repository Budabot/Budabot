<?php

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
	 *  - rally (10.9 30 y 20 <playfield id>)
	 *  - rally 10.9 30 y 20 <playfield id>
	 *
	 * @HandlesCommand("rally")
	 * @Matches("/^rally \(?([0-9\.]+) ([0-9\.]+) y ([0-9\.]+) ([0-9]+)\)?$/i")
	 */
	public function rallySet1Command($message, $channel, $sender, $sendto, $args) {
		$x_coords = $args[1];
		$y_coords = $args[2];
		$playfield_id = $args[4];
		$name = $playfield_id;

		$playfield = $this->playfieldController->get_playfield_by_id($playfield_id);
		if ($playfield !== null) {
			$name = $playfield->short_name;
		}
		$this->set($name, $playfield_id, $x_coords, $y_coords);

		$this->replyCurrentRally($channel, $sendto);
	}

	/**
	 * This command handler sets rally waypoint, using following example syntaxes:
	 *  - rally 10.9 x 30 x <playfield id/name>
	 *  - rally 10.9 . 30 . <playfield id/name>
	 *  - rally 10.9, 30, <playfield id/name>
	 *  - etc...
	 *
	 * @HandlesCommand("rally")
	 * @Matches("/^rally ([0-9\.]+)([x,. ]+)([0-9\.]+)([x,. ]+)(.+)$/i")
	 */
	public function rallySet2Command($message, $channel, $sender, $sendto, $args) {
		$x_coords = $args[1];
		$y_coords = $args[3];

		if (is_numeric($args[5])) {
			$playfield_id = $args[5];
			$playfield_name = $playfield_id;

			$playfield = $this->playfieldController->get_playfield_by_id($playfield_id);
			if ($playfield !== null) {
				$playfield_name = $playfield->short_name;
			}
		} else {
			$playfield_name = $args[5];
			$playfield = $this->playfieldController->get_playfield_by_name($playfield_name);
			if ($playfield === null) {
				$sendto->reply("Could not find playfield '$playfield_name'");
				return;
			}
			$playfield_id = $playfield->id;
		}
		$this->set($playfield_name, $playfield_id, $x_coords, $y_coords);

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

	private function set($name, $playfield_id, $x_coords, $y_coords) {
		$link = $this->text->make_chatcmd("Rally: {$x_coords}x{$y_coords} {$name}", "/waypoint {$x_coords} {$y_coords} {$playfield_id}");
		$blob = "Click here to use rally: $link";
		$rally = $this->text->make_blob("Rally: {$x_coords}x{$y_coords} {$name}", $blob);

		$this->settingManager->save("rally", $rally);

		return $rally;
	}

	private function get() {
		return $this->settingManager->get("rally");
	}

	private function clear() {
		$this->settingManager->save("rally", '');
	}
	
	private function replyCurrentRally($channel, $sendto) {
		$rally = $this->get();
		if ('' == $rally) {
			$msg = "No rally set.";
			$sendto->reply($msg);
			return;
		}
		$sendto->reply($rally);

		// send message 2 more times (3 total) if used in private channel
		if ($channel == "priv") {
			$sendto->reply($rally);
			$sendto->reply($rally);
		}
	}
}

?>
