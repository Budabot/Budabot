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
	public $chatBot;

	/** @Logger */
	public $logger;

	// parameters
	private $uri;
	private $callback;
	private $data;
	
	// uri components
	private $host;
	private $path;
	private $query;
	
	// stream
	private $stream;
	private $notifier;
	private $headersSent = false;
	private $headerReceived = false;
	private $headerData = '';
	private $responseHeaders = array();
	private $responseBody = '';
	private $responseBodyLength = 0;

	private $errorString = false;
	
	/**
	 * Executes HTTP query to $uri.
	 *
	 * @param string   $uri      URI which should be requested
	 * @param callback $callback callbakc which will be called when request is done
	 * @param mixed    $data     extra data which will be passed as second argument to the callback
	 */
	public function execute($uri, $callback, $data) {
		$this->uri = $uri;
		$this->callback = $callback;
		$this->data = $data;
		
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
			$this->host = $components['host'];
		} else {
			$this->setError("Host not specified in uri: '$uri'");
			$this->callCallback();
			return;
		}

		$this->path  = isset($components['path'])? $components['path']: '/';
		$this->path .= isset($components['query'])? "?$components[query]": '';
		
		// start connection
		$flags = STREAM_CLIENT_ASYNC_CONNECT|STREAM_CLIENT_CONNECT;
		// don't use asyncronous stream on Windows with SSL
		// see bug: https://bugs.php.net/bug.php?id=49295
		if ($scheme == 'ssl' && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$flags = STREAM_CLIENT_CONNECT;
		}
		$this->stream = stream_socket_client("$scheme://{$this->host}:$port", $errno, $errstr, 10, $flags);
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
		$this->chatBot->addSocketNotifier($this->notifier);
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
						
						$this->responseBodyLength = isset($headers['content-length'])? intval($headers['content-length']): 0;
					}
				}
				else {
					$this->responseBody .= $data;
				}
				if ($this->responseBodyLength <= strlen($this->responseBody)) {
					$this->responseBody = substr($this->responseBody, 0, $this->responseBodyLength);
					$this->close();
					$this->callCallback();
					break;
				}
			}
			break;

		case SocketNotifier::ACTIVITY_WRITE:
			// the stream is ready for writing so lets dump request headers there
			if ($this->headersSent == false) {
				$headers  = "GET {$this->path} HTTP/1.0\r\n";
				$headers .= "Host: {$this->host}\r\n";
				$headers .= "\r\n";

				$this->logger->log('DEBUG', "Sending request with headers: $headers");

				$written  = fwrite($this->stream, $headers);
				if ($written === false) {
					$this->setError("Cannot write request headers for uri '{$this->uri}' to stream");
					$this->close();
					$this->callCallback();
				} else if ($written != strlen($headers)) {
					$this->setError("Failed to write all http headers in one go for uri '{$this->uri}' to stream");
					$this->close();
					$this->callCallback();
				}
				$this->headersSent = true;
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
		$this->chatBot->removeSocketNotifier($this->notifier);
		$this->notifier = null;
		fclose($this->stream);
	}

	/**
	 * Sets error to given $errorString.
	 *
	 * @param string $errorString error string
	 */
	private function setError($errorString) {
		debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$this->errorString = $errorString;
		$this->logger->log('ERROR', $errorString);
	}
	
	/**
	 * Calls the user supplied callback.
	 */
	private function callCallback() {
		$response = new StdClass();
		$response->error   = $this->errorString;
		$response->headers = $this->responseHeaders;
		$response->body    = $this->responseBody;
		call_user_func($this->callback, $response, $this->data);
	}
}
