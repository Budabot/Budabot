<?php

class API {

	/** @Inject */
	public $commandManager;
	
	/** @Inject */
	public $preferences;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $setting;
	
	/** @Logger */
	public $logger;

	/** @Inject */
	public $accessLevel;
	
	private $apisocket = null;
	private $socketNotifier = null;

	private function openApiSocket() {
		// bind to any address
		$address = '0.0.0.0';
		
		// read port from config-file
		$port = intval($this->chatBot->vars['API Port']);
		if ($port < 1 || $port > 65535) {
			$this->logger->log('ERROR', "API's port must be within 1 and 65535, currently it is $port");
			return;
		}

		// Create a TCP Stream socket
		$this->apisocket = socket_create(AF_INET, SOCK_STREAM, 0);
		socket_bind($this->apisocket, $address, $port);
		$errno = socket_last_error();
		if ($errno == 0) {
			$this->logger->log('INFO', 'API socket bound successfully');
			socket_listen($this->apisocket);
			socket_set_nonblock($this->apisocket);
			$this->socketNotifier = new SocketNotifier($this->apisocket, 
				SocketNotifier::ACTIVITY_READ, array($this, 'onApiActivity'));
			$this->chatBot->addSocketNotifier($this->socketNotifier);
		} else {
			$this->logger->log('ERROR', socket_strerror($errno));
		}
		
	}
	
	/**
	 * @Event("connect")
	 * @Description("Start to listen for incoming command requests")
	 */
	function connectEvent($eventObj) {
		// open the api socket if it is not open yet
		if ($this->apisocket === null) {
			$this->openApiSocket();
		}
	}

	/**
	 * This method is called there is activity in the API socket.
	 */
	public function onApiActivity($type) {
		/* Accept incoming requests and handle them as child processes */
		$client = @socket_accept($this->apisocket);
		if ($client !== false) {
			$clientHandler = new ClientHandler($client, $this->logger);

			// Read the input from the client
			$apiRequest = $clientHandler->readPacket();
			if ($apiRequest->version != API_VERSION) {
				$clientHandler->writePacket(new APIResponse(API_INVALID_VERSION, "API version must be: " . API_VERSION));
				return;
			}
			
			$userPassword = $this->preferences->get($apiRequest->username, 'apipassword');

			$isSuperAdmin = $apiRequest->username == $this->chatBot->vars['SuperAdmin'];
			$fromLocalHost = $clientHandler->getClientAddress() == '127.0.0.1';
			
			// password is not needed for superadmin from 'localhost' if the superadmin hasn't set password yet
			$noPasswordNeeded = $isSuperAdmin && $fromLocalHost && !$userPassword;

			if (!$noPasswordNeeded) {
				if ($userPassword === false) {
					$clientHandler->writePacket(new APIResponse(API_UNSET_PASSWORD, "Password has not been set for this user."));
					return;
				} else if ($userPassword != $apiRequest->password) {
					$clientHandler->writePacket(new APIResponse(API_INVALID_PASSWORD, "Password was incorrect."));
					return;
				}
			}
			
			if ($apiRequest->type == API_SIMPLE_MSG) {
				$type = 'msg';
				$apiReply = new APISimpleReply();
			} else if ($apiRequest->type == API_ADVANCED_MSG) {
				$type = 'api';
				$apiReply = new APIAdvancedReply();
			} else {
				$clientHandler->writePacket(new APIResponse(API_INVALID_REQUEST_TYPE, "Invalid request type."));
				return;
			}

			try {
				$responseCode = $this->process($type, $apiRequest->command, $apiRequest->username, $apiReply);
				$response = new APIResponse($responseCode, $apiReply->getOutput());
			} catch (APIException $e) {
				$response = new APIResponse(API_EXCEPTION, $e->getResponseMessage());
			} catch (Exception $e) {
				$response = new APIResponse(API_EXCEPTION, $e->getMessage());
			}
			$clientHandler->writePacket($response);
		}
	}
	
	private function process($channel, $message, $sender, $sendto) {
		list($cmd, $params) = explode(' ', $message, 2);
		$cmd = strtolower($cmd);
		
		$commandHandler = $this->commandManager->getActiveCommandHandler($cmd, $channel, $message);
		
		// if command doesn't exist
		if ($commandHandler === null) {
			$this->chatBot->spam[$sender] += 20;
			return API_UNKNOWN_COMMAND;
		}

		// if the character doesn't have access
		if ($this->accessLevel->checkAccess($sender, $commandHandler->admin) !== true) {
			$this->chatBot->spam[$sender] += 20;
			return API_ACCESS_DENIED;
		}

		// record usage stats
		if ($this->setting->get('record_usage_stats') == 1) {
			Registry::getInstance('usage')->record($channel, $cmd, $sender, $commandHandler);
		}
	
		$syntaxError = $this->commandManager->callCommandHandler($commandHandler, $message, $channel, $sender, $sendto);
		$this->chatBot->spam[$sender] += 10;
		
		if ($syntaxError === true) {
			return API_SYNTAX_ERROR;
		} else {
			return API_SUCCESS;
		}
	}
	
	/**
	 * @Command("apipassword")
	 * @AccessLevel("all")
	 * @Description("Sets your api password, use 'apipassword clear' to clear your password.")
	 * @Matches("/^apipassword (.*)$/i")
	 */
	public function apipasswordCommand($message, $channel, $sender, $sendto, $arr) {
		if ($arr[1] == 'clear') {
			$this->preferences->save($sender, 'apipassword', '');
			$sendto->reply("Your API password has been cleared successfully.");
		} else {
			$this->preferences->save($sender, 'apipassword', $arr[1]);
			$sendto->reply("Your API password has been updated successfully.");
		}
	}
}

?>
