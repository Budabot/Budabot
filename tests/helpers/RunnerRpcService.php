<?php

class RunnerRpcService {

	/** @Inject */
	public $db;

	/** @Inject */
	public $eventManager;

	/** @Inject */
	public $httpApi;

	/** @Inject */
	public $socketManager;

	public $resultsMap = array();
	public $receivedRequests = array();

	public function start($port) {
		$loop = new ReactLoopAdapter($this->socketManager);
		$this->server = new JSONRPCServer($loop, $port, $this);
	}

	public function givenRequestToUriReturnsResult($uri, $result) {
		$parts = parse_url($uri);
		$key = $this->buildKey($parts['host'], $parts['path']);
		$this->resultsMap[$key] = $result;

		$that = $this;
		$this->httpApi->registerHandler("|^/tests/|i", function ($request, $response, $body) use ($that) {
			$headers = $request->getHeaders();
			$key = $that->buildKey($headers['Host'], $request->getPath());

			if (isset($that->resultsMap[$key])) {
				$that->receivedRequests[$key] = $body;
				$responseBody = $that->resultsMap[$key];
				$response->writeHead(200, array('Content-Length' => strlen($responseBody)));
				$response->end($responseBody);
				return;
			}

			LegacyLogger::log('ERROR', 'Tests', "Http-query '$key' not found");
			$response->writeHead(404);
			$response->end();
		});
	}

	public function buildKey($host, $path) {
		if (strpos($path, '/tests') === 0) {
			$path = substr($path, 6);
		}
		return "$host+$path";
	}

	public function triggerEventInModule($module, $type) {
		$eventObj = new stdClass;
		$eventObj->type = $type;

		$data = $this->db->query(
			"SELECT `file` FROM eventcfg_<myname> WHERE `module` = ? AND `type` = ?", $module, $type
		);

		forEach ($data as $row) {
			$eventObj = new stdClass;
			$eventObj->type = $type;
			$this->eventManager->callEventHandler($eventObj, $row->file);
		}
	}

	public function takeReceivedRequestMadeToUri($uri) {
		$result = false;
		$parts = parse_url($uri);
		$key = $this->buildKey($parts['host'], $parts['path']);
		if (isset($this->receivedRequests[$key])) {
			$result = $this->receivedRequests[$key];
			unset($this->receivedRequests[$key]);
		};
		return $result;
	}
}
