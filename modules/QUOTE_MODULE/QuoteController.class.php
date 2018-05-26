<?php

namespace Budabot\User\Modules;

/**
 * Authors: 
 *	- Lucier (RK1)
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'quote', 
 *		accessLevel = 'all', 
 *		description = 'Add/Remove/View Quotes', 
 *		help        = 'quote.txt'
 *	)
 */
class QuoteController {

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
	public $accessManager;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "quote");
	}

	/**
	 * @HandlesCommand("quote")
	 * @Matches("/^quote add (.+)$/si")
	 */
	public function quoteAddCommand($message, $channel, $sender, $sendto, $args) {
		$quoteMSG = trim($args[1]);
		$row = $this->db->queryRow("SELECT * FROM `quote` WHERE `msg` LIKE ?", $quoteMSG);
		if ($row !== null) {
			$msg = "This quote has already been added as quote <highlight>$row->id<end>.";
		} else {
			if (strlen($quoteMSG) > 1000) {
				$msg = "This quote is too big.";
			} else {
				$poster = $sender;

				// nextId = maxId + 1
				$id = $this->getMaxId() + 1;

				$this->db->exec("INSERT INTO `quote` (`id`, `poster`, `dt`, `msg`) VALUES (?, ?, ?, ?)", $id, $poster, time(), $quoteMSG);
				$msg = "Quote <highlight>$id<end> has been added.";
			}
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("quote")
	 * @Matches("/^quote (rem|del|remove|delete) ([0-9]+)$/i")
	 */
	public function quoteRemoveCommand($message, $channel, $sender, $sendto, $args) {
		$id = $args[2];
		$row = $this->db->queryRow("SELECT * FROM `quote` WHERE `id` = ?", $id);

		if ($row !== null) {
			$poster = $row->poster;

			//only author or admin can delete.
			if (($poster == $sender) || $this->accessManager->checkAccess($sender, 'moderator')) {
				$this->db->exec("DELETE FROM `quote` WHERE `id` = ?", $id);
				$msg = "This quote has been deleted.";
			} else {
				$msg = "Only a moderator or $poster can delete this quote.";
			}
			
			// re-number remaining quotes so there is no holes in the quote numbering
			// since sqlite doesn't support ORDER BY on UPDATEs, we have to manually update each row
			// in order to prevent duplicate key errors
			$maxId = $this->getMaxId();
			$currentId = $id + 1;
			
			while ($currentId <= $maxId ) {
				$this->db->exec("UPDATE `quote` SET `id` = `id` - 1 WHERE `id` = ?", $currentId);
				$currentId++;
			}
		} else {
			$msg = "Could not find this quote.  Already deleted?";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("quote")
	 * @Matches("/^quote search (.+)$/i")
	 */
	public function quoteSearchCommand($message, $channel, $sender, $sendto, $args) {
		$search = $args[1];
		$searchParam = '%' . $search . '%';

		// Search for poster:
		$list = "";
		$data = $this->db->query("SELECT * FROM `quote` WHERE `poster` LIKE ?", $searchParam);
		forEach ($data as $row) {
			$list .= $this->text->makeChatcmd($row->id, "/tell <myname> quote $row->id") . ", ";
		}
		if ($list) {
			$msg .= "<tab>Quotes posted by <highlight>$search<end>: ";
			$msg .= substr($list, 0, strlen($list) - 2);
		}

		// Search inside quotes:
		$list = "";
		$data = $this->db->query("SELECT * FROM `quote` WHERE `msg` LIKE ?", $searchParam);
		forEach ($data as $row) {
			$list .= $this->text->makeChatcmd($row->id, "/tell <myname> quote $row->id") . ", ";
		}
		if ($list) {
			if ($msg) {
				$msg .="\n\n";
			}
			$msg .= "<tab>Quotes that contain '<highlight>$search<end>': ";
			$msg .= substr($list, 0, strlen($list) - 2);
		}

		if ($msg) {
			$msg = $this->text->makeBlob("Results for: '$search'", $msg);
		} else {
			$msg = "Could not find any matches for this search.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("quote")
	 * @Matches("/^quote ([0-9]+)$/i")
	 */
	public function quoteShowCommand($message, $channel, $sender, $sendto, $args) {
		$id = $args[1];
		
		$result = $this->getQuoteInfo($id);
		
		if ($result == null) {
			$msg = "No quote found with ID <highlight>$id<end>.";
		} else {
			$msg = $result;
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("quote")
	 * @Matches("/^quote$/i")
	 */
	public function quoteShowRandomCommand($message, $channel, $sender, $sendto, $args) {
		// choose a random quote to show
		$result = $this->getQuoteInfo(null);
		
		if ($result == null) {
			$msg = "There are no quotes to show.";
		} else {
			$msg = $result;
		}
		$sendto->reply($msg);
	}
	
	public function getMaxId() {
		$row = $this->db->queryRow("SELECT COALESCE(MAX(id), 0) AS max_id FROM `quote`");
		return $row->max_id;
	}

	public function getQuoteInfo($id = null) {
		$count = $this->getMaxId();

		if ($count == 0) {
			return null;
		}

		if ($id == null) {
			$id = rand(1, $count);
		}

		$row = $this->db->queryRow("SELECT * FROM `quote` WHERE `id` = ?", $id);
		if ($row === null) {
			return null;
		}

		$poster = $row->poster;
		$quoteMSG = $row->msg;

		$msg = "ID: <highlight>$id<end> of $count\n";
		$msg .= "Poster: <highlight>$poster<end>\n";
		$msg .= "Date: <highlight>" . $this->util->date($row->dt) . "<end>\n";
		$msg .= "Quote: <highlight>$quoteMSG<end>\n\n";

		$msg .= "<header2>Quotes posted by <highlight>$poster<end>\n";
		$data = $this->db->query("SELECT * FROM `quote` WHERE `poster` = ?", $poster);
		$list = "";
		forEach ($data as $row) {
			$list .= $this->text->makeChatcmd($row->id, "/tell <myname> quote $row->id") . ", ";
		}
		$msg .= substr($list, 0, strlen($list) - 2);

		return $this->text->makeBlob("Quote", $msg).': "'.$quoteMSG.'"';
	}
}
