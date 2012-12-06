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
	private $requestData = '';
	private $headerData = '';
	private $responseHeaders = array();
	private $responseBody = '';

	private $request;
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

		if (!$this->buildRequest()) {
			return;
		}

		$this->finished = false;

		$this->logger->log('DEBUG', "Sending request: {$this->request->getData()}");

		$this->initTimeout();
		$this->timeoutEvent = $this->timer->callLater($this->timeout, array($this, 'abortWithMessage'),
			"Timeout error after waiting {$this->timeout} seconds");

		if (!$this->createStream()) {
			return;
		}

		$this->setupStreamNotify();
	}

	private function buildRequest() {
		try {
			$this->request = new HttpRequest($this->method, $this->uri, $this->queryParams, $this->headers);
			$this->requestData = $this->request->getData();
		} catch (InvalidHttpRequest $e) {
			$this->abortWithMessage($e->getMessage());
			return false;
		}
		return true;
	}

	/**
	 * @internal
	 */
	public function abortWithMessage($errorString) {
		$this->setError($errorString);
		$this->finish();
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

	private function initTimeout() {
		if ($this->timeout === null) {
			$this->timeout = $this->setting->http_timeout;
		}
	}

	private function createStream() {
		$this->stream = stream_socket_client($this->getStreamUri(), $errno, $errstr, 10, $this->getStreamFlags());
		if ($this->stream === false) {
			$this->abortWithMessage("Failed to create socket stream, reason: $errstr ($errno)");
			return false;
		}
		stream_set_blocking($this->stream, 0);
		return true;
	}

	private function getStreamUri() {
		$scheme = $this->request->getScheme();
		$host = $this->request->getHost();
		$port = $this->request->getPort();
		return "$scheme://$host:$port";
	}

	private function getStreamFlags() {
		$flags = STREAM_CLIENT_ASYNC_CONNECT | STREAM_CLIENT_CONNECT;
		// don't use asyncronous stream on Windows with SSL
		// see bug: https://bugs.php.net/bug.php?id=49295
		if ($this->request->getScheme() == 'ssl' && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$flags = STREAM_CLIENT_CONNECT;
		}
		return $flags;
	}

	private function setupStreamNotify() {
		// set event loop to notify us when something happens in the stream
		$this->notifier = new SocketNotifier(
			$this->stream,
			SocketNotifier::ACTIVITY_READ | SocketNotifier::ACTIVITY_WRITE | SocketNotifier::ACTIVITY_ERROR,
			array($this, 'onStreamActivity')
		);
		$this->socketManager->addSocketNotifier($this->notifier);
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

							$this->responseHeaders = $this->extractHeadersFromHeaderData();
						}
					}
					else {
						$this->responseBody .= $data;
					}
					if ($this->getBodyLength() !== null && $this->getBodyLength() <= strlen($this->responseBody)) {
						$this->responseBody = substr($this->responseBody, 0, $this->getBodyLength());
						$this->finish();
						break;
					}
				}

				if (feof($this->stream)) {
					$this->finish();
				}
				break;

			case SocketNotifier::ACTIVITY_WRITE:
				if ($this->requestData) {
					$written = fwrite($this->stream, $this->requestData);
					if ($written === false) {
						$this->abortWithMessage("Cannot write request headers for uri '{$this->uri}' to stream");
					} else if ($written > 0) {
						$this->requestData = substr($this->requestData, $written);
					}
				}
				break;

			case SocketNotifier::ACTIVITY_ERROR:
				$this->abortWithMessage('Socket error occurred');
				break;
		}
	}

	private function getBodyLength() {
		return isset($this->responseHeaders['content-length']) ? intval($this->responseHeaders['content-length']) : null;
	}

	private function extractHeadersFromHeaderData() {
		$headers = array();
		forEach (explode("\r\n", $this->headerData) as $line) {
			if (preg_match('/([^:]+):(.+)/', $line, $matches)) {
				$headers[strtolower(trim($matches[1]))] = trim($matches[2]);
			}
		}
		return $headers;
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
}
