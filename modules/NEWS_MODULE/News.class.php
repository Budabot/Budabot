<?php

class News {
	/** @Inject */
	public $db;

	/**
	 * @Setting("news")
	 * @Description("Private channel news")
	 * @Visibility("hide")
	 * @Type("text")
	 */
	public $defaultNews = "Not set.";
	
	/**
	 * @Setting("adminnews")
	 * @Description("Current admin news")
	 * @Visibility("hide")
	 * @Type("text")
	 */
	public $defaultAdminNews = "Not set.";

	public function getNews() {
		$data = $this->db->query("SELECT * FROM `#__news` ORDER BY `sticky` DESC, `time` DESC LIMIT 10");
		$msg = '';
		if (count($data) != 0) {
			$blob = "<header> :::::: News :::::: <end>\n\n";
			$sticky = "";
			forEach ($data as $row) {
				if ($sticky != '') {
					if ($sticky != $row->sticky) {
						$blob .= "____________________________\n\n";
					} else {
						$blob .= "\n";
					}
				}

				$blob .= "<highlight>{$row->news}<end>\n";
				$blob .= "By {$row->name} " . date("dS M, H:i", $row->time) . " ";
				$blob .= Text::make_chatcmd("Remove", "/tell <myname> news rem $row->id") . " ";
				if ($row->sticky == 1) {
					$blob .= Text::make_chatcmd("Unsticky", "/tell <myname> news unsticky $row->id")."\n";
				} else if ($row->sticky == 0) {
					$blob .= Text::make_chatcmd("Sticky", "/tell <myname> news sticky $row->id")."\n";
				}
				$sticky = $row->sticky;
			}
			$msg = Text::make_blob("News", $blob)." [Last updated at ".date("dS M, H:i", $data[0]->time)."]";
		}
		return $msg;
	}
	
	/**
	 * @Event("logOn")
	 * @Description("Sends a tell with news to players logging in")
	 */
	public function logon($chatBot, $type, $sender, $args) {
		if (isset($chatBot->guildmembers[$sender]) && $chatBot->is_ready()) {
			$msg = $this->getNews();
			if ($msg != '') {
				$chatBot->send($msg, $sender);
			}
		}
	}
	
	/**
	 * @Command("news")
	 * @AccessLevel("all")
	 * @Description("Show News")
	 */
	public function newsCommand($chatBot, $message, $channel, $sender, $sendto) {
		if (!preg_match("/^news$/i", $message)) {
			return false;
		}
		$msg = $this->getNews();
		if ($msg == '') {
			$msg = "No News recorded yet.";
		}

		$chatBot->send($msg, $sendto);
	}
	
	/**
	 * @Subcommand("news add (.+)")
	 * @Channels("")
	 * @AccessLevel("rl")
	 * @Description("Add a news entry")
	 */
	public function newsAddCommand($chatBot, $message, $channel, $sender, $sendto) {
		if (preg_match("/^news add (.+)$/si", $message, $arr)) {
			$news = $arr[1];
			$this->db->exec("INSERT INTO `#__news` (`time`, `name`, `news`, `sticky`) VALUES (?, ?, ?, 0)", time(), $sender, $news);
			$msg = "News has been added successfully.";

			$chatBot->send($msg, $sendto);
		} else {
			return false;
		}
	}
	
	/**
	 * @Subcommand("news rem (.+)")
	 * @Channels("")
	 * @AccessLevel("rl")
	 * @Description("Remove a news entry")
	 */
	public function newsRemCommand($chatBot, $message, $channel, $sender, $sendto) {
		if (preg_match("/^news rem ([0-9]+)$/i", $message, $arr)) {
			$id = $arr[1];
			$rows = $this->db->exec("DELETE FROM `#__news` WHERE `id` = ?", $id);
			if ($rows == 0) {
				$msg = "No news entry found with the ID <highlight>{$id}<end>.";
			} else {
				$msg = "News entry <highlight>{$id}<end> was deleted successfully.";
			}

			$chatBot->send($msg, $sendto);
		} else {
			return false;
		}
	}
	
	/**
	 * @Subcommand("news sticky (.+)")
	 * @Channels("")
	 * @AccessLevel("rl")
	 * @Description("Stickies a news entry")
	 */
	public function stickyCommand($chatBot, $message, $channel, $sender, $sendto) {
		if (preg_match("/^news sticky ([0-9]+)$/i", $message, $arr)) {
			$id = $arr[1];

			$row = $this->db->queryRow("SELECT * FROM `#__news` WHERE `id` = ?", $id);

			if ($row->sticky == 1) {
				$msg = "News ID $id is already stickied.";
			} else {
				$this->db->exec("UPDATE `#__news` SET `sticky` = 1 WHERE `id` = ?", $id);
				$msg = "News ID $id successfully stickied.";
			}
			$chatBot->send($msg, $sendto);
		} else {
			return false;
		}
	}
	
	/**
	 * @Subcommand("news unsticky (.+)")
	 * @Channels("")
	 * @AccessLevel("rl")
	 * @Description("Unstickies a news entry")
	 */
	public function unstickyCommand($chatBot, $message, $channel, $sender, $sendto) {
		if (preg_match("/^news unsticky ([0-9]+)$/i", $message, $arr)) {
			$id = $arr[1];

			$row = $this->db->queryRow("SELECT * FROM `#__news` WHERE `id` = ?", $id);

			if ($row->sticky == 0) {
				$msg = "News ID $id is not stickied.";
			} else if ($row->sticky == 1) {
				$this->db->exec("UPDATE `#__news` SET `sticky` = 0 WHERE `id` = ?", $id);
				$msg = "News ID $id successfully unstickied.";
			}
			$chatBot->send($msg, $sendto);
		} else {
			return false;
		}
	}
}

?>