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
		$this->settingManager->add($this->moduleName, "quote_stat_count", "Number of users shown in stats", "edit", "number", "10");
	}

	/**
	 * @HandlesCommand("quote")
	 * @Matches("/^quote add (.+)$/si")
	 */
	public function quoteAddCommand($message, $channel, $sender, $sendto, $args) {
		$quoteMSG = trim($args[1]);
		$row = $this->db->queryRow("SELECT * FROM `quote` WHERE `What` LIKE ?", $quoteMSG);
		if ($row !== null) {
			$msg = "This quote has already been added as quote <highlight>$row->id<end>.";
		} else {
			if (strlen($quoteMSG) > 1000) {
				$msg = "This quote is too big.";
			} else {
				$quoteWHO = $sender;

				// nextId = maxId + 1
				$id = $this->getMaxId() + 1;
				
				if (preg_match("/^(\(\d\d:\d\d\) )?To \[([a-z0-9-]+)\]:/i", $quoteMSG, $arr)) {
					//To [Person]: message
					$quoteOfWHO = $arr[2];
				} else if (preg_match("/^(\(\d\d:\d\d\) )?\[([a-z0-9-]+)\]:/i", $quoteMSG, $arr)) {
					//[Person]: message
					$quoteOfWHO = $arr[2];
				} else if (preg_match("/^(\(\d\d:\d\d\) )?\[[^\]]+\] ([a-z0-9-]+):/i", $quoteMSG, $arr)) {
					//[Neu. OOC] Lucier: message
					$quoteOfWHO = $arr[2];
				} else if (preg_match("/^(\(\d\d:\d\d\) )?([a-z0-9-]+) shouts:/i", $quoteMSG, $arr)) {
					//Lucier shouts: message
					$quoteOfWHO = $arr[2];
				} else if (preg_match("/^(\(\d\d:\d\d\) )?([a-z0-9-]+) whispers:/i", $quoteMSG, $arr)) {
					//Lucier whispers: message
					$quoteOfWHO = $arr[2];
				} else if (preg_match("/^(\(\d\d:\d\d\) )?([a-z0-9-]+):/i", $quoteMSG, $arr)) {
					//Lucier: message
					$quoteOfWHO = $arr[2];
				} else {
					$quoteOfWHO = $sender;
				}

				$this->db->exec("INSERT INTO `quote` (`id`, `Who`, `OfWho`, `When`, `What`) VALUES (?, ?, ?, ?, ?)", $id, $quoteWHO, $quoteOfWHO, time(), $quoteMSG);
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
			$quoteWHO = $row->Who;
			$quoteOfWHO = $row->OfWho;
			$quoteDATE = $row->When;
			$quoteMSG = $row->What;

			//only author or admin can delete.
			if (($quoteWHO == $sender) || $this->accessManager->checkAccess($sender, 'moderator')) {
				$this->db->exec("DELETE FROM `quote` WHERE `id` = ?", $id);
				$msg = "This quote has been deleted.";
			} else {
				$msg = "Only a moderator or $quoteWHO can delete this quote.";
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
		$data = $this->db->query("SELECT * FROM `quote` WHERE `Who` LIKE ?", $searchParam);
		forEach ($data as $row) {
			$list .= $this->text->make_chatcmd($row->id, "/tell <myname> quote $row->id") . ", ";
		}
		if ($list) {
			$msg .= "<tab>Quotes posted by <highlight>$search<end>: ";
			$msg .= substr($list, 0, strlen($list) - 2);
		}

		// Search for victim:
		$list = "";
		$data = $this->db->query("SELECT * FROM `quote` WHERE `OfWho` LIKE ?", $searchParam);
		forEach ($data as $row) {
			$list .= $this->text->make_chatcmd($row->id, "/tell <myname> quote $row->id") . ", ";
		}
		if ($list) {
			if ($msg) {
				$msg .="\n\n";
			}
			$msg .= "<tab>Quotes <highlight>$search<end> said: ";
			$msg .= substr($list, 0, strlen($list) - 2);
		}

		// Search inside quotes:
		$list = "";
		$data = $this->db->query("SELECT * FROM `quote` WHERE `OfWho` NOT LIKE ? AND `What` LIKE ?", $searchParam, $searchParam);
		forEach ($data as $row) {
			$list .= $this->text->make_chatcmd($row->id, "/tell <myname> quote $row->id") . ", ";
		}
		if ($list) {
			if ($msg) {
				$msg .="\n\n";
			}
			$msg .= "<tab>Quotes that contain '<highlight>$search<end>': ";
			$msg .= substr($list, 0, strlen($list) - 2);
		}

		if ($msg) {
			$msg = $this->text->make_blob("Results for: '$search'", $msg);
		} else {
			$msg = "Could not find any matches for this search.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("quote")
	 * @Matches("/^quote stats$/i")
	 */
	public function quoteStatsCommand($message, $channel, $sender, $sendto, $args) {
		$top = $this->settingManager->get("quote_stat_count");

		//$quoters = setup a list of who quoted the most
		$data = $this->db->query("SELECT * FROM `quote` ORDER BY `Who`");
		$count = count($data);
		$quoters = array();
		forEach ($data as $row) {
			$quoters[$row->Who]++;
		}
		arsort($quoters);

		//$victims = setup a list of who was quoted the most
		$data = $this->db->query("SELECT * FROM `quote` ORDER BY `OfWho`");
		$victims = array();
		forEach ($data as $row) {
			$victims[$row->OfWho]++;
		}
		arsort($victims);

		$blob = "<highlight>Top $top Quoters:<end> (".count($quoters)." total)\n";
		$listnum = 0;
		forEach ($quoters as $key => $val) {
			$listnum++;
			$blob .= "<tab>$listnum) ";
			$blob .= $this->text->make_chatcmd($key, "/tell <myname> quote search $key");
			$blob .= ": <highlight>$val<end> " . number_format((100 * ($val / $count)), 0) . "%\n";
			if ($listnum >= $top) {
				break;
			}
		}

		$blob .= "\n<highlight>Top $top Quoted:<end> (".count($victims)." total)\n";
		$listnum = 0;
		forEach ($victims as $key => $val) {
			$listnum++;
			$blob .= "<tab>$listnum) ".
				$this->text->make_chatcmd($key, "/tell <myname> quote search $key") .
				": <highlight>$val<end> " . number_format((100 * ($val / $count)), 0) . "%\n";
			if ($listnum >= $top) {
				break;
			}
		}

		$msg = $this->text->make_blob("Quote stats", $blob);
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
		
		$quoteWHO = $row->Who;
		$quoteOfWHO = $row->OfWho;
		$quoteDATE = $row->When;
		$quoteMSG = $row->What;

		$msg = "<tab>ID: (<highlight>$id<end> of $count)\n";
		$msg .= "<tab>Poster: <highlight>$quoteWHO<end>\n";
		$msg .= "<tab>Quoting: <highlight>$quoteOfWHO<end>\n";
		$msg .= "<tab>Date: <highlight>" . $this->util->date($quoteDATE) . "<end>\n\n";

		$msg .= "<tab>Quotes posted by <highlight>$quoteWHO<end>: ";
		$data = $this->db->query("SELECT * FROM `quote` WHERE `Who` = ?", $quoteWHO);
		$list = "";
		forEach ($data as $row) {
			$list .= $this->text->make_chatcmd($row->id, "/tell <myname> quote $row->id") . ", ";
		}
		$msg .= substr($list, 0, strlen($list) - 2) . "\n\n";

		$msg .="<tab>Quotes <highlight>$quoteOfWHO<end> said: ";
		$data = $this->db->query("SELECT * FROM `quote` WHERE `OfWho` = ?", $quoteOfWHO);
		$list = "";
		forEach ($data as $row) {
			$list .= $this->text->make_chatcmd($row->id, "/tell <myname> quote $row->id") . ", ";
		}
		$msg .= substr($list, 0, strlen($list) - 2);

		return $this->text->make_blob("Quote", $msg).': "'.$quoteMSG.'"';
	}
}
