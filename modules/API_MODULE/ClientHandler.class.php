<?php

class ClientHandler {
	private $client;
	private $syncId;

	function __construct($client) {
		$this->client = $client;
	}

	function readPacket() {
		$size = array_pop(unpack("n", socket_read($this->client, 2)));
		$obj = json_decode(socket_read($this->client, $size));
		$this->syncId = $obj->syncId;
		return $obj;
	}

	function writePacket($apiPacket) {
		$apiPacket->syncId = $this->syncId;
		$output = json_encode($apiPacket);
		socket_write($this->client, pack("n", strlen($output)));
		socket_write($this->client, $output);
		$this->close();
	}
	
	function close() {
		socket_close($this->client);
	}
}

?>