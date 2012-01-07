<?php

class CommandSearchView {

	public function render($results, $hasAccess, $exactMatch) {
        $blob = "<header> :::::: Command Search Results :::::: <end>\n\n";
        forEach ($results as $row) {
            if ($row->help != '') {
                $helpLink = Text::make_chatcmd("Help", "/tell <myname> help $row->help");
            } else {
                $helpLink = Text::make_chatcmd("Help", "/tell <myname> help $row->cmd");
            }
            if ($hasAccess) {
                $module = Text::make_chatcmd($row->module, "/tell <myname> config {$row->module}");
            } else {
                $module = "<yellow>{$row->module}<end>";
            }

            $cmd = str_pad($row->cmd . " ", 20, ".");
            $blob .= "<highlight>{$cmd}<end> {$module} - {$row->description} ({$helpLink})\n";
        }

        if (count($results) == 0) {
            $msg = "No results found.";
        } else {
            $msg = Text::make_blob(count($results) . ' results found', $blob);
        }
		return $msg;
	}
}
