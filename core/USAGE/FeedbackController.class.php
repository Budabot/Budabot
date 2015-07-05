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
	 * @Matches("/^feedback confirm (.*)$/i")
	 */
	public function feedbackConfirmCommand($message, $channel, $sender, $sendto, $args) {
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
			$msg = "There was an error submitting your request.1";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("feedback")
	 * @Matches("/^feedback (.*)$/i")
	 */
	public function feedbackCommand($message, $channel, $sender, $sendto, $args) {
		$message = $args[1];
		
		global $version;

		$blob = '';
		$blob .= "This is the message that will be sent to the Budabot team:\n\n";
		$blob .= "Sender: <highlight>$sender<end>\n";
		$blob .= "Version: <highlight>$version<end>\n";
		$blob .= "Message: <highlight>$message<end>\n\n";
		$blob .= "To send this feedback, click here: ";
		$blob .= $this->text->make_chatcmd("Submit message", "/tell <myname> feedback confirm $message");
		$blob .= "\n\nTo change your message use the <symbol>feedback command to create a new message.\n\n";
		$blob .= "The Budabot team accepts any type of feedback including bug reports, feature requests, praise, and criticism.";

		$msg = $this->text->make_blob("Verify Your Feedback Submission", $blob);
		$sendto->reply($msg);
	}
}

?>