<?php

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
	 * @Command("cmdsearch")
	 * @AccessLevel("all")
	 * @Description("Find commands based on key words")
	 * @Matches("/^cmdsearch (.*)/i")
	 * @DefaultStatus("1")
	 */
	public function searchCommand($message, $channel, $sender, $sendto, $arr) {
		$this->searchWords = explode(" ", $arr[1]);

		$sqlquery = "SELECT DISTINCT module, cmd, help, description FROM cmdcfg_<myname> WHERE status = 1";
		$data = $this->db->query($sqlquery);

		$results = array_filter($data, array($this, 'exactFilter'));
		$exactMatch = !empty($results);

		if (!$exactMatch)
		{
			// oops! no results, lets try to find similar commands
			forEach ($data as $row) {
				$keywords = explode(' ', $row->description);
				array_push($keywords, $row->cmd);
				$keywords = array_unique($keywords);
				$row->distance = 0;
				forEach($this->searchWords as $searchWord) {
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

		$access = false;
		if ($this->accessLevel->checkAccess($sender, 'mod')) {
			$access = true;
		}

		$msg = $this->view->render($results, $access, $exactMatch);

		$this->chatBot->send($msg, $sendto);

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