<?php
/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'waitlist',
 *		accessLevel = 'all',
 *		description = 'Show/Set the waitlist',
 *		help        = 'waitlist.txt'
 *	)
 */
class WaitlistController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $db;

	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $text;
	
	private $waitlist;
	
	/**
	 * @HandlesCommand("waitlist")
	 * @Matches("/^waitlist next$/i")
	 */
	public function waitlistNextCommand($message, $channel, $sender, $sendto, $args) {
		if (count($this->waitlist[$sender]) == 0) {
			$msg = "There is no one on your waitlist!";
			$sendto->reply($msg);
			return;
		}

		$name = array_shift(array_keys($this->waitlist[$sender]));
		unset($this->waitlist[$sender][$name]);
		$this->waitlist[$sender][$name] = true;
		$this->chatBot->sendTell("<highlight>$sender waitlist<end>: You can come now!", $name);

		$msg = "<highlight>$name<end> has been called to come now.";
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("waitlist")
	 * @Matches("/^waitlist add (.+)$/i")
	 */
	public function waitlistAddCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));

		if (isset($this->waitlist[$sender][$name])) {
			$msg = "<highlight>$name<end> is already on your waitlist!";
			$sendto->reply($msg);
			return;
		}

		$this->waitlist[$sender][$name] = true;
		$this->chatBot->sendTell("You have been added to the waitlist of <highlight>$sender<end>.", $name);

		$msg = "<highlight>$name<end> has been added to your waitlist.";
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("waitlist")
	 * @Matches("/^waitlist (rem all|clear)$/i")
	 */
	public function waitlistClearCommand($message, $channel, $sender, $sendto, $args) {
		if (count($this->waitlist[$sender]) == 0) {
			$msg = "There is no one on your waitlist!";
			$sendto->reply($msg);
			return;
		}

		unset($this->waitlist[$sender]);

		$msg = "Your waitlist has been cleared.";
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("waitlist")
	 * @Matches("/^waitlist rem (.+)$/i")
	 */
	public function waitlistRemoveCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));

		if (!isset($this->waitlist[$sender][$name])) {
			$msg = "<highlight>$name<end> is not on your waitlist!";
			$sendto->reply($msg);
			return;
		}

		unset($this->waitlist[$sender][$name]);
		$msg = "You have been removed from {$sendto}'s waitlist.";
		$this->chatBot->sendTell($msg, $name);

		$msg = "<highlight>$name<end> has been removed from your waitlist.";
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("waitlist")
	 * @Matches("/^waitlist shuffle$/i")
	 */
	public function waitlistShuffleCommand($message, $channel, $sender, $sendto, $args) {
		if (count($this->waitlist[$sender]) == 0) {
			$msg = "There is no one on your waitlist!";
			$sendto->reply($msg);
			return;
		}

		$keys = array_keys($this->waitlist[$sender]);
		shuffle($keys);
		$random = array();
		forEach ($keys as $key) {
			$random[$key] = $this->waitlist[$sender][$key];
		}
		$this->waitlist[$sender] = $random;

		$count = 0;
		$blob = '';
		forEach ($this->waitlist[$sender] as $name => $value) {
			$count++;
			$blob .= "{$count}. $name \n";
		}

		$msg = "Your waitlist has been shuffled. " . $this->text->make_blob("Waitlist for $sender ($count)", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("waitlist")
	 * @Matches("/^waitlist$/i")
	 * @Matches("/^waitlist ([a-z0-9-]+)$/i")
	 */
	public function waitlistCommand($message, $channel, $sender, $sendto, $args) {
		if (count($args) == 2) {
			$char = ucfirst(strtolower($args[1]));
		} else {
			$char = $sender;
		}

		if (count($this->waitlist[$char]) == 0) {
			$msg = "<highlight>$char<end> doesn't have a waitlist!";
			$sendto->reply($msg);
			return;
		}

		$count = 0;
		$blob = '';
		forEach ($this->waitlist[$char] as $name => $value) {
			$count++;
			$blob .= "{$count}. $name \n";
		}

		$msg = $this->text->make_blob("Waitlist for $char ($count)", $blob);

		$sendto->reply($msg);
	}
}

?>
