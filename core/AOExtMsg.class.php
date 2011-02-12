<?php

  /* New "extended" messages, parser and abstraction.
   * These were introduced in 16.1.  The messages use postscript
   * base85 encoding (not ipv6 / rfc 1924 base85).  They also use
   * some custom encoding and references to further confuse things.
   *
   * Messages start with the magic marker ~& and end with ~
   * Messages begin with two base85 encoded numbers that define
   * the category and instance of the message.  After that there
   * are an category/instance defined amount of variables which
   * are prefixed by the variable type.  A base85 encoded number
   * takes 5 bytes.  Variable types:
   *
   * s: string, first byte is the length of the string
   * i: signed integer (b85)
   * u: unsigned integer (b85)
   * f: float (b85)
   * R: reference, b85 category and instance
   * F: recursive encoding
   * ~: end of message
   *
   */
   
class AOExtMsg {
	
	public $args, $category, $instance, $message_string, $message;

	function __construct($str) {
		$this->read($str);
	}

	function read($msg) {
		if (empty($msg) || substr($msg, 0, 2) != "~&" || substr($msg, -1) != "~") {
			return false;
		}
		$msg = substr($msg, 2, -1);
		$this->category = $this->b85g($msg);
		$this->instance = $this->b85g($msg);
		
		$this->args = AOExtMsg::parse_params($msg);
		if ($this->args === null) {
			echo "Error parsing params for category: '$this->category' instance: '$this->instance' string: '$msg'\n";
		} else {
			$this->message_string = MMDBParser::get_message_string($this->category, $this->instance);
			if ($this->message_string !== null) {
				$this->message = vsprintf($this->message_string, $this->args);
			}
		}
	}
	
	public static function parse_params($msg) {
		$args = array();
		while ($msg != '') {
			$data_type = $msg[0];
			$msg = substr($msg, 1); // skip the data type id
			switch ($data_type) {
				case "S":
					$len = ord($msg[0]) * 256 + ord($msg[1]);
					$str = substr($msg, 2, $len);
					$msg = substr($msg, $len + 2);
					$args[] = $str;
					break;
				
				
				case "s":
					$len = ord($msg[0]) - 1;
					$str = substr($msg, 1, $len);
					$msg = substr($msg, $len + 1);
					$args[] = $str;
					break;

				case "i":
				case "u":
					$num = AOExtMsg::b85g($msg);
					$args[] = $num;
					break;

				case "R":
					$cat = AOExtMsg::b85g($msg);
					$ins = AOExtMsg::b85g($msg);
					$str = MMDBParser::get_message_string($cat, $ins);
					if ($str === null) {
						$str = "Unknown ($cat, $ins)";
					}
					$args[] = $str;
					break;

				default:
					echo "Error! could not parse argument: '$data_type'\n";
					return null;
					break;
			}
		}
		
		return $args;
	}

	public static function b85g(&$str) {
		$n = 0;
		for ($i = 0; $i < 5; $i++) {
			$n = $n * 85 + ord($str[$i]) - 33;
		}
		$str = substr($str, 5);
		return $n;
	}
}

?>
