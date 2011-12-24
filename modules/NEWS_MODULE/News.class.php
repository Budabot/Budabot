<?php

class News {
	public static function getNews() {
		$db = DB::get_instance();
	
		$data = $db->query("SELECT * FROM `#__news` ORDER BY `sticky` DESC, `time` DESC LIMIT 10");
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
}

?>