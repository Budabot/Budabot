<?php

class AOChatServerStub {
	private $process = null;
	private $rpcClient = null;

	public function startServer($chatPort, $rpcPort) {
		$this->process = proc_open("php server.php $chatPort $rpcPort", array(), $pipes, __DIR__, null, array('bypass_shell' => true));
		if (!is_resource($this->process)) {
			throw new Exception("Failed to start aochat server!");
		}
		$this->rpcClient = new JsonRpc\RpcClient("http://127.0.0.1:$rpcPort/");
	}

	public function stopServer() {
		proc_terminate($this->process);
		$this->process = null;
		$this->rpcClient = null;
	}

	public function __call($method, $arguments) {
		if (!$this->rpcClient) {
			throw new Exception("No such method, or rpc client/server not running!");
		}

		$response = $this->rpcClient->__call($method, $arguments);
		if (isset($response->error)) {
			throw new Exception($response->error->message, $response->error->code);
		}
		return $response->result;
	}
}

