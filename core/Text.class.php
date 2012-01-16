<?php

class Text {
	
	/**	
	 * @name: make_header
	 * @description: creates a formatted header to go in a blob
	 */
	public function make_header($title, $links = NULL) {
		$chatBot = Registry::getInstance('chatBot');
		$setting = Registry::getInstance('setting');

		$color = $setting->get('default_header_color');
		$baseR = hexdec(substr($color,14,2));
		$baseG = hexdec(substr($color,16,2));
		$baseB = hexdec(substr($color,18,2));
		$color2 = "<font color='#".strtoupper(substr("00".dechex($baseR*.75),-2).substr("00".dechex($baseG*.75),-2).substr("00".dechex($baseB*.75),-2))."'>";
		$color3 = "<font color='#".strtoupper(substr("00".dechex($baseR*.60),-2).substr("00".dechex($baseG*.60),-2).substr("00".dechex($baseB*.60),-2))."'>";
		$color4 = "<font color='#".strtoupper(substr("00".dechex($baseR*.45),-2).substr("00".dechex($baseG*.45),-2).substr("00".dechex($baseB*.45),-2))."'>";

		//Title
		$header = $color4.":::".$color3.":::".$color2.":::".$color;
		$header .= " $title ";
		$header .= "</font>:::</font>:::</font>:::</font>\n\n";

		if ($links !== NULL) {
			forEach ($links as $title => $command){
				$header .= " ::: " . Text::make_chatcmd($title, $command, 'style="text-decoration:none;"') . " ::: ";
			}
			$header .= "\n\n";
		}

		return $header;
	}
	
	public function make_header_links($links) {
		$output = '';
		forEach ($links as $title => $command){
			$output .= " ::: " . Text::make_chatcmd($title, $command, 'style="text-decoration:none;"') . " ::: ";
		}
		return $output;
	}
	
	/**	
	 * @name: make_blob
	 * @description: creates an info window
	 */
	function make_blob($name, $content, $header = NULL) {
		$setting = Registry::getInstance('setting');
		
		if ($header === null) {
			$header = $name;
		}

		// escape double quotes
		$content = str_replace('"', '&quot;', $content);
		
		$content = Text::format_message($content);
		
		$pages = Text::paginate($content, $setting->get("max_blob_size"), array("<pagebreak>", "\n", " "));
		$num = count($pages);
		
		if ($num == 1) {
			$page = $pages[0];
			$header = "<header> :::::: $header :::::: <end>\n\n";
			$page = "<a href=\"text://".$setting->get("default_window_color").$header.$page."\">$name</a>";
			return $page;
		} else {
			$i = 1;
			forEach ($pages as $key => $page) {
				$headerMarkup = "<header> :::::: $header (Page $i / $num) :::::: <end>\n\n";
				$page = "<a href=\"text://".$setting->get("default_window_color").$headerMarkup.$page."\">$name</a> (Page <highlight>$i / $num<end>)";
				$pages[$key] = $page;
				$i++;
			}
			return $pages;
		}
	}
	
	function make_legacy_blob($name, $content) {
		$setting = Registry::getInstance('setting');

		// escape double quotes
		$content = str_replace('"', '&quot;', $content);
		
		$content = Text::format_message($content);
		
		$pages = Text::paginate($content, $setting->get("max_blob_size"), array("<pagebreak>", "\n", " "));
		$num = count($pages);
		
		if ($num == 1) {
			$page = $pages[0];
			$page = "<a href=\"text://".$setting->get("default_window_color").$page."\">$name</a>";
			return $page;
		} else {
			$i = 1;
			forEach ($pages as $key => $page) {
				if ($i > 1) {
					$header = "<header> :::::: $name (Page $i / $num) :::::: <end>\n\n";
				} else {
					$header = '';
				}
				$page = "<a href=\"text://".$setting->get("default_window_color").$header.$page."\">$name</a> (Page <highlight>$i / $num<end>)";
				$pages[$key] = $page;
				$i++;
			}
			return $pages;
		}
	}
	
	function paginate($input, $maxLength, $symbols) {
		$pageSize = 0;
		$currentPage = '';
		$result = array();
		$symbol = array_shift($symbols);

		$lines = explode($symbol, $input);
		forEach ($lines as $line) {
			if ($symbol == "\n") {
				$line .= "\n";
			}
			
			$lineLength = strlen($line);
			if ($lineLength > $maxLength) {
				if ($pageSize != 0) {
					$result []= $currentPage;
					$currentPage = '';
					$pageSize = 0;
				}

				if (count($symbols) > 0) {
					$newResult = Text::paginate($line, $maxLength, $symbols);
					$result = array_merge($result, $newResult);
				} else {
					LegacyLogger::log('ERROR', 'Text', "Could not successfully page blob");
					$result []= $line;
				}
			} else if ($pageSize + $lineLength < $maxLength) {
				$currentPage .= $line;
				$pageSize += $lineLength;
			} else {
				$result []= $currentPage;
				$currentPage = $line;
				$pageSize = $lineLength;
			}
		}
		
		if ($pageSize > 0) {
			$result []= $currentPage;
		}

		return $result;
	}
	
