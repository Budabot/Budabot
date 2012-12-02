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
	public $socketManager;

	/** @Logger */
	public $logger;

	// parameters
	private $uri;
	private $callback;
	private $data;
	private $headers = array();
	
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
	
	/**
	 * Executes HTTP query to $uri.
	 *
	 * @param string   $method   http method to use (get/post)
	 * @param string   $uri      URI which should be requested
	 * @param array    $params   array of key/value pair parameters passed as a query
	 * @param callback $callback callbakc which will be called when request is done
	 * @param mixed    $data     extra data which will be passed as second argument to the callback
	 */
	public function execute($method, $uri, $params, $callback, $data) {
		$this->uri      = $uri;
		$this->callback = $callback;
		$this->data     = $data;
		
		// parse URI's contents
		$components = parse_url($uri);
		
		if (is_array($components) == false) {
			$this->setError("Variable '$uri' is not URI.");
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
			$this->setError("Unknown scheme '$components[scheme]' provided in uri: '$uri'");
			$this->callCallback();
			return;
		}
		
		if (isset($components['host'])) {
			$host = $components['host'];
		} else {
			$this->setError("Host not specified in uri: '$uri'");
			$this->callCallback();
			return;
		}

		// combine uri's query and passed in $params array to single query string
		if (isset($components['query'])) {
			parse_str($components['query'], $params);
		}
		$query = http_build_query($params);

		$path  = isset($components['path'])? $components['path']: '/';
		// with get-method we'll add the query to path
		if ($method == 'get' && $query) {
			$path .= "?{$query}";
		}
		$this->request  = strtoupper($method) . " $path HTTP/1.0\r\n";

		$headers = array();
		$headers['Host'] = $host;
		if ($method == 'post' && $query) {
			$headers['Content-Type'] = 'application/x-www-form-urlencoded';
			$headers['Content-Length'] = strlen($query);
		}

		$headers = array_merge($headers, $this->headers);
		forEach ($headers as $header => $value) {
			$this->request .= "$header: $value\r\n";
		}

		$this->request .= "\r\n";
		if ($method == 'post') {
			$this->request .= $query;
		}
		$this->logger->log('DEBUG', "Sending request: {$this->request}");

		// start connection
		$flags = STREAM_CLIENT_ASYNC_CONNECT|STREAM_CLIENT_CONNECT;
		// don't use asyncronous stream on Windows with SSL
		// see bug: https://bugs.php.net/bug.php?id=49295
		if ($scheme == 'ssl' && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$flags = STREAM_CLIENT_CONNECT;
		}
		$this->stream = stream_socket_client("$scheme://$host:$port", $errno, $errstr, 10, $flags);
		if ($this->stream === false) {
			$this->setError("Failed to create socket stream, reason: $errstr ($errno)");
			$this->callCallback();
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

	public function withHeader($header, $value) {
		$this->headers[$header] = $value;
	}

	/**
	 * Handler method which will be called when activity occurs in the SocketNotifier.
	 *
	 * @param int $type type of activity, see SocketNotifier::ACTIVITY_* constants.
	 */
	public function onStreamActivity($type) {
		switch ($type) {
		case SocketNotifier::ACTIVITY_READ:
			while(true) {
				$data = fread($this->stream, 8192);
				if ($data === false) {
					$this->setError("Failed to read from the stream for uri '{$this->uri}'");
					$this->close();
					$this->callCallback();
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
					$this->close();
					$this->callCallback();
					break;
				}
			}

			if (feof($this->stream)) {
				$this->close();
				$this->callCallback();
			}
			break;

		case SocketNotifier::ACTIVITY_WRITE:
			if ($this->request) {
				$written = fwrite($this->stream, $this->request);
				if ($written === false) {
					$this->setError("Cannot write request headers for uri '{$this->uri}' to stream");
					$this->close();
					$this->callCallback();
				} else if ($written > 0) {
					$this->request = substr($this->request, $written);
				}
			}
			break;

		case SocketNotifier::ACTIVITY_ERROR:
			$this->close();
			$this->callCallback();
			break;
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
			$response = new StdClass();
			$response->error   = $this->errorString;
			$response->headers = $this->responseHeaders;
			$response->body    = $this->responseBody;
			call_user_func($this->callback, $response, $this->data);
		}
	}
}
