<?php

class ClientHandler {
	private $client;
	private $syncId;

	function __construct($client) {
		$this->client = $client;
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
		while ($remaining > 0) { // TODO: it might be wise to have some 
								 // timeout here if the client doesn't sent all needed data
			$data = socket_read($this->client, $remaining);
			if ($data === false) {
				$error = socket_last_error($this->client);
				if($error != 10035)
				{
					// dumps for debugging purposes if stuff fails
					var_dump( $error );
					var_dump( socket_strerror( $error ) );
					return false;
				}
			} else {
				$remaining -= strlen($data);
				$result .= $data;
			}
		}
		return $result;
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