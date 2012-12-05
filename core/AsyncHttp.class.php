<?php

/**
 * The AsyncHttp class provides means to make HTTP and HTTPS requests.
 *
 * This class should not be instanced as it is, but instead Util class's
 * httpGet() method should be used to create and use instance of the
 * AsyncHttp class. 
 */
class AsyncHttp {

	/** @Inject */
	public $setting;

	/** @Inject */
	public $socketManager;

	/** @Inject */
	public $timer;

	/** @Logger */
	public $logger;

	// parameters
	private $uri;
	private $callback;
	private $data;
	private $headers = array();
	private $timeout = null;
	private $queryParams = array();
	
	// stream
	private $stream;
	private $notifier;
	private $headerReceived = false;
	private $request = '';
	private $headerData = '';
	private $responseHeaders = array();
	private $responseBody = '';
	private $responseBodyLength = 0;

	private $errorString = false;
	private $timeoutEvent = null;
	private $finished;
	private $loop;

	/**
	 * @param string   $method http method to use (get/post)
	 * @param string   $uri    URI which should be requested
	 */
	public function __construct($method, $uri) {
		$this->method = $method;
		$this->uri    = $uri;
	}

	/**
	 * Executes HTTP query.
	 */
	public function execute() {

		$this->finished = false;

		if ($this->timeout === null) {
			$this->timeout = $this->setting->http_timeout;
		}

		// parse URI's contents
		$components = parse_url($this->uri);
		
		if (is_array($components) == false) {
			$this->setError("Variable '{$this->uri}' is not URI.");
			$this->callCallback();
			return;
		}

		if ($components['scheme'] == 'http') {
			$scheme = 'tcp';
			if (isset($components['port'])) {
				$port = $components['port'];
			} else {
				$port = 80;
			}
		} else if ($components['scheme'] == 'https') {
			$scheme = 'ssl';
			if (isset($components['port'])) {
				$port = $components['port'];
			} else {
				$port = 443;
			}
		} else {
			$this->setError("Unknown scheme '$components[scheme]' provided in uri: '{$this->uri}'");
			$this->callCallback();
			return;
		}
		
		if (isset($components['host'])) {
			$host = $components['host'];
		} else {
			$this->setError("Host not specified in uri: '{$this->uri}'");
			$this->callCallback();
			return;
		}

		// combine uri's query and passed in $params array to single query string
		if (isset($components['query'])) {
			parse_str($components['query'], $this->queryParams);
		}
		$query = http_build_query($this->queryParams);

		$path  = isset($components['path'])? $components['path']: '/';
		// with get-method we'll add the query to path
		if ($this->method == 'get' && $query) {
			$path .= "?{$query}";
		}
		$this->request  = strtoupper($this->method) . " $path HTTP/1.0\r\n";

		$headers = array();
		$headers['Host'] = $host;
		if ($this->method == 'post' && $query) {
			$headers['Content-Type'] = 'application/x-www-form-urlencoded';
			$headers['Content-Length'] = strlen($query);
		}

		$headers = array_merge($headers, $this->headers);
		forEach ($headers as $header => $value) {
			$this->request .= "$header: $value\r\n";
		}

		$this->request .= "\r\n";
		if ($this->method == 'post') {
			$this->request .= $query;
		}
		$this->logger->log('DEBUG', "Sending request: {$this->request}");

		$this->timeoutEvent = $this->timer->callLater($this->timeout, array($this, 'abortWithMessage'),
			"Timeout error after waiting {$this->timeout} seconds");

		// start connection
		$flags = STREAM_CLIENT_ASYNC_CONNECT|STREAM_CLIENT_CONNECT;
		// don't use asyncronous stream on Windows with SSL
		// see bug: https://bugs.php.net/bug.php?id=49295
		if ($scheme == 'ssl' && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$flags = STREAM_CLIENT_CONNECT;
		}
		$this->stream = stream_socket_client("$scheme://$host:$port", $errno, $errstr, 10, $flags);
		if ($this->stream === false) {
			$this->abortWithMessage("Failed to create socket stream, reason: $errstr ($errno)");
			return;
		}
		stream_set_blocking($this->stream, 0);
		// set event loop to notify us when something happens in the stream
		$this->notifier = new SocketNotifier(
			$this->stream,
			SocketNotifier::ACTIVITY_READ|SocketNotifier::ACTIVITY_WRITE|SocketNotifier::ACTIVITY_ERROR,
			array($this, 'onStreamActivity')
		);
		$this->socketManager->addSocketNotifier($this->notifier);
	}

	/**
	 * @param $header
	 * @param $value
	 * @return AsyncHttp
	 */
	public function withHeader($header, $value) {
		$this->headers[$header] = $value;
		return $this;
	}

	/**
	 * @param $timeout
	 * @return AsyncHttp
	 */
	public function withTimeout($timeout) {
		$this->timeout = $timeout;
		return $this;
	}

