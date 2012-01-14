<?php

class CommandSearchView {

	/** @Inject */
	public $text;

	public function render($results, $hasAccess, $exactMatch) {
		$blob = "<header> :::::: Command Search Results :::::: <end>\n\n";
		forEach ($results as $row) {
			if ($row->help != '') {
				$helpLink = $this->text->make_chatcmd("Help", "/tell <myname> help $row->help");
			} else {
				$helpLink = $this->text->make_chatcmd("Help", "/tell <myname> help $row->cmd");
			}
			if ($hasAccess) {
				$module = $this->text->make_chatcmd($row->module, "/tell <myname> config {$row->module}");
			} else {
				$module = "<yellow>{$row->module}<end>";
			}

			$cmd = str_pad($row->cmd . " ", 20, ".");
			$blob .= "<highlight>{$cmd}<end> {$module} - {$row->description} ({$helpLink})\n";
		}

		if (count($results) == 0) {
			$msg = "No results found.";
		} else {
			if ($exactMatch) {
				$msg = $this->text->make_blob(count($results) . ' results found', $blob);
			} else {
				$msg = 'Exact match not found. ' . $this->text->make_blob('Did you mean one of these?', $blob);
			}
		}
		return $msg;
	}
}
