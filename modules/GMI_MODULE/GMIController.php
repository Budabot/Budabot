<?php

namespace Budabot\User\Modules;

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
	
	const GMI_URL = "http://kdj.dk/market/api.php";

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
		$params = "p[]=item&p[]=" . $clusterId;
		
		$response = $this->http->get(self::GMI_URL . '?' . $params)->waitAndReturnResponse();
		
		if (isset($response->error)) {
			$msg = $response->error;
		} else {
			$results = json_decode($response->body);
			
			// remove null orders
			if (empty($results->sell[0]->sellOrderId)) {
				array_shift($results->sell);
			}
			if (empty($results->buy[0]->buyOrderId)) {
				array_shift($results->buy);
			}
			
			$itemName = $results->info->name;
			
			$blob = "Item: <highlight>$itemName<end>\n\n";

			$countSellOrders = count($results->sell);
			$blob .= "\n<header2>Sell Orders ($countSellOrders)<end>\n\n";
			if ($countSellOrders == 0) {
				$blob .= "<i>No sell orders found<i>\n\n";
			} else {
				forEach ($results->sell as $item) {
					$blob .= number_format($item->price) . " cr. (QL$item->ql) " . $this->text->make_chatcmd($item->seller, "/tell $item->seller") . "\n\n";
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
					
					$blob .= number_format($item->price) . " cr. (QL$ql) " . $this->text->make_chatcmd($item->buyer, "/tell $item->buyer") . "\n\n";
				}
			}
			$blob .= "\nPowered by " . $this->text->make_chatcmd("aogmi.com", "/start http://aogmi.com/");
			
			$msg = $this->text->make_blob("GMI Search Results ($countSellOrders, $countBuyOrders)", $blob);
		}
		
		return $msg;
	}
	
	/**
	 * @HandlesCommand("gmi")
	 * @Matches("/^gmi (.+)$/i")
	 */
	public function gmiSearchCommand($message, $channel, $sender, $sendto, $args) {
		$search = $args[1];
		
		$params = "p[]=searchAll&p[]=" . urlencode($search);
		
		$response = $this->http->get(self::GMI_URL . '?' . $params)->waitAndReturnResponse();
		
		if (isset($response->error)) {
			$msg = $response->error;
		} else {
			$results = json_decode($response->body);
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
	
	public function formatCredits($credits) {
		
	}
}