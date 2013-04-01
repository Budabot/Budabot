<?php

namespace Budabot\Core\Modules;

class Request {

	/**
	 * @var React\Http\Request
	 */
	private $request;

	public function __construct($request) {
		$this->request = $request;
	}

	public function getMethod() {
		return $this->request->getMethod();
	}

	public function getPath() {
		return $this->request->getPath();
	}

	public function getQuery() {
		return $this->request->getQuery();
	}

	public function getHttpVersion() {
		return $this->request->getHttpVersion();
	}

	public function getHeaders() {
		return $this->request->getHeaders();
	}

	public function expectsContinue() {
		return $this->request->expectsContinue();
	}

	public function isReadable() {
		return $this->request->isReadable();
	}

	public function pause() {
		$this->request->pause();
	}

	public function resume() {
		$this->request->resume();
	}

	public function close() {
		$this->request->close();
	}

	public function pipe(React\Stream\WritableStreamInterface $dest, array $options = array()) {
		return $this->request->pipe($dest, $options);
	}

	public function on($event, $listener) {
		$this->request->on($event, $listener);
	}

	public function removeAllListeners() {
		$this->request->removeAllListeners();
	}

	public function getCookies() {
		$headers = $this->getHeaders();
		$cookieString = isset($headers['Cookie'])? $headers['Cookie']: '';

		$cookies = array();
		$kvStrings = explode('; ', $cookieString);
		forEach ($kvStrings as $kv) {
			if (strpos($kv, '=') !== false) {
				list($key, $value) = explode('=', $kv, 2);
				$cookies[trim($key)] = $value;
			}
		}
		return $cookies;
	}

	public function getCookie($name) {
		$cookies = $this->getCookies();
		return isset($cookies[$name])? $cookies[$name]: null;
	}

	public function isWebSocketHandshake() {
		$headers = $this->getHeaders();
		return isset($headers['Upgrade']) &&
			strcasecmp($headers['Upgrade'], 'websocket') == 0;
	}

	public function toRequestString() {
		$method = $this->getMethod();
		$path = $this->getPath();
		$version = $this->getHttpVersion();
		$headerData = "$method $path HTTP/$version\r\n";
		foreach ($this->getHeaders() as $header => $value) {
			$headerData .= "$header: $value\r\n";
		}
		$headerData .= "\r\n";
		return $headerData;
	}
}