	/**
	 * Defines a callback which will be called later on when the remote
	 * server has responded or an error has occurred.
	 *
	 * The callback has following signature:
	 * <code>function callback($response, $data)</code>
	 *  * $response - Response as an object, it has properties:
	 *                $error: error message, if any
	 *                $headers: received HTTP headers as an array
	 *                $body: received contents
	 *  * $data     - optional value which is same as given as argument to
	 *                this method.
	 *
	 * @param callable $callback callback which will be called when request is done
	 * @param mixed    $data     extra data which will be passed as second argument to the callback
	 * @return AsyncHttp
	 */
	public function withCallback($callback, $data = null) {
		$this->callback = $callback;
		$this->data     = $data;
		return $this;
	}

	/**
	 * @param array $params array of key/value pair parameters passed as a query
	 * @return AsyncHttp
	 */
	public function withQueryParams($params) {
		$this->queryParams = $params;
		return $this;
	}

	/**
	 * Waits until response is fully received from remote server and returns
	 * the response. Note that this blocks execution, but do not freeze the bot
	 * as the execution will return to event loop while waiting.
	 *
	 * @return mixed
	 */
	public function waitAndReturnResponse() {
		// run in event loop, waiting for loop->quit()
		$this->loop = new EventLoop();
		Registry::injectDependencies($this->loop);
		$this->loop->exec();

		return $this->buildResponse();
	}

	/**
	 * @internal
	 * Handler method which will be called when activity occurs in the SocketNotifier.
	 *
	 * @param int $type type of activity, see SocketNotifier::ACTIVITY_* constants.
	 */
	public function onStreamActivity($type) {
		if ($this->finished) {
			return;
		}

		$this->timeoutEvent->restart();

		switch ($type) {
		case SocketNotifier::ACTIVITY_READ:
			while(true) {
				$data = fread($this->stream, 8192);
				if ($data === false) {
					$this->abortWithMessage("Failed to read from the stream for uri '{$this->uri}'");
					break;
				}
				if (strlen($data) == 0) {
					break; // nothing to read, stop looping
				}
				if ($this->headerReceived == false) {
					$this->headerData .= $data;
					
					$headersEndPos = strpos($this->headerData, "\r\n\r\n");
					if ($headersEndPos !== false) {
						$this->headerReceived = true;
						$this->responseBody = substr($this->headerData, $headersEndPos + 4);
						$this->headerData = substr($this->headerData, 0, $headersEndPos);

						$headers = array();
						forEach (explode("\r\n", $this->headerData) as $line) {
							if (preg_match('/([^:]+):(.+)/', $line, $matches)) {
								$headers[strtolower(trim($matches[1]))] = trim($matches[2]);
							}
						}
						$this->responseHeaders = $headers;
						
						$this->responseBodyLength = isset($headers['content-length'])? intval($headers['content-length']): null;
					}
				}
				else {
					$this->responseBody .= $data;
				}
				if ($this->responseBodyLength !== null && $this->responseBodyLength <= strlen($this->responseBody)) {
					$this->responseBody = substr($this->responseBody, 0, $this->responseBodyLength);
					$this->finish();
					break;
				}
			}

			if (feof($this->stream)) {
				$this->finish();
			}
			break;

		case SocketNotifier::ACTIVITY_WRITE:
			if ($this->request) {
				$written = fwrite($this->stream, $this->request);
				if ($written === false) {
					$this->abortWithMessage("Cannot write request headers for uri '{$this->uri}' to stream");
				} else if ($written > 0) {
					$this->request = substr($this->request, $written);
				}
			}
			break;

		case SocketNotifier::ACTIVITY_ERROR:
			$this->abortWithMessage('Socket error occurred');
			break;
		}
	}

	/**
	 * @internal
	 */
	public function abortWithMessage($errorString) {
		$this->setError($errorString);
		$this->finish();
	}

	private function finish() {
		$this->finished = true;
		$this->timeoutEvent->abort();
		$this->close();
		$this->callCallback();
		if ($this->loop) {
			$this->loop->quit();
		}
	}

	/**
	 * Removes socket notifier from bot's reactor loop and closes the stream.
	 */
	private function close() {
		$this->socketManager->removeSocketNotifier($this->notifier);
		$this->notifier = null;
		fclose($this->stream);
	}

	/**
	 * Sets error to given $errorString.
	 *
	 * @param string $errorString error string
	 */
	private function setError($errorString) {
		$this->errorString = $errorString;
		$this->logger->log('ERROR', $errorString);
	}
	
	/**
	 * Calls the user supplied callback.
	 */
	private function callCallback() {
		if ($this->callback !== null) {
			$response = $this->buildResponse();
			call_user_func($this->callback, $response, $this->data);
		}
	}

	private function buildResponse() {
		$response = new StdClass();
		$response->error   = $this->errorString;
		$response->headers = $this->responseHeaders;
		$response->body    = $this->responseBody;
		return $response;
	}
}
