<?php

namespace Budabot\Core\Modules;

use stdClass;

/**
 * Authors:
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command       = 'feedback',
 *		accessLevel   = 'all',
 *		description   = 'Provide feedback or report a problem to the Budabot team',
 *		help          = 'feedback.txt',
 *		defaultStatus = '1'
 *	)
 */
class FeedbackController {
	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $db;

	/** @Inject */
	public $http;
	
	/** @Inject */
	public $text;
	
	/**
	 * @HandlesCommand("feedback")
	 * @Matches("/^feedback (.*)$/i")
	 */
	public function feedbackCommand($message, $channel, $sender, $sendto, $args) {
		$message = $args[1];
		
		global $version;
		
		$obj = new stdClass;
		$obj->name = $sender;
		$obj->comment = $message;
		$obj->version = $version;
		
		$postArray['feedback'] = json_encode($obj);
		
		$url = 'http://budabot.jkbff.com/feedback/feedback.php';
		$msg = $this->http->post($url)->withQueryParams($postArray)->waitAndReturnResponse()->body;
		
		if (empty($msg)) {
			$msg = "There was an error submitting your request.";
		}
		$sendto->reply($msg);
	}
}

?>