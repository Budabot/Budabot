<?php

namespace Budabot\User\Modules;

use stdClass;

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
 *		description = 'Find orgs by name', 
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
	 * @Matches("/^findorg (.+)$/i")
	 */
	public function findOrgCommand($message, $channel, $sender, $sendto, $args) {
		$search = $args[1];
		
		$orgs = $this->lookupOrg('%' . $search . '%');
		$count = count($orgs);

		if ($count > 0) {
			$blob = '';
			forEach ($orgs as $row) {
				$whoisorg = $this->text->make_chatcmd('Whoisorg', "/tell <myname> whoisorg {$row->id}");
				$orglist = $this->text->make_chatcmd('Orglist', "/tell <myname> orglist {$row->id}");
				$orgranks = $this->text->make_chatcmd('Orgranks', "/tell <myname> orgranks {$row->id}");
				$orgmembers = $this->text->make_chatcmd('Orgmembers', "/tell <myname> orgmembers {$row->id}");
				$blob .= "<{$row->faction}>{$row->name}<end> ({$row->id}) - {$row->numMembers} members [$orglist] [$whoisorg] [$orgranks] [$orgmembers]\n\n";
			}

			$msg = $this->text->make_blob("Org Search Results for '{$search}' ($count)", $blob);
		} else {
			$msg = "No matches found.";
		}
		$sendto->reply($msg);
	}
	
	public function lookupOrg($search, $limit = 50) {
		$url = "http://people.anarchy-online.com/people/lookup/orgs.html";
		$response = $this->http->get($url)->withQueryParams(array('l' => $search))->waitAndReturnResponse();
		
		$pattern = '@(<tr>|<tr class="lastRow">)
               <td align="left">
                 <a href="http://people.anarchy-online.com/org/stats/d/(\d+)/name/(\d+)">
                   ([^<]+)</a></td>
               <td align="right">(\d+)</td>
               <td align="right">(\d+)</td>
               <td align="left">([^<]+)</td>
               <td align="left">([^<]+)</td>
               <td align="left" class="dim">RK5</td>
             </tr>@s';
		
		preg_match_all($pattern, $response->body, $arr, PREG_SET_ORDER);
		$orgs = array();
		forEach ($arr as $match) {
			$obj = new stdClass;
			$obj->server = $match[2];
			$obj->name = trim($match[4]);
			$obj->id = $match[3];
			$obj->numMembers = $match[5];
			$obj->faction = $match[7];
			$orgs []= $obj;

			if (count($orgs) == $limit) {
				break;
			}
		}
		return $orgs;
	}
}

