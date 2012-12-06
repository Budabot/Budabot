<?php

class InvalidHttpRequest extends Exception {
}

class HttpRequest {

	private $method;
	private $uri;
	private $extraHeaders  = array();
	private $queryParams   = array();
	private $streamScheme  = null;
	private $streamPort    = null;
	private $streamHost    = null;
	private $uriComponents = array();

	public function __construct($method, $uri, $queryParams, $extraHeaders) {
		$this->method = $method;
		$this->uri = $uri;
		$this->queryParams = $queryParams;
		$this->extraHeaders = $extraHeaders;

		$this->parseUri();

		$this->extractStreamScheme();
		$this->extractStreamPort();
		$this->extractStreamHost();
	}

	private function parseUri() {
		$this->uriComponents = parse_url($this->uri);
		if (!is_array($this->uriComponents)) {
			throw new InvalidHttpRequest("Invalid URI: '{$this->uri}'");
		}
	}

	private function extractStreamScheme() {
		if ($this->uriComponents['scheme'] == 'http') {
			$this->streamScheme = 'tcp';
		} else if ($this->uriComponents['scheme'] == 'https') {
			$this->streamScheme = 'ssl';
		} else {
			throw new InvalidHttpRequest("URI has no valid scheme: '{$this->uri}'");
		}
	}

	private function extractStreamPort() {
		if ($this->uriComponents['scheme'] == 'http') {
			if (isset($this->uriComponents['port'])) {
				$this->streamPort = $this->uriComponents['port'];
			} else {
				$this->streamPort = 80;
			}
		} else if ($this->uriComponents['scheme'] == 'https') {
			if (isset($this->uriComponents['port'])) {
				$this->streamPort = $this->uriComponents['port'];
			} else {
				$this->streamPort = 443;
			}
		} else {
			throw new InvalidHttpRequest("URI has no valid scheme: '{$this->uri}'");
		}
	}

	private function extractStreamHost() {
		if (!isset($this->uriComponents['host'])) {
			throw new InvalidHttpRequest("URI has no host: '{$this->uri}'");
		}
		$this->streamHost = $this->uriComponents['host'];
	}

	public function getHost() {
		return $this->streamHost;
	}

	public function getPort() {
		return $this->streamPort;
	}

	public function getScheme() {
		return $this->streamScheme;
	}

	public function getData() {
		$data = $this->getHeaderData();
		if ($this->method == 'post') {
			$data .= $this->getPostQueryStr();
		}

		return $data;
	}

	private function getHeaderData() {
		$path = $this->getRequestPath();
		$data = strtoupper($this->method) . " $path HTTP/1.0\r\n";

		forEach ($this->getHeaders() as $header => $value) {
			$data .= "$header: $value\r\n";
		}

		$data .= "\r\n";
		return $data;
	}

	private function getRequestPath() {
		$path     = isset($this->uriComponents['path']) ? $this->uriComponents['path'] : '/';
		$queryStr = isset($this->uriComponents['query']) ? $this->uriComponents['query'] : null;

		if ($this->method == 'get') {
			parse_str($queryStr, $queryArray);
			$queryArray = array_merge($queryArray, $this->queryParams);
			$queryStr = http_build_query($queryArray);
		} else if ($this->method == 'post') {
		} else {
			throw new InvalidHttpRequest("Invalid http method: '{$this->method}'");
		}
		return "$path?$queryStr";
	}

	private function getHeaders() {
		$headers = array();
		$headers['Host'] = $this->streamHost;
		if ($this->method == 'post' && $this->queryParams) {
			$headers['Content-Type'] = 'application/x-www-form-urlencoded';
			$headers['Content-Length'] = strlen($this->getPostQueryStr());
		}

		$headers = array_merge($headers, $this->extraHeaders);
		return $headers;
	}

	private function getPostQueryStr() {
		return http_build_query($this->queryParams);
	}
}
