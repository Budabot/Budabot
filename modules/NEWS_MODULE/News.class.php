<?php

class News {
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $text;

	/**
	 * @Setting("news")
	 * @Description("Private channel news")
	 * @Visibility("hide")
	 * @Type("text")
	 * @Help("news.txt")
	 */
	public $defaultNews = "Not set.";

	public function getNews() {
		$data = $this->db->query("SELECT * FROM `#__news` ORDER BY `sticky` DESC, `time` DESC LIMIT 10");
		$msg = '';
		if (count($data) != 0) {
			$blob = '';
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
				$blob .= "By {$row->name} " . date(Util::DATETIME, $row->time) . " ";
				$blob .= $this->text->make_chatcmd("Remove", "/tell <myname> news rem $row->id") . " ";
				if ($row->sticky == 1) {
					$blob .= $this->text->make_chatcmd("Unsticky", "/tell <myname> news unsticky $row->id")."\n";
				} else if ($row->sticky == 0) {
					$blob .= $this->text->make_chatcmd("Sticky", "/tell <myname> news sticky $row->id")."\n";
				}
				$sticky = $row->sticky;
			}
			$msg = $this->text->make_blob("News", $blob)." [Last updated at ".date(Util::DATETIME, $data[0]->time)."]";
		}
		return $msg;
	}
	
	/**
	 * @Event("logOn")
	 * @Description("Sends a tell with news to players logging in")
	 */
	public function logon($eventObj) {
		$sender = $eventObj->sender;

		if (isset($this->chatBot->guildmembers[$sender]) && $this->chatBot->is_ready()) {
			$msg = $this->getNews();
			if ($msg != '') {
				$this->chatBot->send($msg, $sender);
			}
		}
	}
	
	/**
	 * @Command("news")
	 * @AccessLevel("all")
	 * @Description("Show News")
	 * @Matches("/^news$/i")
	 * @Help("news.txt")
	 */
	public function newsCommand($message, $channel, $sender, $sendto) {
		$msg = $this->getNews();
		if ($msg == '') {
			$msg = "No News recorded yet.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @Subcommand("news add (.+)")
	 * @AccessLevel("rl")
	 * @Description("Add a news entry")
	 * @Matches("/^news add (.+)$/si")
	 */
	public function newsAddCommand($message, $channel, $sender, $sendto, $arr) {
		$news = $arr[1];
		$this->db->exec("INSERT INTO `#__news` (`time`, `name`, `news`, `sticky`) VALUES (?, ?, ?, 0)", time(), $sender, $news);
		$msg = "News has been added successfully.";

		$sendto->reply($msg);
	}
	
	/**
	 * @Subcommand("news rem (.+)")
	 * @AccessLevel("rl")
	 * @Description("Remove a news entry")
	 * @Matches("/^news rem ([0-9]+)$/i")
	 */
	public function newsRemCommand($message, $channel, $sender, $sendto, $arr) {
		$id = $arr[1];
		$rows = $this->db->exec("DELETE FROM `#__news` WHERE `id` = ?", $id);
		if ($rows == 0) {
			$msg = "No news entry found with the ID <highlight>{$id}<end>.";
		} else {
			$msg = "News entry <highlight>{$id}<end> was deleted successfully.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @Subcommand("news sticky (.+)")
	 * @AccessLevel("rl")
	 * @Description("Stickies a news entry")
	 * @Matches("/^news sticky ([0-9]+)$/i")
	 */
	public function stickyCommand($message, $channel, $sender, $sendto, $arr) {
		$id = $arr[1];

		$row = $this->db->queryRow("SELECT * FROM `#__news` WHERE `id` = ?", $id);

		if ($row->sticky == 1) {
			$msg = "News ID $id is already stickied.";
		} else {
			$this->db->exec("UPDATE `#__news` SET `sticky` = 1 WHERE `id` = ?", $id);
			$msg = "News ID $id successfully stickied.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @Subcommand("news unsticky (.+)")
	 * @AccessLevel("rl")
	 * @Description("Unstickies a news entry")
	 * @Matches("/^news unsticky ([0-9]+)$/i")
	 */
	public function unstickyCommand($message, $channel, $sender, $sendto, $arr) {
		$id = $arr[1];

		$row = $this->db->queryRow("SELECT * FROM `#__news` WHERE `id` = ?", $id);

		if ($row->sticky == 0) {
			$msg = "News ID $id is not stickied.";
		} else if ($row->sticky == 1) {
			$this->db->exec("UPDATE `#__news` SET `sticky` = 0 WHERE `id` = ?", $id);
			$msg = "News ID $id successfully unstickied.";
		}
		$sendto->reply($msg);
	}
}

?>