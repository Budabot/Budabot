<?php
/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'findorg', 
 *		accessLevel = 'all', 
 *		description = "Find orgs by name", 
 *		help        = 'findorg.txt'
 *	)
 */
class FindOrgController {

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
	
	/** @Inject */
	public $http;

	/**
	 * @HandlesCommand("findorg")
	 * @Matches("/^findorg (.+) (\d)$/i")
	 * @Matches("/^findorg (.+)$/i")
	 */
	public function findOrgCommand($message, $channel, $sender, $sendto, $args) {
		$search = $args[1];

		$dimension = $this->chatBot->vars['dimension'];
		if (count($args) == 3) {
			$dimension = $args[2];
		}
		
		$orgs = $this->lookupOrg($search, $dimension);
		$count = count($orgs);

		if ($count > 0) {
			$blob = '';
			forEach ($orgs as $row) {
				$whoisorg = $this->text->make_chatcmd('Whoisorg', "/tell <myname> whoisorg {$row->id} $dimension");
				if ($row->dimension == $this->chatBot->vars['dimension']) {
					$orglist = $this->text->make_chatcmd('Orglist', "/tell <myname> orglist {$row->id}");
					$orgranks = $this->text->make_chatcmd('Orgranks', "/tell <myname> orgranks {$row->id}");
					$orgmembers = $this->text->make_chatcmd('Orgmembers', "/tell <myname> orgmembers {$row->id}");
					$tower_attacks = $this->text->make_chatcmd('Tower Attacks', "/tell <myname> attacks org {$row->name}");
					$tower_victories = $this->text->make_chatcmd('Tower Victories', "/tell <myname> victory org {$row->name}");
					$blob .= "{$row->name} ({$row->id}) [$whoisorg] [$orglist] [$orgranks] [$orgmembers] [$tower_attacks] [$tower_victories]\n";
				} else {
					$blob .= "{$row->name} ({$row->id}) [$whoisorg]\n";
				}
			}

			$msg = $this->text->make_blob("Org Search Results for '{$search}' on RK{$dimension} ($count)", $blob);
		} else {
			$msg = "No matches found.";
		}
		$sendto->reply($msg);
	}
	
	public function lookupOrg($search, $dimension, $limit = 50) {
		$search = '%' . $search . '%';
		$url = "http://people.anarchy-online.com/people/lookup/orgs.html";
		
		$response = $this->http->get($url)->withQueryParams(array('l' => $search))->waitAndReturnResponse();
		preg_match_all('|<a href="http://people.anarchy-online.com/org/stats/d/(\\d)/name/(\\d+)">([^<]+)</a>|s', $response->body, $arr, PREG_SET_ORDER);
		$orgs = array();
		forEach ($arr as $match) {
			if ($dimension === null || $match[1] == $dimension) {
				$obj = new stdClass;
				$obj->name = trim($match[3]);
				$obj->dimension = $match[1];
				$obj->id = $match[2];
				$orgs []= $obj;
				if (count($orgs) == $limit) {
					break;
				}
			}
		}
		return $orgs;
	}
}

