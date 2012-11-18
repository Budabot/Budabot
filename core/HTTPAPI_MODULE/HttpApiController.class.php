<?php

/**
 * @Instance("HttpApi")
 * 
 * Author: Marebone
 *
 * @DefineCommand(
 *		command     = 'httpapi',
 *      description = "Provides web browser link to bot's HTTP API",
 *		accessLevel = 'all'
 * )
 */
class HttpApiController {

	/** @Inject */
	public $socketManager;

	/** @Inject */
	public $settingManager;

	/** @Inject */
	public $text;

	/** @Logger */
	public $logger;

	private $loop;
	private $socket;
	private $http;
	
	/** @internal */
	public $handlers = array();

	/**
	 * @Setting("httpapi_port")
	 * @Description("IP port where the HTTP api server listens at")
	 * @Visibility("edit")
	 * @Type("number")
	 * @Options("80;8080")
	 */
	public $defaultPort = "80";

	/**
	 * @Setting("httpapi_base_uri")
	 * @Description("Server's base uri, leave empty for default value")
	 * @Visibility("edit")
	 * @Type("text")
	 */
	public $defaultBaseUri = "";
	
	/**
	 * @Setting("httpapi_enabled")
	 * @Description("HTTP api server is enabled")
	 * @Visibility("edit")
	 * @Type("options")
	 * @Options("true;false")
	 * @Intoptions("1;0")
	 */
	public $defaultEnabled = "0";

	/**
	 * @Setup
	 */
	public function setup() {
		$this->loop = new ReactLoopAdapter($this->socketManager);
		$this->socket = new React\Socket\Server($this->loop);
		$this->http = new React\Http\Server($this->socket);

		$that = $this;
		$this->http->on('request', function ($request, $response) use ($that) {
			$session = new StdClass();
			$session->request  = $request;
			$session->response = $response;
			$session->body     = '';

			$request->on('data', function ($bodyBuffer) use ($that, $session) {
				$session->body .= $bodyBuffer;
				if (!$that->isRequestBodyFullyReceived($session)) {
					return;
				}
				$handler = $that->findHandlerForRequest($session);
				$that->handleRequest($handler, $session);
			});
		});

		// setup handler for root path
		$this->registerHandler("|^/$|", function ($request, $response) {
			$response->writeHead(200, array('Content-Type' => 'text/plain'));
			$response->end("Hello Budabot!\n");
		});

		// switch server's port if httpapi_port setting is changed
		$this->settingManager->registerChangeListener('httpapi_port', function($name, $oldValue, $newValue) use ($that) {
			$that->listen($newValue);
		});
		
		// listen or stop listening when httpapi_enabled setting is changed
		$this->settingManager->registerChangeListener('httpapi_enabled', function($name, $oldValue, $newValue) use ($that) {
			if ($newValue == 1) {
				$port = $that->settingManager->get('httpapi_port');
				$that->listen($port);
			} else {
				$that->stopListening();
			}
		});

		if ($this->settingManager->get('httpapi_enabled') == 1) {
			$port = $this->settingManager->get('httpapi_port');
			$that->listen($port);
		}
		
		// make sure we close the socket before exit
		register_shutdown_function(function() use ($that) {
			$that->stopListening();
		});
	}

	/**
	 * Adds handler callback which will be called if given $path matches to
	 * what has been requested through the HTTP API.
	 *
	 * The callback has following signature:
	 * <code>function callback($request, $response, $data)</code>
	 * $request: http request object (@link: https://github.com/react-php/http/blob/master/Request.php)
	 * $response: http response object (@link: https://github.com/react-php/http/blob/master/Response.php)
	 * $data: optional data variable given on register
	 *
	 * Example usage:
	 * <code>
	 *	$this->httpApi->registerHandler("|^/{$this->moduleName}/foo|i", function($request, $response, $requestBody) {
	 *		// ...
	 *	} );
	 * </code>
	 *
	 * @param string   $path     request's path must match to this regexp 
	 * @param callback $callback the callback handler to call
	 * $param mixed    $data     any data which will be passed to to the callback(optional)
	 */
	public function registerHandler($path, $callback, $data = null) {
		if (!is_callable($callback)) {
			$this->logger->log('ERROR', 'Given callback is not valid.');
			return;
		}
		$handler = new StdClass();
		$handler->path = $path;
		$handler->callback = $callback;
		$handler->data = $data;
		$this->handlers []= $handler;
	}

	/**
	 * This method returns http uri to given $path.
	 * 
	 * Example usage:
	 * <code>
	 * $uri = $this->httpApi->getUri('/foo');
	 * </code>
	 * Returns: 'http://localhost/foo'
	 *
	 * If setting 'httpapi_base_uri' is set this method will return
	 * that uri + $path instead of localhost + $path.
	 *
	 * @param string $path path to uri resource
	 * @return string
	 */
	public function getUri($path) {
		$path    = ltrim($path, '/');
		$baseUri = $this->settingManager->get('httpapi_base_uri');
		if ($baseUri) {
			$baseUri = rtrim($baseUri, '/');
			return "$baseUri/$path";
		} else {
			$port = $this->settingManager->get('httpapi_port');
			if ($port == 80) {
				return "http://localhost/$path";
			} else {
				return "http://localhost:$port/$path";
			}
		}
	}
	
	public function stopListening() {
		$this->socket->shutdown();
	}
	
	/**
	 * This method (re)starts the http server.
	 *
	 * @param integer $port ip port where the server will listen
	 * @internal
	 */
	public function listen($port) {
		$this->stopListening();

		// test if the port is available
		$socket = socket_create(AF_INET, SOCK_STREAM, 0);
		if (socket_bind($socket, '0.0.0.0', $port) === false) {
			$this->logger->log('ERROR', "Starting HTTP API failed, port $port is already in use");
			return;
		}
		socket_close($socket);
		
		try {
			$this->socket->listen($port, '0.0.0.0');
		} catch(Exception $e) {
			$this->logger->log('ERROR', 'Starting HTTP API failed, reason: ' . $e->getMessage());
		}
	}

	/**
	 * This command handler shows web link to user.
	 *
	 * @HandlesCommand("httpapi")
	 */
	public function httpapiCommand($message, $channel, $sender, $sendto, $args) {
		$uri  = $this->getUri('/');
		$link = $this->text->make_chatcmd( $uri, "/start $uri" );
		$msg  = $this->text->make_blob('HTTP API', "Open $link to web browser.");
		$sendto->reply($msg);
	}

	public function isRequestBodyFullyReceived($session) {
		$headers = $session->request->getHeaders();
		$currentLength  = strlen($session->body);
		$requiredLength = intval($headers['Content-Length']);
		return $currentLength == $requiredLength;
	}

	public function findHandlerForRequest($session) {
		forEach ($this->handlers as $handler) {
			if (preg_match($handler->path, $session->request->getPath())) {
				return $handler;
			}
		}
		return null;
	}

	public function handleRequest($handler, $session) {
		if ($handler) {
			call_user_func($handler->callback, $session->request,
				$session->response, $session->body, $handler->data);
		} else {
			$session->response->writeHead(404);
			$session->response->end();
		}
	}
}
