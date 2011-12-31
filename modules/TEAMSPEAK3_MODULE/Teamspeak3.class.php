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

function getTeamspeak3Status() {
	global $chatBot;
	$setting = $chatBot->getInstance('setting');
	$ts = new Teamspeak3($setting->get('ts_username'), $setting->get('ts_password'), $setting->get('ts_server'), $setting->get('ts_queryport'));

	try {
		$server = $setting->get('ts_server');
		$clientPort = $setting->get('ts_clientport');
		$serverLink = Text::make_chatcmd($server, "/start http://ts3server:://$server:$clientPort");
		
		$users = $ts->exec('clientlist');
		$count = 0;
		$blob = "<header> :::::: Teamspeak 3 Info :::::: <end>\n\n";
		$blob .= "Server: $serverLink\n";
		$blob .= "Description: <highlight>" . $setting->get('ts_description') . "<end>\n\n";
		$blob .= "Users:\n";
		forEach ($users as $user) {
			if ($user['client_type'] == 0) {
				$blob .= "<highlight>{$user['client_nickname']}<end>\n";
				$count++;
			}
		}
		if ($count == 0) {
			$blob .= "<i>No users connected</i>\n";
		}
		$blob .= "\n\nTeamspeak 3 support by Tshaar (RK2)";
		$msg = Text::make_blob("{$count} user(s) on Teamspeak", $blob);
	} catch (Exception $e) {
		$msg = "Error! " . $e->getMessage();
	}
	
	return $msg;
}

?>
