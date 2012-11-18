<?php

class ClientHandler {
	private $client;
	private $syncId;
	private $logger;

	function __construct($client, $logger) {
		$this->client = $client;
		$this->logger = $logger;
	}

	function readPacket() {
		$size = array_pop(unpack("n", $this->readData(2)));
		$data = $this->readData($size);
		$obj = json_decode($data);
		$this->syncId = $obj->syncId;
		return $obj;
	}

	function readData( $byteCount ) {
		$result = '';
		$remaining = $byteCount;
		// TODO: it might be wise to have some timeout here if the client
		// doesn't sent all needed data
		while ($remaining > 0) {
			$data = fread($this->client, $remaining);
			if ($data !== false) {
				$remaining -= strlen($data);
				$result .= $data;
			}
		}
		return $result;
	}

	function writePacket($apiPacket) {
		$apiPacket->syncId = $this->syncId;
		$output = json_encode($apiPacket);
		fwrite($this->client, pack("n", strlen($output)));
		fwrite($this->client, $output);
		$this->close();
	}

	function close() {
		fclose($this->client);
	}

	/**
	 * Returns client's IP-address.
	 */
	public function getClientAddress() {
		$address = stream_socket_get_name($this->client);
		return $address;
	}
}

?>
