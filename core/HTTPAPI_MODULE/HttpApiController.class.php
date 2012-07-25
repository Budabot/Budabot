<?php

/**
 * @Instance
 * 
 * Author: Marebone
 */
class HttpApiController {

	/** @Inject */
	public $chatBot;

	private $loop;
	private $socket;
	private $http;

	/** @Setup */
	public function setup() {
		$this->loop = new ReactLoopAdapter($this->chatBot);
		$this->socket = new React\Socket\Server($this->loop);
		$this->http = new React\Http\Server($this->socket);

		$this->http->on('request', function ($request, $response) {
			$response->writeHead(200, array('Content-Type' => 'text/plain'));
			$response->end("Hello Budabot!\n");
		});

		$this->socket->listen(1337);
	}
}
