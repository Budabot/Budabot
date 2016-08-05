<?php

namespace Budabot\Core;

/**
 * @Instance
 */
class Text {

	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $settingManager;
	
	/** @Logger */
	public $logger;

	public function makeHeaderLinks($links) {
		$output = '';
		forEach ($links as $title => $command){
			$output .= " ::: " . $this->makeChatcmd($title, $command, 'style="text-decoration:none;"') . " ::: ";
		}
		return $output;
	}

	/**
	 * @name: makeBlob
	 * @description: creates an info window
	 */
	public function makeBlob($name, $content, $header = null) {
		if ($header === null) {
			$header = $name;
		}

		// trim extra whitespace from beginning and ending
		$content = trim($content);

		// escape double quotes
		$content = str_replace('"', '&quot;', $content);
		$header = str_replace('"', '&quot;', $header);

		$content = $this->format_message($content);
		
		// if the content is blank, add a space so the blob will at least appear
		if ($content == '') {
			$content = ' ';
		}

		$pages = $this->paginate($content, $this->settingManager->get("max_blob_size"), array("<pagebreak>", "\n", " "));
		$num = count($pages);

		if ($num == 1) {
			$page = $pages[0];
			$headerMarkup = "<header>$header<end>\n\n";
			$page = "<a href=\"text://".$this->settingManager->get("default_window_color").$headerMarkup.$page."\">$name</a>";
			return $page;
		} else {
			$i = 1;
			forEach ($pages as $key => $page) {
				$headerMarkup = "<header>$header (Page $i / $num)<end>\n\n";
				$page = "<a href=\"text://".$this->settingManager->get("default_window_color").$headerMarkup.$page."\">$name</a> (Page <highlight>$i / $num<end>)";
				$pages[$key] = $page;
				$i++;
			}
			return $pages;
		}
	}

	public function makeLegacyBlob($name, $content) {
		// escape double quotes
		$content = str_replace('"', '&quot;', $content);

		$content = $this->format_message($content);

		$pages = $this->paginate($content, $this->settingManager->get("max_blob_size"), array("<pagebreak>", "\n", " "));
		$num = count($pages);

		if ($num == 1) {
			$page = $pages[0];
			$page = "<a href=\"text://".$this->settingManager->get("default_window_color").$page."\">$name</a>";
			return $page;
		} else {
			$i = 1;
			forEach ($pages as $key => $page) {
				if ($i > 1) {
					$header = "<header>$name (Page $i / $num)<end>\n\n";
				} else {
					$header = '';
				}
				$page = "<a href=\"text://".$this->settingManager->get("default_window_color").$header.$page."\">$name</a> (Page <highlight>$i / $num<end>)";
				$pages[$key] = $page;
				$i++;
			}
			return $pages;
		}
	}

	public function paginate($input, $maxLength, $symbols) {
		$pageSize = 0;
		$currentPage = '';
		$result = array();
		$symbol = array_shift($symbols);

		$lines = explode($symbol, $input);
		forEach ($lines as $line) {
			// retain new lines and spaces in output
			if ($symbol == "\n" || $symbol == " ") {
				$line .= $symbol;
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
					$this->logger->log('ERROR', "Could not successfully page blob");
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
	 * @name: makeChatcmd
	 * @description: creates a chatcmd link
	 * @param: $name - the name the link will show
	 * @param: $content - the chatcmd to execute
	 * @param: $style (optional) - any styling you want applied to the link
	 */
	public function makeChatcmd($name, $content, $style = null) {
		$content = str_replace("'", '&#39;', $content);
		return "<a $style href='chatcmd://$content'>$name</a>";
	}

	/**
	 * @name: make_userlink
	 * @description: creates a user link which adds support for right clicking usernames in chat, providing you with a menu of options (ignore etc.) (see 18.1 AO patchnotes)
	 * @param: $name - the name the user to create a link for
	 * @param: $style (optional) - any styling you want applied to the link
	 */
	public function make_userlink($user, $style = null) {
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
			"<header>" => $this->settingManager->get('default_header_color'),
			"<header2>" => $this->settingManager->get('default_header2_color'),
			"<highlight>" => $this->settingManager->get('default_highlight_color'),
			"<black>" => "<font color='#000000'>",
			"<white>" => "<font color='#FFFFFF'>",
			"<yellow>" => "<font color='#FFFF00'>",
			"<blue>" => "<font color='#8CB5FF'>",
			"<green>" => "<font color='#00DE42'>",
			"<red>" => "<font color='#FF0000'>",
			"<orange>" => "<font color='#FCA712'>",
			"<grey>" => "<font color='#C3C3C3'>",
			"<cyan>" => "<font color='#00FFFF'>",
			"<violet>" => "<font color='#8F00FF'>",

			"<neutral>" => $this->settingManager->get('default_neut_color'),
			"<omni>" => $this->settingManager->get('default_omni_color'),
			"<clan>" => $this->settingManager->get('default_clan_color'),
			"<unknown>" => $this->settingManager->get('default_unknown_color'),

			"<myname>" => $this->chatBot->vars["name"],
			"<myguild>" => $this->chatBot->vars["my_guild"],
			"<tab>" => "    ",
			"<end>" => "</font>",
			"<symbol>" => $this->settingManager->get("symbol"));

		$message = str_ireplace(array_keys($array), array_values($array), $message);

		return $message;
	}
}

?>
