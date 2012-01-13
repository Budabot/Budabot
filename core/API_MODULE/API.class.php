<?php

class API {

	/** @Inject */
	public $command;
	
	/** @Inject */
	public $preferences;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $setting;
	
	/** @Logger */
	public $logger;

	/**
	 * @Setting("api_port")
	 * @Description("Port number to listen for API requests")
	 * @Visibility("edit")
	 * @Type("number")
	 * @Options("5250")
	 */
	public $defaultAPIPort = "5250";
	
	private $apisocket;

	/**
	 * @Event("connect")
	 * @Description("Opens a socket to listen for API requests")
	 * @DefaultStatus("0")
	 */
	public function connect($eventObj) {
		// bind to port 5250 on any address
		$address = '0.0.0.0';
		$port = $this->setting->get('api_port');

		// Create a TCP Stream socket
		$this->apisocket = socket_create(AF_INET, SOCK_STREAM, 0);
		socket_bind($this->apisocket, $address, $port);
		$errno = socket_last_error();
		if ($errno == 0) {
			$this->logger->log('INFO', 'API socket bound successfully');
		} else {
			$this->logger->log('ERROR', socket_strerror($errno));
		}
		socket_listen($this->apisocket);
		socket_set_nonblock($this->apisocket);
	}
	
	/**
	 * @Event("2sec")
	 * @Description("Checks for and processes API requests")
	 * @DefaultStatus("0")
	 */
	public function listen($eventObj) {
		/* Accept incoming requests and handle them as child processes */
		$client = @socket_accept($this->apisocket);
		if ($client !== false) {
			$clientHandler = new ClientHandler($client);

			// Read the input from the client
			$apiRequest = $clientHandler->readPacket();
			if ($apiRequest->version != API_VERSION) {
				$clientHandler->writePacket(new APIResponse(API_FAILURE, "API version must be: " . API_VERSION));
			}
			
			$password = $this->preferences->get($apiRequest->username, 'apipassword');
			if ($password === false) {
				$clientHandler->writePacket(new APIResponse(API_FAILURE, "Password has not been set for this user."));
			} else if ($password != $apiRequest->password) {
				$clientHandler->writePacket(new APIResponse(API_FAILURE, "Password was incorrect."));
			} else {
				if ($apiRequest->type == API_SIMPLE_MSG) {
					$type = 'msg';
				} else if ($apiRequest->type == API_ADVANCED_MSG) {
					$type = 'api';
				} else {
					$clientHandler->writePacket(new APIResponse(API_FAILURE, "Invalid request type."));
					return;
				}

				$this->command->command($type, $apiRequest->command, $apiRequest->username, $clientHandler);
			}
		}
	}
	
	/**
	 * @Command("apipassword")
	 * @AccessLevel("mod")
	 * @Description("Set your api password")
	 * @Matches("/^apipassword (.*)$/i")
	 */
	public function apipasswordCommand($message, $channel, $sender, $sendto, $arr) {
		$this->preferences->save($sender, 'apipassword', $password, $arr[1]);
		$this->chatBot->send("Your API password has been updated successfully.", $sendto);
	}
}

?>