	/**	
	 * @name: make_structured_blob
	 * @description: creates an info window
	 */
	function make_structured_blob($name, $content, $style = NULL) {
		$chatBot = Registry::getInstance('chatBot');
		$setting = Registry::getInstance('setting');

		// escape double quotes
		$content = str_replace('"', '&quot;', $content);
		
		// Format retaining blob.
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
		 * Blob #2: <header_incomplete><content from 7001 - 10000><footer>
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
			
			if ($nextCount < $setting->get("max_blob_size")) {
				//If it's less than max_blob_size still, we're good
				$output .= $arr['header'] . $arr['content'] . $arr['footer'];
			} else if ($nextCount - 500 < $setting->get("max_blob_size") && strlen($output) >= ($setting->get("max_blob_size") / 2)) {
				//If less than 500 characters over the cap, we go ahead and move the entire section into the next page (but only if the current page has >= half its max size used in content already)
				$outputArr[] = $output; //Stick the current page into our output array
				$output = "<header>::::: $name Page " . (count($outputArr) + 1) . " :::::<end>\n\n" . $arr['header'] . $arr['content'] . $arr['footer']; //And start the new page
			} else {
				//Alright, looks like we're splitting the section over multiple pages
				if (strlen($output) + strlen($arr['header']) < $setting->get("max_blob_size")) {
					$output .= $arr['header'];
				} else {
					//New page (simplest split)
					$outputArr[] = $output;
					$output = "<header>::::: $name Page " . (count($outputArr) + 1) . " :::::<end>\n\n" . $arr['header'];
				}
				
				//Now for the content
				if (strlen($output) + strlen($arr['content']) < $setting->get("max_blob_size")) {
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
						if (strlen($output) + strlen($str) + strlen($arr['footer_incomplete']) < $setting->get("max_blob_size")) {
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
				if (strlen($output) + strlen($arr['footer']) < $setting->get("max_blob_size")) {
					$output .= $arr['footer'];
				} else {
					//This is a tricky one.  footers should have formatting ending tags only.
					// So for now, we will leave the footer off if it comes last (everything important should be in content)
					$outputArr[] = $output;
					$output = "<header>::::: $name Page " . (count($outputArr) + 1) . " :::::<end>\n\n";
				}
			}
			
		}
		
		if (!empty($output)) {
			$outputArr[] = $output;
		}
		
		// Turn all pages into clickable blobs
		forEach ($outputArr as $index => $page) {
			if (count($outputArr) > 1) {
				if (count($outputArr) == $index + 1) {
					$outputArr[$index] = "<a $style href=\"text://".$setting->get("default_window_color").str_replace("<pagebreak>", "", $page)."\">$name</a> (Page <highlight>" . ($index + 1) . " - End<end>)";
				} else {
					$outputArr[$index] = "<a $style href=\"text://".$setting->get("default_window_color").str_replace("<pagebreak>", "", $page)."\">$name</a> (Page <highlight>" . ($index + 1) . "<end>)";
				}
			} else {
				$outputArr = "<a $style href=\"text://".$setting->get("default_window_color").str_replace("<pagebreak>", "", $page)."\">$name</a>";
			}
		}
		
		return $outputArr; //Return the result
	}
	
	/**
	 * @name: make_chatcmd
	 * @description: creates a chatcmd link
	 * @param: $name - the name the link will show
	 * @param: $content - the chatcmd to execute
	 * @param: $style (optional) - any styling you want applied to the link
	 */
	function make_chatcmd($name, $content, $style = NULL) {
		$content = str_replace('"', '&quot;', $content);
		$content = str_replace("'", '&#39;', $content);
		return "<a $style href='chatcmd://$content'>$name</a>";
	}
	
	/**
	 * @name: make_userlink
	 * @description: creates a user link which adds support for right clicking usernames in chat, providing you with a menu of options (ignore etc.) (see 18.1 AO patchnotes)
	 * @param: $name - the name the user to create a link for
	 * @param: $style (optional) - any styling you want applied to the link
	 */
	function make_userlink($user, $style = NULL) {
		return "<a $style href='user://$user'>$user</a>";
	}
	
	/**	
	 * @name: make_item
	 * @description: creates an item link
	 */
	public function make_item($lowId, $highId,  $ql, $name) {
		return "<a href='itemref://{$lowId}/{$highId}/{$ql}'>{$name}</a>";
	}
	
	/**	
	 * @name: make_item
	 * @description: creates an item link
	 */
	public function make_image($imageId) {
		return "<img src='rdb://{$imageId}'>";
	}
	
	/**	
	 * @name: format_message
	 * @description: formats a message with colors, bot name, symbol, etc
	 */
	public function format_message($message) {
		$chatBot = Registry::getInstance('chatBot');
		$setting = Registry::getInstance('setting');
		
		$array = array(
			"<header>" => $setting->get('default_header_color'),
			"<highlight>" => $setting->get('default_highlight_color'),
			"<black>" => "<font color='#000000'>",
			"<white>" => "<font color='#FFFFFF'>",
			"<yellow>" => "<font color='#FFFF00'>",
			"<blue>" => "<font color='#8CB5FF'>",
			"<green>" => "<font color='#00DE42'>",
			"<red>" => "<font color='#ff0000'>",
			"<orange>" => "<font color='#FCA712'>",
			"<grey>" => "<font color='#C3C3C3'>",
			"<cyan>" => "<font color='#00FFFF'>",
			
			"<neutral>" => $setting->get('default_neut_color'),
			"<omni>" => $setting->get('default_omni_color'),
			"<clan>" => $setting->get('default_clan_color'),
			"<unknown>" => $setting->get('default_unknown_color'),

			"<myname>" => $chatBot->vars["name"],
			"<myguild>" => $chatBot->vars["my_guild"],
			"<tab>" => "    ",
			"<end>" => "</font>",
			"<symbol>" => $setting->get("symbol"));
		
		$message = str_ireplace(array_keys($array), array_values($array), $message);

		return $message;
	}
}

?>
