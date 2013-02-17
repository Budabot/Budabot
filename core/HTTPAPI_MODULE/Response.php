<?php

class Response {

	/**
	 * @var React\Http\Response
	 */
	private $response;

	private $cookieName = null;
	private $cookieValue = null;
	private $cookieOptions = array();

	function __construct($response) {
		$this->response = $response;
	}

	public function isWritable() {
		return $this->response->isWritable();
	}

	public function writeContinue() {
		$this->response->writeContinue();
	}

	public function writeHead($status = 200, $headers = array()) {
		$headers = $this->addCookieHeader($headers);
		$this->response->writeHead($status, $headers);
	}

	public function write($data) {
		$this->response->write($data);
	}

	public function end($data = null) {
		$this->response->end($data);
	}

	public function close() {
		$this->response->close();
	}

	public function on($event, $listener) {
		$this->response->on($event, $listener);
	}

	public function setCookie($name, $value, $options = array()) {
		$this->cookieName = $name;
		$this->cookieValue = $value;
		$this->cookieOptions = $options;
	}

	private function addCookieHeader($in) {
		if ($this->cookieName !== null && $this->cookieValue !== null) {
			$optionsStr = '';
			forEach ($this->cookieOptions as $option => $value) {
				$optionsStr .= "; {$option}={$value}";
			}
			$in['Set-Cookie'] = "{$this->cookieName}={$this->cookieValue}{$optionsStr}";
		}
		return $in;
	}
}
