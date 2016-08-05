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
 *		command     = 'links',
 *		accessLevel = 'guild',
 *		description = 'Displays, adds, or removes links from the org link list',
 *		help        = 'links.txt'
 *	)
 */
class LinksController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $settingManager;

	/** @Inject */
	public $text;

	/** @Inject */
	public $accessManager;
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "links");
		$this->settingManager->add($this->moduleName, 'showfullurls', 'Enable full urls in the link list output', 'edit', "options", 0, "true;false", "1;0");
	}
	
	/**
	 * @HandlesCommand("links")
	 * @Matches("/^links$/i")
	 */
	public function linksListCommand($message, $channel, $sender, $sendto, $args) {
		$blob = '';

		$sql = "SELECT * FROM links ORDER BY name ASC";
		$data = $this->db->query($sql);
		forEach ($data as $row) {
			$remove = $this->text->makeChatcmd('Remove', "/tell <myname> <symbol>links rem $row->id");
			if ($this->settingManager->get('showfullurls') == 1) {
				$website = $this->text->makeChatcmd($row->website, "/start $row->website");
			} else {
				$website = $this->text->makeChatcmd('[Link]', "/start $row->website");
			}
			$blob .= "$website <highlight>$row->comments<end> [$row->name] $remove\n";
		}

		if (count($data) == 0) {
			$msg = "No links found.";
		} else {
			$msg = $this->text->makeBlob('Links', $blob);
		}

		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("links")
	 * @Matches("/^links add ([^ ]+) (.+)$/i")
	 */
	public function linksAddCommand($message, $channel, $sender, $sendto, $args) {
		$website = htmlspecialchars($args[1]);
		$comments = $args[2];

		$this->db->exec("INSERT INTO links (`name`, `website`, `comments`, `dt`) VALUES (?, ?, ?, ?)", $sender, $website, $comments, time());
		$msg = "Link added successfully.";
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("links")
	 * @Matches("/^links rem ([0-9]+)$/i")
	 */
	public function linksRemoveCommand($message, $channel, $sender, $sendto, $args) {
		$id = $args[1];

		$obj = $this->db->queryRow("SELECT * FROM links WHERE id = ?", $id);
		if (empty($obj)) {
			$msg = "Link with ID <highlight>$id<end> could not be found.";
		} else if ($obj->name == $sender || $this->accessManager->compareCharacterAccessLevels($sender, $obj->name) > 0) {
			$this->db->exec("DELETE FROM links WHERE id = ?", $id);
			$msg = "Link with ID <highlight>$id<end> deleted successfully.";
		} else {
			$msg = "You do not have permission to delete link with ID <highlight>$id<end>";
		}
		$sendto->reply($msg);
	}
}

?>
