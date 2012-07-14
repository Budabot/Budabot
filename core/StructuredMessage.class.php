<?php

class StructuredMessage {
	private $header;
	private $content = array();
	private $footer;

	public function setHeader($header) {
		$this->header = $header;
	}

	public function setFooter($footer) {
		$this->footer = $footer;
	}

	public function addContent($content) {
		$this->content []= $content;
	}

	public function renderAOML($blob = true) {
		$output = '';

		if (!$blob && !empty($this->header)) {
			$output .= "<header> :::::: $this->header :::::: <end>\n\n";
		}

		forEach ($this->content as $item) {
			if (is_object($item) && method_exists($item, 'renderAOML')) {
				$output .= '<pagebreak>' . $item->renderAOML(false) . "\n";
			} else {
				$output .= $item . "\n";
			}
		}

		if (!empty($this->footer)) {
			$output .= "\n" . $this->footer;
		}

		if ($blob) {
			return Text::make_blob($this->header, $output);
		} else {
			return $output;
		}
	}
}

?>
