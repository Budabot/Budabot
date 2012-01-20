<?php

class Rally {
	/** @Inject */
	public $setting;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $playfields;
	
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
	 * @Command("rally")
	 * @AccessLevel("all")
	 * @Description("Shows or sets the rally waypoint")
	 * @Help("rally.txt")
	 */
	public function rallyCommand($message, $channel, $sender, $sendto) {
		if (preg_match("/^rally$/i", $message)) {
			// skip to end
		} else if (preg_match("/^rally clear$/i", $message)) {
			$this->clear();
			$msg = "Rally has been cleared.";
			$sendto->reply($msg);
			return;
		} else if (preg_match("/^rally \\(?([0-9\\.]+) ([0-9\\.]+) y ([0-9\\.]+) ([0-9]+)\\)?$/i", $message, $arr)) {
			$x_coords = $arr[1];
			$y_coords = $arr[2];
			$playfield_id = $arr[4];
			$name = $playfield_id;

			$playfield = $this->playfields->get_playfield_by_id($playfield_id);
			if ($playfield !== null) {
				$name = $playfield->short_name;
			}
			$this->set($name, $playfield_id, $x_coords, $y_coords);
		} else if (preg_match("/^rally ([0-9\\.]+)([x,. ]+)([0-9\\.]+)([x,. ]+)([0-9]+)$/i", $message, $arr)) {
			$x_coords = $arr[1];
			$y_coords = $arr[3];
			$playfield_id = $arr[5];
			$name = $playfield_id;
			
			$playfield = $this->playfields->get_playfield_by_id($playfield_id);
			if ($playfield !== null) {
				$name = $playfield->short_name;
			}
			$this->set($name, $playfield_id, $x_coords, $y_coords);
		} else if (preg_match("/^rally ([0-9\\.]+)([x,. ]+)([0-9\\.]+)([x,. ]+)(.+)$/i", $message, $arr)) {
			$x_coords = $arr[1];
			$y_coords = $arr[3];
			$playfield_name = $arr[5];
			
			$playfield = $this->playfields->get_playfield_by_name($playfield_name);
			if ($playfield === null) {
				$sendto->reply("Could not find playfield '$playfield_name'");
				return;
			}
			
			$this->set($playfield_name, $playfield->id, $x_coords, $y_coords);
		} else {
			return false;
		}

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

	public function set($name, $playfield_id, $x_coords, $y_coords) {
		$link = $this->text->make_chatcmd("Rally: {$x_coords}x{$y_coords} {$name}", "/waypoint {$x_coords} {$y_coords} {$playfield_id}");
		$blob = "Click here to use rally: $link";
		$rally = $this->text->make_blob("Rally: {$x_coords}x{$y_coords} {$name}", $blob);
		
		$this->setting->save("rally", $rally);
		
		return $rally;
	}
	
	public function get() {
		return $this->setting->get("rally");
	}
	
	public function clear() {
		$this->setting->save("rally", '');
	}
}

?>