<?php

/**
 * @Instance
 */
class Text {

	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $setting;
	
	/** @Logger */
	public $logger;

	public function make_header_links($links) {
		$output = '';
		forEach ($links as $title => $command){
			$output .= " ::: " . $this->make_chatcmd($title, $command, 'style="text-decoration:none;"') . " ::: ";
		}
		return $output;
	}

	/**
	 * @name: make_blob
	 * @description: creates an info window
	 */
	function make_blob($name, $content, $header = NULL) {
		if ($header === null) {
			$header = $name;
		}

		// escape double quotes
		$content = str_replace('"', '&quot;', $content);
		$header = str_replace('"', '&quot;', $header);

		$content = $this->format_message($content);

		$pages = $this->paginate($content, $this->setting->get("max_blob_size"), array("<pagebreak>", "\n", " "));
		$num = count($pages);

		if ($num == 1) {
			$page = $pages[0];
			$headerMarkup = "<header> :::::: $header :::::: <end>\n\n";
			$page = "<a href=\"text://".$this->setting->get("default_window_color").$headerMarkup.$page."\">$name</a>";
			return $page;
		} else {
			$i = 1;
			forEach ($pages as $key => $page) {
				$headerMarkup = "<header> :::::: $header (Page $i / $num) :::::: <end>\n\n";
				$page = "<a href=\"text://".$this->setting->get("default_window_color").$headerMarkup.$page."\">$name</a> (Page <highlight>$i / $num<end>)";
				$pages[$key] = $page;
				$i++;
			}
			return $pages;
		}
	}

	function make_legacy_blob($name, $content) {
		// escape double quotes
		$content = str_replace('"', '&quot;', $content);

		$content = $this->format_message($content);

		$pages = $this->paginate($content, $this->setting->get("max_blob_size"), array("<pagebreak>", "\n", " "));
		$num = count($pages);

		if ($num == 1) {
			$page = $pages[0];
			$page = "<a href=\"text://".$this->setting->get("default_window_color").$page."\">$name</a>";
			return $page;
		} else {
			$i = 1;
			forEach ($pages as $key => $page) {
				if ($i > 1) {
					$header = "<header> :::::: $name (Page $i / $num) :::::: <end>\n\n";
				} else {
					$header = '';
				}
				$page = "<a href=\"text://".$this->setting->get("default_window_color").$header.$page."\">$name</a> (Page <highlight>$i / $num<end>)";
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
					$newResult = $this->paginate($line, $maxLength, $symbols);
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
	 * @name: make_image
	 * @description: creates an image.
	 * @param $imageId id of the image
	 * @param $db (optional) image database to use, by default uses rdb
	 */
	public function make_image($imageId, $db = "rdb") {
		return "<img src='{$db}://{$imageId}'>";
	}

	/**
	 * @name: format_message
	 * @description: formats a message with colors, bot name, symbol, etc
	 */
	public function format_message($message) {
		$array = array(
			"<header>" => $this->setting->get('default_header_color'),
			"<header2>" => $this->setting->get('default_header2_color'),
			"<highlight>" => $this->setting->get('default_highlight_color'),
			"<black>" => "<font color='#000000'>",
			"<white>" => "<font color='#FFFFFF'>",
			"<yellow>" => "<font color='#FFFF00'>",
			"<blue>" => "<font color='#8CB5FF'>",
			"<green>" => "<font color='#00DE42'>",
			"<red>" => "<font color='#ff0000'>",
			"<orange>" => "<font color='#FCA712'>",
			"<grey>" => "<font color='#C3C3C3'>",
			"<cyan>" => "<font color='#00FFFF'>",
			"<violet>" => "<font color='#8F00FF'>",

			"<neutral>" => $this->setting->get('default_neut_color'),
			"<omni>" => $this->setting->get('default_omni_color'),
			"<clan>" => $this->setting->get('default_clan_color'),
			"<unknown>" => $this->setting->get('default_unknown_color'),

			"<myname>" => $this->chatBot->vars["name"],
			"<myguild>" => $this->chatBot->vars["my_guild"],
			"<tab>" => "    ",
			"<end>" => "</font>",
			"<symbol>" => $this->setting->get("symbol"));

		$message = str_ireplace(array_keys($array), array_values($array), $message);

		return $message;
	}
}

?>
