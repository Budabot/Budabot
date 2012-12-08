<?php

class RunnerRpcService {

	/** @Inject */
	public $socketManager;

	/** @Inject */
	public $httpApi;

	public $resultsMap = array();

	public function start($port) {
		$loop = new ReactLoopAdapter($this->socketManager);
		$this->server = new JSONRPCServer($loop, $port, $this);
	}

	public function givenRequestToUriReturnsResult($uri, $result) {
		$parts = parse_url($uri);
		$key = "$parts[host]+$parts[path]";
		$this->resultsMap[$key] = $result;

		$that = $this;
		$this->httpApi->registerHandler("|^/tests/|i", function ($request, $response) use ($that) {
			$headers = $request->getHeaders();
			$host = $headers['Host'];
			$path = substr($request->getPath(), 6);
			$key = "$host+$path";
			if (isset($that->resultsMap[$key])) {
				$response->writeHead(200);
				$response->end($that->resultsMap[$key]);
				return;
			}
			LegacyLogger::log('ERROR', 'Tests', "Http-query '$key' not found");
			$response->writeHead(404);
			$response->end();
		});
	}
}
