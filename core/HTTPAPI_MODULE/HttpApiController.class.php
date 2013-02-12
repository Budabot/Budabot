<?php

use Ratchet\WebSocket\WsServer;
use Ratchet\Wamp\WampServer;
use React\Socket\Server as SocketServer;

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
 * @DefineCommand(
 *		command     = 'httpapi updateipaddress',
 *		accessLevel = 'admin',
 *		description = "Updates API's IP-address from whatismyip.com"
 *	)
 */
class HttpApiController {

	/** @Inject */
	public $socketManager;

	/** @Inject */
	public $settingManager;

	/** @Inject */
	public $setting;

	/** @Inject */
	public $text;

	/** @Inject */
	public $http;

	/** @Logger */
	public $logger;

	private $loop;
	private $socket;
	private $httpServer;
	private $wamp;
	
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
	 * @Setting("httpapi_address")
	 * @Description("Server's IP-address or host name")
	 * @Visibility("edit")
	 * @Type("text")
	 */
	public $defaultAddress = "localhost";
	
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
	 * @internal
	 */
	public function setup() {
		$this->loop = new ReactLoopAdapter($this->socketManager);
		$this->socket = new SocketServer($this->loop);
		$this->httpServer = new WebServer($this->socket);

		$this->wamp = new WampHandler();
		$this->httpServer->setRatchetComponent(new WsServer(new WampServer($this->wamp)));

		$that = $this;
		$this->httpServer->on('request', function ($request, $response) use ($that) {
			$request = new \Request($request);

			$session = new StdClass();
			$session->request  = $request;
			$session->response = $response;
			$session->body     = '';

			$request->on('data', function ($bodyBuffer) use ($that, &$session) {
				$session->body .= $bodyBuffer;
				if (!$that->isRequestBodyFullyReceived($session)) {
					return;
				}

				$that->handleRequest($session);
				$session = null;
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
				$port = $that->setting->httpapi_port;
				$that->listen($port);
			} else {
				$that->stopListening();
			}
		});

		if ($this->setting->httpapi_enabled == 1) {
			$port = $this->setting->httpapi_port;
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
	 * <code>
	 *     function callback($request, $response, $data)
	 * </code>
	 *
	 * Arguments:
	 * - $request: http request object (@link: https://github.com/react-php/http/blob/master/Request.php)
	 * - $response: http response object (@link: https://github.com/react-php/http/blob/master/Response.php)
	 * - $data: optional data variable given on register
	 *
	 * Example usage:
	 * <code>
	 *     $this->httpApi->registerHandler("|^/{$this->moduleName}/foo|i", function($request, $response, $requestBody) {
	 *         // ...
	 *     } );
	 * </code>
	 *
	 * @param string   $path     request's path must match to this regexp
	 * @param callback $callback the callback handler to call
	 * @param mixed    $data     any data which will be passed to to the callback(optional)
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
	 *     $uri = $this->httpApi->getUri('/foo');
	 * </code>
	 * Returns: 'http://localhost/foo'
	 *
	 * Settings 'httpapi_address' and 'httpapi_port' affect what the returned
	 * URI will be.
	 *
	 * @param string $path path to uri resource
	 * @return string
	 */
	public function getUri($path) {
		$path    = ltrim($path, '/');
		$address = $this->getHostComponent();
		$port = $this->getPortComponent();
		return "http://$address$port/$path";
	}

	private function getHostComponent() {
		$host = $this->setting->httpapi_address;
		if (!$host) {
			return 'localhost';
		}
		return $host;
	}

	private function getPortComponent() {
		$port = $this->setting->httpapi_port;
		if ($port == 80) {
			return '';
		}
		return ":$port";
	}

	/**
	 * This method returns server's WebSocket uri.
	 *
	 * Example usage:
	 * <code>
	 *     $uri = $this->httpApi->getWebSocketUri();
	 * </code>
	 * Returns: 'ws://localhost/'
	 *
	 * Settings 'httpapi_address' and 'httpapi_port' affect what the returned
	 * URI will be.
	 *
	 * @param string $path path to uri resource
	 * @return string
	 */
	public function getWebSocketUri() {
		$address = $this->getHostComponent();
		$port = $this->getPortComponent();
		return "ws://$address$port/";
	}

	/** @internal */
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
	 * @internal
	 */
	public function httpapiCommand($message, $channel, $sender, $sendto, $args) {
		$uri  = $this->getUri('/');
		$link = $this->text->make_chatcmd( $uri, "/start $uri" );
		$msg  = $this->text->make_blob('HTTP API', "Open $link to web browser.");
		$sendto->reply($msg);
	}

	/**
	 * This command handler checks from whatismyip.com bot's public IP-address
	 * and updates the API's address.
	 *
	 * @HandlesCommand("httpapi updateipaddress")
	 * @internal
	 */
	public function updateIpAddressCommand($message, $channel, $sender, $sendto, $args) {
		$setting = $this->setting;
		$this->http->get('http://automation.whatismyip.com/n09230945.asp')
			->withHeader('User-Agent', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0')
			->withCallback(function($response) use ($setting, $sendto) {
			if ($response->error) {
				$sendto->reply("Failed, error was: {$response->error}");
			} else {
				$setting->httpapi_address = $response->body;
				$sendto->reply("Success, updated httpapi_address setting to: '{$setting->httpapi_address}'");
			}
		});
	}

	/** @internal */
	public function isRequestBodyFullyReceived($session) {
		$headers = $session->request->getHeaders();
		$currentLength  = strlen($session->body);
		$requiredLength = intval($headers['Content-Length']);
		return $currentLength == $requiredLength;
	}

	private function findHandlerForPath($path) {
		forEach ($this->handlers as $handler) {
			if (preg_match($handler->path, $path)) {
				return $handler;
			}
		}
		return null;
	}

	/** @internal */
	public function handleRequest($session) {
		$path = $session->request->getPath();
		$handler = $this->findHandlerForPath($path);
		if ($handler) {
			call_user_func($handler->callback, $session->request,
				$session->response, $session->body, $handler->data);
		} else {
			// handler not found for requested path, next see if by adding or
			// removing a slash to path's end would have a handler, if there is,
			// redirect caller to there
			$lastChar = substr($path, -1);
			if ($lastChar == '/') {
				$path = substr($path, 0, -1);
			} else {
				$path .= '/';
			}
			if ($this->findHandlerForPath($path)) {
				$this->redirectToPath($session->response, $path);
			} else {
				// still no handler, return 'not found' error
				$session->response->writeHead(404);
				$session->response->end();
			}
		}
	}

	/**
	 * This method sends redirection response (http code 302) back to client,
	 * redirecting it to new $path on the same server.
	 *
	 * Example usage:
	 * <code>
	 *     $this->httpApi->redirectToPath($response, "/{$this->moduleName}/redirected/path");
	 * </code>
	 *
	 * @param $response http response object
	 * @param $path new path
	 */
	public function redirectToPath($response, $path) {
		$response->writeHead(302, array(
			'Location' => $this->getUri($path)));
		$response->end();
	}

	/**
	 * This method publishes new WebSocket/WAMP event which will be send to all
	 * connected clients.
	 *
	 * Example usage:
	 * <code>
	 *     $uri = $this->httpApi->getUri('/hello_response');
	 *     $this->httpApi->wampPublish($uri, 'hello world');
	 * </code>
	 *
	 * @param $topicName name or uri of the event topic
	 * @param $payload data to be send with the event
	 */
	public function wampPublish($topicName, $payload) {
		$this->wamp->publish($topicName, $payload);
	}

	/**
	 * This method registers a callback which will be called when
	 * a WebSocket/WAMP client subscribes to a event topic.
	 *
	 * The callback has following signature:
	 * <code>
	 *     function callback($client)
	 * </code>
	 *
	 * Arguments:
	 * - $client: wamp connection to the client which subscribed
	 *    (@link: https://github.com/cboden/Ratchet/blob/master/src/Ratchet/Wamp/WampConnection.php)
     *
	 * Example usage:
	 * <code>
	 *     $uri = $this->httpApi->getUri('/hello');
	 *     $this->httpApi->onWampSubscribe($uri, function($client) {
	 *         $client->send($uri, 'Hello new client!');
	 *     });
	 * </code>
	 *
	 * @param $topicName name or uri of the event topic
	 * @param $callback callback to be called on subscribe
	 */
	public function onWampSubscribe($topicName, $callback) {
		$this->wamp->on("subscribe-$topicName", $callback);
	}
}
