<?php

class JSONRPCServer extends Evenement\EventEmitter {

	public function __construct($loop, $port, $handler) {

		$socket = new React\Socket\Server($loop);
		$this->httpServer = new React\Http\Server($socket, $loop);

		$this->handler = $handler;
		$that = $this;

		$this->httpServer->on('request', function ($request, $response) use ($that) {
			$request->on('data', function ($data) use ($response, $that) {
			
				// for some reason the server sometimes emits empty $data so
				// try to ignore those
				if (!trim($data)) {
					return;
				}

				$responseRequired = true;
				$rpcResponse = new StdClass();
				$rpcResponse->jsonrpc = '2.0';
				$rpcResponse->id = null;

				try {
					$rpcRequest = json_decode($data);
					if ($rpcRequest === null) {
						throw new Exception('Parse error.', -32700);
					}
					if (!is_object($rpcRequest)) {
						throw new Exception('Invalid Request.', -32600);
					}

					if (isset($rpcRequest->id)) {
						$rpcResponse->id = $rpcRequest->id;
					} else {
						$responseRequired = false;
					}

					if (!isset($rpcRequest->jsonrpc) || $rpcRequest->jsonrpc != '2.0') {
						throw new Exception('Invalid Request.', -32600);
					}
					if (!isset($rpcRequest->method) || !is_string($rpcRequest->method)) {
						throw new Exception('Invalid Request.', -32600);
					}
					if (isset($rpcRequest->params)) {
						if (!is_array($rpcRequest->params)) {
							throw new Exception('Invalid Request.', -32600);
						}
					} else {
						$rpcRequest->params = array();
					}
					$callee = array($that->handler, $rpcRequest->method);
					if (!is_callable($callee)) {
						throw new Exception("Method '{$rpcRequest->method}' not found.", -32601);
					}
					$rpcResponse->result = call_user_func_array($callee, $rpcRequest->params);

				} catch(Exception $e) {
					$rpcResponse->error = new StdClass();
					$rpcResponse->error->code = $e->getCode();
					$rpcResponse->error->message = $e->getMessage();
				}

				if ($responseRequired) {
					$jsonResponse = json_encode($rpcResponse);
					$response->writeHead(200);
					$response->end($jsonResponse);
				} else {
					$response->writeHead(200);
					$response->end();
				}
			});
		});

		$socket->listen($port);
	}
}

