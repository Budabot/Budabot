<?php

require_once __DIR__ . '/../Process.class.php';

class AOChatServerStub {
	private $process = null;
	private $rpcClient = null;

	public function startServer($chatPort, $rpcPort, $logfile) {
		$this->process = new Process();
		$this->process->setCommand("php server.php $chatPort $rpcPort");

		if (is_string($logfile)) {
			$file = fopen($logfile, 'w');
			$this->process->setDescriptorspec(array(
				1 => $file,
				2 => $file
			));
		} else if ($logfile) {
			$this->process->setDescriptorspec(array());
		} else {
			$this->process->setDescriptorspec(array(
				1 => array('file', 'nul', 'w'),
				2 => array('file', 'nul', 'w')
			));
		}

		$this->process->setWorkingDir(__DIR__);
		if (!$this->process->start()) {
			throw new Exception("Failed to start aochat server!");
		}

		// wait until the aochat server's json-rpc interface is accessible
		$timeout = time() + 300;
		do {
			$socket = @fsockopen('127.0.0.1', $rpcPort, $errno, $errstr, 1);
			if (time() > $timeout) {
				throw new Exception("Failed to connect to aochat server's rpc-port ($rpcPort): $errstr ($errno)!");
			}
		} while($socket === false);
		fclose($socket);

		$this->rpcClient = new JsonRpc\RpcClient("http://127.0.0.1:$rpcPort/");
	}

	public function stopServer() {
		if ($this->process) {
			$this->process->stop();
		}

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

