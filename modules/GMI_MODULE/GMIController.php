<?php

namespace Budabot\User\Modules;

use stdClass;

/**
 * Authors:
 *  - Tyrence
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'gmi',
 *		accessLevel = 'all',
 *		description = 'Search GMI for buy and sell orders',
 *		help        = 'gmi.txt'
 *	)
 */
class GMIController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $db;

	/** @Inject */
	public $util;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $http;
	
	const GMI_URL = "http://aogmi.com/";

	/** @Setup */
	public function setup() {
		
	}
	
	/**
	 * @HandlesCommand("gmi")
	 * @Matches("/^gmi (\d+)$/i")
	 */
	public function gmiShowCommand($message, $channel, $sender, $sendto, $args) {
		$clusterId = $args[1];
		
		$msg = $this->lookupGmiItem($clusterId);

		$sendto->reply($msg);
	}
	
	public function lookupGmiItem($clusterId) {
		$response = $this->http->get(self::GMI_URL . 'Item/' . $clusterId)->waitAndReturnResponse();
		
		if (isset($response->error)) {
			$msg = $response->error;
		} else {
			$results = $this->parseItem($response->body);
			
			$itemName = $results->info->name;
			
			$countSellOrders = count($results->sell);
			$blob .= "\n<header2>Sell Orders ($countSellOrders)<end>\n\n";
			if ($countSellOrders == 0) {
				$blob .= "<i>No sell orders found<i>\n\n";
			} else {
				forEach ($results->sell as $item) {
					$blob .= $item->price . " (QL$item->ql) " . $this->text->make_chatcmd($item->seller, "/tell $item->seller") . "\n\n";
				}
			}

			$countBuyOrders = count($results->buy);
			$blob .= "\n<header2>Buy Orders ($countBuyOrders)<end>\n\n";
			if ($countBuyOrders == 0) {
				$blob .= "<i>No buy orders found<i>\n\n";
			} else {
				forEach ($results->buy as $item) {
					if ($item->minQl == $item->maxQl) {
						$ql = $item->maxQl;
					} else {
						$ql = $item->minQl . '-' . $item->maxQl;
					}
					
					$blob .= $item->price . " (QL$ql) " . $this->text->make_chatcmd($item->buyer, "/tell $item->buyer") . "\n\n";
				}
			}
			$blob .= "\nPowered by " . $this->text->make_chatcmd("aogmi.com", "/start http://aogmi.com/");
			
			$msg = $this->text->make_blob("GMI: $itemName ($countSellOrders, $countBuyOrders)", $blob);
		}
		
		return $msg;
	}
	
	/**
	 * @HandlesCommand("gmi")
	 * @Matches("/^gmi (.+)$/i")
	 */
	public function gmiSearchCommand($message, $channel, $sender, $sendto, $args) {
		$search = $args[1];

		$response = $this->http->get(self::GMI_URL . 'search/' . urlencode($search))->waitAndReturnResponse();
		
		if (isset($response->error)) {
			$msg = $response->error;
		} else {
			$results = $this->parseSearch($response->body);
			$count = count($results);
			
			if ($count == 0) {
				$msg = "Could not find any items on GMI matching your search criteria.";
			} else if ($count == 1) {
				$msg = $this->lookupGmiItem($results[0]->cluster_id);
			} else {
				$blob = '';
				forEach ($results as $item) {
					$blob .= $this->text->make_image($item->icon) . "\n";
					$blob .= $this->text->make_chatcmd($item->name, "/tell <myname> gmi $item->cluster_id") . "\n\n<pagebreak>";
				}
				$blob .= "\nPowered by " . $this->text->make_chatcmd("aogmi.com", "/start http://aogmi.com/");

				$msg = $this->text->make_blob("GMI Search Results ($count)", $blob);
			}
		}

		$sendto->reply($msg);
	}
	
	public function parseSearch($input) {
		$lines = explode("\n", $input);
		$results = array();
		$item = new stdClass;
		
		forEach ($lines as $line) {
			if (preg_match('/<img src="http:\/\/aomarket\.funcom\.com\/staticLIVE\/images\/icons\/(\d+)\.png" alt="" \/>/i', $line, $arr)) {
				$item->icon = $arr[1];
			} else if (preg_match('/<a href="\/Item\/(\d+)">([^<]+)<\/a>/i', $line, $arr)) {
				$item->cluster_id = $arr[1];
				$item->name = $arr[2];
				$results []= $item;
				$item = new stdClass;
			}
		}
		
		return $results;
	}
	
	public function parseItem($input) {
		$input = str_replace("</td>\r\n                                <td>", "<td>", $input);
		$lines = explode("\n", $input);
		$results = new stdClass;
		$results->sell = array();
		$results->buy = array();
		$type = '';
		
		forEach ($lines as $line) {
			if (preg_match('/<table id="sellOrders">/i', $line)) {
				$type = 'sell';
			} else if (preg_match('/<table id="buyOrders">/i', $line)) {
				$type = 'buy';
			} else if ($type == 'sell' && preg_match('/<td>(\d+)<td>([^<]+)<td>([^<]+)<td>(\d+)<td>([^<]+)<\/td>/i', $line, $arr)) {
				$item = new stdClass;
				$item->price = $arr[2];
				$item->ql = $arr[1];
				$item->seller = $arr[3];
				$results->sell []= $item;
			} else if ($type == 'buy' && preg_match('/<td>(\d+)-(\d+)<td>([^<]+)<td>([^<]+)<td>(\d+)<td>([^<]+)<\/td>/i', $line, $arr)) {
				$item = new stdClass;
				$item->price = $arr[3];
				$item->minQl = $arr[1];
				$item->maxQl = $arr[2];
				$item->buyer = $arr[4];
				$results->buy []= $item;
			} else if (preg_match('/<span class="header">([^<]+)<\/span>/i', $line, $arr)) {
				$results->info->name = $arr[1];
			}
		}
		
		return $results;
	}
}