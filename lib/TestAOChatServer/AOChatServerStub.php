<?php

class AOChatServerStub {
	private $process = null;
	private $rpcClient = null;
	private $pipes = array();

	public function startServer($chatPort, $rpcPort) {
		$spec = array(
			1 => array('file', 'nul', 'w')
		);
		$this->process = proc_open("php server.php $chatPort $rpcPort", $spec, $this->pipes, __DIR__, null, array('bypass_shell' => true));
		if (!is_resource($this->process)) {
			throw new Exception("Failed to start aochat server!");
		}
		$this->rpcClient = new JsonRpc\RpcClient("http://127.0.0.1:$rpcPort/");
	}

	public function stopServer() {
		forEach($this->pipes as $pipe) {
			fclose($pipe);
		}
		$this->pipes = array();
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

