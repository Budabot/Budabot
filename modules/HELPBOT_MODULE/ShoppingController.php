<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'shop', 
 *		accessLevel = 'all', 
 *		description = 'Search for things that have been posted to the shopping channels', 
 *		help        = 'shop.txt'
 *	)
 */
class ShoppingController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $http;
	
	const URL = "http://budabot.jkbff.com/shopping/index.php";
	
	/** @Setup */
	public function setup() {
		
	}

	/**
	 * @HandlesCommand("shop")
	 * @Matches("/^shop (\d+) (\d+) (.+)$/i")
	 * @Matches("/^shop (\d+) (.+)$/i")
	 * @Matches("/^shop (.+)$/i")
	 */
	public function shopCommand($message, $channel, $sender, $sendto, $args) {
		if (count($args) == 4) {
			$minQl = $args[1];
			$maxQl = $args[2];
			$search = $args[3];
		} else if (count($args) == 3) {
			$minQl = $args[1];
			$maxQl = $args[1];
			$search = $args[2];
		} else {
			$minQl = 0;
			$maxQl = 500;
			$search = $args[1];
		}
		
		$dimension = $this->chatBot->vars['dimension'];

		$params = array(
			'server' => $dimension,
			'search' => $search,
			'minql' => $minQl,
			'maxql' => $maxQl,
			'bot' => 'budabot'
		);
		$response = $this->http->get(self::URL)->withQueryParams($params)->waitAndReturnResponse();
		if (!empty($response->error)) {
			$msg = "There was an error processing your request: " . $response->error;
		} else if (substr($response->body, 0, 5) == 'Error') {
			$msg = "There was an error processing your request: " . $response->body;
		} else {
			$results = json_decode($response->body);
			$count = count($results);
			if ($count == 0) {
				$msg = "No results were found matching your search criteria.";
			} else {
				$blob = '';
				forEach ($results as $result) {
					$senderLink = Text::make_chatcmd($result->sender, "/tell $result->sender");
					$timeString = Util::unixtime_to_readable(time()- $result->time, false);
					$post = preg_replace('/<a href="itemref:\/\/(\d+)\/(\d+)\/(\d+)">([^<]+)<\/a>/', "<a href='itemref://\\1/\\2/\\3'>\\4</a>", $result->message);
					$blob .= "[$senderLink]: {$post} - <highlight>($timeString ago)<end>\n\n";
				}
				$msg = $this->text->make_blob("Shopping Results for '$search' ($count)", $blob);
			}
		}

		$sendto->reply($msg);
	}
}