<?php

/**
 * @Instance
 */
class CommandSearchView {

	/** @Inject */
	public $text;

	public function render($results, $hasAccess, $exactMatch) {
		$blob = '';
		forEach ($results as $row) {
			if ($row->help != '') {
				$helpLink = ' (' . $this->text->make_chatcmd("Help", "/tell <myname> help $row->cmd") . ')';
			}
			if ($hasAccess) {
				$module = $this->text->make_chatcmd($row->module, "/tell <myname> config {$row->module}");
			} else {
				$module = "{$row->module}";
			}

			$cmd = str_pad($row->cmd . " ", 20, ".");
			$blob .= "<highlight>{$cmd}<end> {$module} - {$row->description}{$helpLink}\n";
		}

		$count = count($results);
		if ($count == 0) {
			$msg = "No results found.";
		} else {
			if ($exactMatch) {
				$msg = $this->text->make_blob("Command Search Results ($count)", $blob);
			} else {
				$msg = $this->text->make_blob("Possible Matches ($count)", $blob);
			}
		}
		return $msg;
	}
}
