<?php

/**
 * Commands this controller contains:
 *	@DefineCommand(
 *		command       = 'cmdsearch',
 *      alias         = 'searchcmd',
 *		accessLevel   = 'all',
 *		description   = 'Finds commands based on key words',
 *		defaultStatus = 1,
 *		help          = 'cmdsearch.txt'
 *	)
 */
class CommandSearchController {

	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $db;

	/** @Inject */
	public $accessLevel;

	/** @Inject("CommandSearchView") */
	public $view;

	private $searchWords;

	/**
	 * @HandlesCommand("cmdsearch")
	 * @Matches("/^cmdsearch (.*)/i")
	 */
	public function searchCommand($message, $channel, $sender, $sendto, $arr) {
		$this->searchWords = explode(" ", $arr[1]);
		
		$access = false;
		if ($this->accessLevel->checkAccess($sender, 'mod')) {
			$access = true;
		}
		
		// if a mod or higher, show all commands, not just enabled commands
		if ($access) {
			$sqlquery = "SELECT DISTINCT module, cmd, help, description FROM cmdcfg_<myname>";
		} else {
			$sqlquery = "SELECT DISTINCT module, cmd, help, description FROM cmdcfg_<myname> WHERE status = 1";
		}
		$data = $this->db->query($sqlquery);

		$results = array_filter($data, array($this, 'exactFilter'));
		$exactMatch = !empty($results);

		if (!$exactMatch) {
			// oops! no results, lets try to find similar commands
			forEach ($data as $row) {
				$keywords = explode(' ', $row->description);
				array_push($keywords, $row->cmd);
				$keywords = array_unique($keywords);
				$row->distance = 0;
				forEach ($this->searchWords as $searchWord) {
					$distance = 9999;
					forEach ($keywords as $keyword) {
						$distance = min($distance, levenshtein($keyword, $searchWord));
					}
					$row->distance += $distance;
				}
			}
			$results = $data;
			usort($results, array($this, 'sortByDistance'));
			$results = array_slice($results, 0, 5);
		}

		$msg = $this->view->render($results, $access, $exactMatch);

		$sendto->reply($msg);

		return true;
	}

	public function exactFilter($row) {
		forEach ($this->searchWords as $word) {
			if (false === stripos($row->cmd, $word) && false === stripos($row->description, $word)) {
				return false;
			}
		}
		return true;
	}

	public function sortByDistance($row1, $row2) {
		$d1 = $row1->distance;
		$d2 = $row2->distance;
		if ($d1 == $d2) {
			return 0;
		}
		return ($d1 < $d2) ? -1 : 1;
	}
}
