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
	private $requestData = '';
	private $responseData = '';
	private $headersEndPos = false;
	private $responseHeaders = array();

	private $request;
	private $errorString = false;
	private $timeoutEvent = null;
	private $finished;
	private $loop;

	/**
	 * @internal
	 * @param string   $method http method to use (get/post)
	 * @param string   $uri    URI which should be requested
	 */
	public function __construct($method, $uri) {
		$this->method   = $method;
		$this->uri      = $uri;
		$this->finished = false;
	}

	/**
	 * @internal
	 * Executes HTTP query.
	 */
	public function execute() {
		if (!$this->buildRequest()) {
			return;
		}

		$this->initTimeout();

		if (!$this->createStream()) {
			return;
		}
		$this->setupStreamNotify();

		$this->logger->log('DEBUG', "Sending request: {$this->request->getData()}");
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
		$response->body    = $this->getResponseBody();
		return $response;
	}

	private function initTimeout() {
		if ($this->timeout === null) {
			$this->timeout = $this->setting->http_timeout;
		}
		$this->timeoutEvent = $this->timer->callLater($this->timeout, array($this, 'abortWithMessage'),
			"Timeout error after waiting {$this->timeout} seconds");
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
				$this->processResponse();
				break;

			case SocketNotifier::ACTIVITY_WRITE:
				$this->processRequest();
				break;

			case SocketNotifier::ACTIVITY_ERROR:
				$this->abortWithMessage('Socket error occurred');
				break;
		}
	}

	private function processResponse() {
		$this->responseData = $this->readAllFromSocket();

		if (!$this->areHeadersReceived()) {
			$this->processHeaders();
		}

		if ($this->isBodyLengthKnown() && $this->isBodyFullyReceived()) {
			$this->finish();
		}

		if ($this->isStreamClosed()) {
			$this->finish();
		}
	}

	private function processHeaders() {
		$this->headersEndPos = strpos($this->responseData, "\r\n\r\n");
		if ($this->headersEndPos !== false) {
			$headerData = substr($this->responseData, 0, $this->headersEndPos);
			$this->responseHeaders = $this->extractHeadersFromHeaderData($headerData);
		}
	}

	private function getResponseBody() {
		return substr($this->responseData, $this->headersEndPos + 4);
	}

	private function areHeadersReceived() {
		return $this->headersEndPos !== false;
	}

	private function isStreamClosed() {
		return feof($this->stream);
	}

	private function isBodyFullyReceived() {
		return $this->getBodyLength() <= strlen($this->getResponseBody());
	}

	private function isBodyLengthKnown() {
		return $this->getBodyLength() !== null;
	}

	private function readAllFromSocket() {
		$data = '';
		while (true) {
			$chunk = fread($this->stream, 8192);
			if ($chunk === false) {
				$this->abortWithMessage("Failed to read from the stream for uri '{$this->uri}'");
				break;
			}
			if (strlen($chunk) == 0) {
				break; // nothing to read, stop looping
			}
			$data .= $chunk;
		}
		return $data;
	}

	private function getBodyLength() {
		return isset($this->responseHeaders['content-length']) ? intval($this->responseHeaders['content-length']) : null;
	}

	private function extractHeadersFromHeaderData($data) {
		$headers = array();
		forEach (explode("\r\n", $data) as $line) {
			if (preg_match('/([^:]+):(.+)/', $line, $matches)) {
				$headers[strtolower(trim($matches[1]))] = trim($matches[2]);
			}
		}
		return $headers;
	}

	private function processRequest() {
		if ($this->requestData) {
			$written = fwrite($this->stream, $this->requestData);
			if ($written === false) {
				$this->abortWithMessage("Cannot write request headers for uri '{$this->uri}' to stream");
			} else if ($written > 0) {
				$this->requestData = substr($this->requestData, $written);
			}
		}
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
