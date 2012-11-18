<?php

/**
 * @author: Tshaar (RK2)
 */
class Teamspeak3 {
	private $username;
	private $password;
	private $address;
	private $port;
	private $serverId;

	public function __construct($username, $password, $address = '127.0.0.1', $port = 10011, $serverId = 1) {
		$this->username = $username; // Set to false for guest access
		$this->password = $password;
		$this->address = $address;
		$this->port = $port;
		$this->serverId = $serverId;
	}

	// Send data to the stream and fetch output
	public function exec($data) {
		$this->connect();
		fputs($this->stream, $data . "\n");
		fputs($this->stream, "quit\n");
		while (!feof($this->stream)) {
			$data = explode('|', fgets($this->stream));
			if (substr($data[0], 1, 8) != 'error id' && substr($data[0], 1, 7) != 'Welcome' && substr($data[0], 1, 2) != 'S3') {
				forEach ($data as $outputVar) {
					$outputLine = explode(' ', $outputVar);
					unset($outputVar);
					forEach ($outputLine as $line) {
						$fragment = explode('=', $line);
						@$outputVar[trim($fragment[0])] = trim($fragment[1]);
					}
					$finalData[] = $outputVar;
				}
			}
		}
		fclose($this->stream);
		if (@$finalData) {
			array_pop($finalData);
			return $finalData;
		} else {
			throw new Exception('Invalid TS3 query.');
		}
	}

	// Open the stream
	public function connect() {
		$stream = fsockopen($this->address, $this->port, $errno, $errstr, 5);
		$this->stream = $stream;
		if ($this->stream) {
			if ($this->username) {
				fputs($this->stream, "login client_login_name=". $this->username ." client_login_password=". $this->password ."\n");
			}
			fputs($this->stream, "use ". $this->serverId ."\n");
		} else {
			throw new Exception('Unable to connect to the TS3 server.');
		}
	}

	// Close the stream
	public function __destruct() {
		fclose($this->stream);
	}
}

?>
