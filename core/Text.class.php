<?php

class Text {
	
	/**	
	 * @name: make_header
	 * @description: creates a formatted header to go in a blob
	 */
	public static function make_header($title, $links = NULL) {
		global $chatBot;
	
		// if !$links, then make_header function will show default links:  Help, About, Download.
	        // if $links = "none", then make_header wont show ANY links.
		// if $links = array("Help;chatcmd:///tell <myname> help"),  slap in your own array for your own links.

		$color = $chatBot->settings['default_header_color'];
		$baseR = hexdec(substr($color,14,2)); $baseG = hexdec(substr($color,16,2)); $baseB = hexdec(substr($color,18,2));
		$color2 = "<font color='#".strtoupper(substr("00".dechex($baseR*.75),-2).substr("00".dechex($baseG*.75),-2).substr("00".dechex($baseB*.75),-2))."'>";
		$color3 = "<font color='#".strtoupper(substr("00".dechex($baseR*.50),-2).substr("00".dechex($baseG*.50),-2).substr("00".dechex($baseB*.50),-2))."'>";
		$color4 = "<font color='#".strtoupper(substr("00".dechex($baseR*.25),-2).substr("00".dechex($baseG*.25),-2).substr("00".dechex($baseB*.25),-2))."'>";

		//Title
		$header = $color4.":::".$color3.":::".$color2.":::".$color;
		$header .= $title;
		$header .= "</font>:::</font>:::</font>:::</font> ";


		if (!$links) {
			$links = array( "Help;chatcmd:///tell ".$chatBot->vars["name"]." help",
					"About;chatcmd:///tell ".$chatBot->vars["name"]." about",
					"Download;chatcmd:///start http://budabot.aodevs.com/index.php?page=14");
		}
		if (strtolower($links) != "none") {
			forEach ($links as $link){
				preg_match("/^(.+);(.+)$/i", $link, $arr);
				if ($arr[1] && $arr[2]) {
					$header .= $color4.":".$color3.":".$color2.":";
					$header .= "<a style='text-decoration:none' href='$arr[2]'>".$color."$arr[1]</font></a>";
					$header .= ":</font>:</font>:</font>";
				}
			}
		}

		$header .= $chatBot->settings["default_window_color"]."\n\n";

		return $header;
	}
	
	/**	
	 * @name: make_link
	 * @description: creates a clickable link
	 */
	function make_link($name, $content, $type = "blob", $style = NULL){
		global $chatBot;
		
		// escape double quotes
		$content = str_replace('"', '&quot;', $content);

		if ($type == "blob") { // Normal link.
			if (strlen($content) > $chatBot->settings["max_blob_size"]) {  //Split the windows if they are too big
				$array = explode("<pagebreak>", $content);
				$pagebreak = true;
				
				// if the blob hasn't specified how to split it, split on linebreaks
				if (count($array) == 1) {
					$array = explode("\n", $content);
					$pagebreak = false;
				}
				$page = 1;
				$page_size = 0;
			  	forEach ($array as $line) {
					// preserve newline char if we split on newlines
					if ($pagebreak == false) {
						$line .= "\n";
					}
					$line_length = strlen($line);
					if ($page_size + $line_length < $chatBot->settings["max_blob_size"]) {
						$result[$page] .= $line;
						$page_size += $line_length;
				    } else {
						$result[$page] = "<a $style href=\"text://".$chatBot->settings["default_window_color"].$result[$page]."\">$name</a> (Page <highlight>$page<end>)";
				    	$page++;
						
						$result[$page] .= "<header>::::: $name Page $page :::::<end>\n\n";
						$result[$page] .= $line;
						$page_size = strlen($result[$page]);
					}
				}
				$result[$page] = "<a $style href=\"text://".$chatBot->settings["default_window_color"].$result[$page]."\">$name</a> (Page <highlight>$page - End<end>)";
				return $result;
			} else {
				$content = str_replace('<pagebreak>', '', $content);
				return "<a $style href=\"text://".$chatBot->settings["default_window_color"].$content."\">$name</a>";
			}
		} else if ($type == "text") { // Majic link.
			$content = str_replace("'", '&#39;', $content);
			return "<a $style href='text://$content'>$name</a>";
		} else if ($type == "chatcmd") { // Chat command.
			$content = str_replace("'", '&#39;', $content);
			return "<a $style href='chatcmd://$content'>$name</a>";
		} else if ($type == "user") { // Adds support for right clicking usernames in chat, providing you with a menu of options (ignore etc.) (see 18.1 AO patchnotes)
			$content = str_replace("'", '&#39;', $content);
			return "<a $style href='user://$content'>$name</a>";
		}
	}
	
	/**	
	 * @name: make_item
	 * @description: creates an item link
	 */
	public static function make_item($lowId, $highId,  $ql, $name){
		return "<a href='itemref://{$lowId}/{$highId}/{$ql}'>{$name}</a>";
	}
	
	/**	
	 * @name: make_item
	 * @description: creates an item link
	 */
	public static function make_image($imageId){
		return "<img src='rdb://{$imageId}'>";
	}
	
	/**	
	 * @name: format_message
	 * @description: formats a message with colors, bot name, symbol, etc
	 */
	public static function format_message($message) {
		global $chatBot;
	
		// Color
		$message = str_ireplace("<header>", $chatBot->settings['default_header_color'], $message);
		$message = str_ireplace("<highlight>", $chatBot->settings['default_highlight_color'], $message);
		$message = str_ireplace("<black>", "<font color='#000000'>", $message);
		$message = str_ireplace("<white>", "<font color='#FFFFFF'>", $message);
		$message = str_ireplace("<yellow>", "<font color='#FFFF00'>", $message);
		$message = str_ireplace("<blue>", "<font color='#8CB5FF'>", $message);
		$message = str_ireplace("<green>", "<font color='#00DE42'>", $message);
		$message = str_ireplace("<red>", "<font color='#ff0000'>", $message);
		$message = str_ireplace("<orange>", "<font color='#FCA712'>", $message);
		$message = str_ireplace("<grey>", "<font color='#C3C3C3'>", $message);
		$message = str_ireplace("<cyan>", "<font color='#00FFFF'>", $message);
		
		$message = str_ireplace("<neutral>", $chatBot->settings['default_neut_color'], $message);
		$message = str_ireplace("<omni>", $chatBot->settings['default_omni_color'], $message);
		$message = str_ireplace("<clan>", $chatBot->settings['default_clan_color'], $message);
		$message = str_ireplace("<unknown>", $chatBot->settings['default_unknown_color'], $message);

		$message = str_ireplace("<myname>", $chatBot->vars["name"], $message);
		$message = str_ireplace("<myguild>", $chatBot->vars["my guild"], $message);
		$message = str_ireplace("<tab>", "    ", $message);
		$message = str_ireplace("<end>", "</font>", $message);
		$message = str_ireplace("<symbol>", $chatBot->settings["symbol"] , $message);

		return $message;
	}
}

?>
