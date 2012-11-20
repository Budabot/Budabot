<?php
/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'testcia',
 *		accessLevel = 'all',
 *		description = 'Relay commit messages into IRC channel'
 *	)
 */
class CIAController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $db;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $ircController;
	
	/** @Inject */
	public $httpApi;
	
	/** @Inject */
	public $settingManager;
	
	/** @Inject */
	public $chatBot;
	
	/** @Logger */
	public $logger;
	
	private $apisocket = null;

	/** @Setup */
	public function setup() {
		
	}
	
	/**
	 * @HandlesCommand("testcia")
	 * @Matches("/^testcia$/i")
	 */
	public function testCIACommand($message, $channel, $sender, $sendto, $args) {
		$output = "POST /CIA_MODULE/processCommit HTTP/1.1\r\n";
		$output .= "Google-Code-Project-Hosting-Hook-HMAC: 17877aa4ea14ae8354af5514c438a77b\r\n";
		$output .= "Content-Type: application/json; charset=UTF-8\r\n";
		$output .= "User-Agent: Google Code Project Hosting (+http://code.google.com/p/support/wiki/PostCommitWebHooks)\r\n";
		//$output .= "Host: stats.jkbff.com:9200\r\n";
		$output .= "Content-Length: 383\r\n";
		$output .= "Accept-Encoding: gzip\r\n";
		$output .= "\r\n";
		$output .= '{"repository_path":"https://budabot2.googlecode.com/svn/","project_name":"budabot2","revisions":[{"added":[],"author":"bigwheels16","url":"http://budabot2.googlecode.com/svn-history/r1/","timestamp":1349898813,"message":"test CIA_MODULE commit hook","path_count":1,"removed":[],"modified":["/trunk/modules/CIA_MODULE/CIAController.class.php"],"revision":1}],"revision_count":1}';

		$fp = fsockopen("127.0.0.1", $this->settingManager->get('httpapi_port'), $errno, $errstr, 30);
		if (!$fp) {
			echo "$errstr ($errno)<br />\n";
		} else {
			echo "writing...\n";
			fwrite($fp, $output);
			/*while (!feof($fp)) {
				echo fgets($fp, 8192);
			}*/
			echo "done writing...\n";
			fclose($fp);
		}
		$sendto->reply("Message sent.");
	}

	/**
	 * @Event("connect")
	 * @Description("Start to listen for incoming commit notifications")
	 * @DefaultStatus("0")
	 */
	public function openApiSocket() {
		// register context path for processing incoming commits
		$this->httpApi->registerHandler("|^/{$this->moduleName}/processCommit|i", array($this, 'processCommit'));
	}
	
	public function processCommit($request, $response, $requestBody) {
		$response->writeHead(200, array('Content-Type' => 'text/plain'));
		$response->end();

		$obj = json_decode($requestBody);
		forEach ($obj->revisions as $revision) {
			$msg = "r{$revision->revision}: $revision->author ($revision->path_count file(s)) - $revision->message";
			$this->ircController->sendMessageToIRC($msg);
			$this->chatBot->sendPrivate($msg);
		}
	}
}

