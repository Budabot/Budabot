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
	function make_blob($name, $content, $style = NULL) {
		global $chatBot;
		
		// escape double quotes
		$content = str_replace('"', '&quot;', $content);
		
		if (is_string($content)) {
			$content = Text::format_message($content);
			$content = str_replace('<pagebreak>', '', $content);
			
			if (strlen($content) > $chatBot->settings["max_blob_size"]) {  //Split the windows if they are too big
				// split on linebreaks
				$array = explode("\n", $content);
				$pagebreak = false;
				
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
				return "<a $style href=\"text://".$chatBot->settings["default_window_color"].$content."\">$name</a>";
			}
		} else if (is_array($content)) { // Format retaining blob.
			/**
			 * $content is expected to be delivered in the following format:
			 * 
			 * $content => array(
			 *  [0] => array(
			 *   'header' => "", //If not set, uses ""
			 *   'content' => "",
			 *   'footer' => "", //If not set, uses ""
			 *   'footer_incomplete' => "", //If not set, uses ""
			 *   'header_incomplete' => "" //If not set, uses 'header'
			 *   )
			 *  [..] => array(..)
			 * )
			 * 
			 * This algorithm will attempt to split a large blob into multiple pages between indices if possible.  It will split up to 500 characters early to preserve formatting.
			 * If it must split in the middle of content then it will append 'footer_incomplete' at the end of the first blob, 'header_incomplete' at the start of the second blob and continue the content.
			 * This algorithm will always split on a line.
			 * In the event that 'footer_incomplete' is not defined, it is treated as blank.  In the event that 'header_incomplete' is not defined, 'header' will be used instead.
			 * 
			 * Note: In the event that 'footer' would go over a blob limit and be placed in another blob, 'footer' is omitted entirely.  'footer' should be used *only* to close up formatting tags or blank spaces.
			 * Note: If an entry in the $content array is a string, it is treated as though the string is the content and there are is no attached 'header' or 'footer'.
			 * 
			 * 
			 * Sample 1:
			 * Blob: <header><content><footer>
			 * 
			 * Sample 2:
			 * Blob #1: <header><content up to character 7000><footer_incomplete>
			 * Blob #2: <header_incomplete><content from 7001 - 1000><footer>
			 * 
			 */
			
			$content = Text::format_message($content); //Budabot markup -> AOML (yay for str_ireplace innately handling arrays!)
			
			$output = "";
			$outputArr = array();
			
			forEach ($content as $index => $arr) {
				if (empty($arr) || (empty($arr['content']) && empty($arr['header']))) {
					continue; //Skip it if it's empty
				}
				
				// Make sure all the values of the current array are set (avoid any odd NULL output)
				if (!is_array($arr)) { $arr = array("content" => $arr); }
				if (isset($arr[0])) { $arr['header'] = $arr[0]; }
				if (isset($arr[1])) { $arr['content'] = $arr[1]; }
				if (isset($arr[2])) { $arr['footer'] = $arr[2]; }
				if (!isset($arr['header'])) { $arr['header'] = ""; }
				if (!isset($arr['footer'])) { $arr['footer'] = ""; }
				if (!isset($arr['header_incomplete'])) { $arr['header_incomplete'] = $arr['header']; }
				if (!isset($arr['footer_incomplete'])) { $arr['footer_incomplete'] = ""; }
				
				$nextCount = strlen($output) + strlen($arr['header']) + strlen($arr['content']) + strlen($arr['footer']); //Character count if header+content+footer are added
				
				if ($nextCount < $chatBot->settings["max_blob_size"]) {
					//If it's less than max_blob_size still, we're good
					$output .= $arr['header'] . $arr['content'] . $arr['footer'];
				} else if ($nextCount - 500 < $chatBot->settings["max_blob_size"] && strlen($output) >= ($chatBot->settings["max_blob_size"] / 2)) {
					//If less than 500 characters over the cap, we go ahead and move the entire section into the next page (but only if the current page has >= half its max size used in content already)
					$outputArr[] = $output; //Stick the current page into our output array
					$output = "<header>::::: $name Page " . (count($outputArr) + 1) . " :::::<end>\n\n" . $arr['header'] . $arr['content'] . $arr['footer']; //And start the new page
				} else {
					//Alright, looks like we're splitting the section over multiple pages
					if (strlen($output) + strlen($arr['header']) < $chatBot->settings["max_blob_size"]) {
						$output .= $arr['header'];
					} else {
						//New page (simplest split)
						$outputArr[] = $output;
						$output = "<header>::::: $name Page " . (count($outputArr) + 1) . " :::::<end>\n\n" . $arr['header'];
					}
					
					//Now for the content
					if (strlen($output) + strlen($arr['content']) < $chatBot->settings["max_blob_size"]) {
						$output .= $arr['content'];
					} else {
						// Split the content into sections based off newlines and <pagebreak> tags
						$cArrN = explode("\n", $arr['content']);
						$incNewline = array();
						$cArr = array();
						forEach ($cArrN as $str) {
							$a = explode("<pagebreak>", $str);
							if (count($a) == 1) {
								$cArr[] = $str;
								$incNewline[] = "\n";
							} else {
								for ($i = 0; $i < count($a); $i++) {
									$cArr[] = $a[$i];
									if ($i + 1 != count($a)) {
										$incNewline[] = "";
									} else {
										$incNewline[] = "\n";
									}
								}
							}
						}
						
						$i = 0;
						
						// Process all the sections of the content
						while ($i < count($cArr)) {
							$str = $cArr[$i];
							if (strlen($output) + strlen($str) + strlen($arr['footer_incomplete']) < $chatBot->settings["max_blob_size"]) {
								//We have room to add another line before splitting
								if ($i + 1 != count($cArr)) {
									$output .= $str . $incNewline[$i];
								} else {
									$output .= $str;
								}
								$i++;
							} else {
								$output .= $arr['footer_incomplete'];
								$outputArr[] = $output;
								$output = "<header>::::: $name Page " . (count($outputArr) + 1) . " :::::<end>\n\n" . $arr['header_incomplete'];
							}
						}
					}
					
					//Now for the footer
					if (strlen($output) + strlen($arr['footer']) < $chatBot->settings["max_blob_size"]) {
						$output .= $arr['footer'];
					} else {
						//This is a tricky one.  footers should have formatting ending tags only.
						// So for now, we will leave the footer off if it comes last (everything important should be in content)
						$outputArr[] = $output;
						$output = "<header>::::: $name Page " . (count($outputArr) + 1) . " :::::<end>\n\n";
					}
				}
				
			}
			
			if (!empty($output))
				$outputArr[] = $output;
			
			// Turn all pages into clickable blobs
			foreach ($outputArr as $index => $page) {
				if (count($outputArr) > 1) {
					if (count($outputArr) == $index + 1) {
						$outputArr[$index] = "<a $style href=\"text://".$chatBot->settings["default_window_color"].str_replace("<pagebreak>", "",$page)."\">$name</a> (Page <highlight>" . ($index + 1) . " - End<end>)";
					} else {
						$outputArr[$index] = "<a $style href=\"text://".$chatBot->settings["default_window_color"].str_replace("<pagebreak>", "",$page)."\">$name</a> (Page <highlight>" . ($index + 1) . "<end>)";
					}
				} else {
					$outputArr = "<a $style href=\"text://".$chatBot->settings["default_window_color"].str_replace("<pagebreak>", "",$page)."\">$name</a>";
				}
			}
			
			return $outputArr; //Return the result
		}
	}
	
	/**	
	 * @name: make_link
	 * @description: creates a clickable link
	 */
	function make_link($name, $content, $type, $style = NULL) {
		global $chatBot;
		
		// escape double quotes
		$content = str_replace('"', '&quot;', $content);

		if ($type == "text") { // Majic link.
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
		
		$array = array(
			"<header>" => $chatBot->settings['default_header_color'],
			"<highlight>" => $chatBot->settings['default_highlight_color'],
			"<black>" => "<font color='#000000'>",
			"<white>" => "<font color='#FFFFFF'>",
			"<yellow>" => "<font color='#FFFF00'>",
			"<blue>" => "<font color='#8CB5FF'>",
			"<green>" => "<font color='#00DE42'>",
			"<red>" => "<font color='#ff0000'>",
			"<orange>" => "<font color='#FCA712'>",
			"<grey>" => "<font color='#C3C3C3'>",
			"<cyan>" => "<font color='#00FFFF'>",
			
			"<neutral>" => $chatBot->settings['default_neut_color'],
			"<omni>" => $chatBot->settings['default_omni_color'],
			"<clan>" => $chatBot->settings['default_clan_color'],
			"<unknown>" => $chatBot->settings['default_unknown_color'],

			"<myname>" => $chatBot->vars["name"],
			"<myguild>" => $chatBot->vars["my_guild"],
			"<tab>" => "    ",
			"<end>" => "</font>",
			"<symbol>" => $chatBot->settings["symbol"]);
		
		$message = str_ireplace(array_keys($array), array_values($array), $message);

		return $message;
	}
}

?>
