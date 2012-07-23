<?php

namespace Guzzle\Http;

/**
 * Parses and generates URLs based on URL parts.  In favor of performance,
 * URL parts are not validated.
 */
class Url
{
    protected $scheme;
    protected $host;
    protected $port;
    protected $username;
    protected $password;
    protected $path = '/';
    protected $fragment;

    /**
     * @var QueryString Query part of the URL
     */
    protected $query;

    /**
     * Factory method to create a new URL from a URL string
     *
     * @param string $url Full URL used to create a Url object
     *
     * @return Url
     */
    public static function factory($url)
    {
        $parts = (array) self::parseUrl($url);
        $parts['scheme'] = isset($parts['scheme']) ? $parts['scheme'] : null;
        $parts['host'] = isset($parts['host']) ? $parts['host'] : null;
        $parts['path'] = isset($parts['path']) ? $parts['path'] : null;
        $parts['port'] = isset($parts['port']) ? $parts['port'] : null;
        $parts['query'] = isset($parts['query']) ? $parts['query'] : null;
        $parts['user'] = isset($parts['user']) ? $parts['user'] : null;
        $parts['pass'] = isset($parts['pass']) ? $parts['pass'] : null;
        $parts['fragment'] = isset($parts['fragment']) ? $parts['fragment'] : null;

        if ($parts['query']) {
            $parts['query'] = self::parseQuery($parts['query']);
        }

        return new self($parts['scheme'], $parts['host'], $parts['user'],
            $parts['pass'], $parts['port'], $parts['path'], $parts['query'],
            $parts['fragment']);
    }

    /**
     * Parse a URL using special handling for a subset of UTF-8 characters in
     * the query string if needed.
     *
     * @param string $url URL to parse
     *
     * @return array
     */
    public static function parseUrl($url)
    {
        $parts = parse_url($url);

        // need to handle query parsing specially for UTF-8 requirements
        if (isset($parts['query'])) {
            $queryPos = strpos($url, '?');
            if (isset($parts['fragment'])) {
                $parts['query'] = substr($url, $queryPos + 1, strpos($url, '#') - $queryPos - 1);
            } else {
                $parts['query'] = substr($url, $queryPos + 1);
            }
        }

        return $parts;
    }

    /**
     * Parse a query string into a QueryString object
     *
     * @param string $query Query string
     *
     * @return QueryString
     */
    public static function parseQuery($query)
    {
        $q = new QueryString();
        foreach (explode('&', $query) as $kvp) {
            $parts = explode('=', $kvp);
            $key = rawurldecode($parts[0]);
            if (substr($key, -2) == '[]') {
                $key = substr($key, 0, -2);
            }
            $value = array_key_exists(1, $parts) ? rawurldecode($parts[1]) : null;
            $q->add($key, $value);
        }

        return $q;
    }

    /**
     * Build a URL from parse_url parts.  The generated URL will be a relative
     * URL if a scheme or host are not provided.
     *
     * @param array $parts Array of parse_url parts
     *
     * @return string
     */
    public static function buildUrl(array $parts)
    {
        $url = $scheme = '';

        if (isset($parts['scheme'])) {
            $scheme = $parts['scheme'];
            $url .= $scheme . '://';
        }

        if (isset($parts['host'])) {

            if (isset($parts['user'])) {
                $url .= $parts['user'];
                if (isset($parts['pass'])) {
                    $url .= ':' . $parts['pass'];
                }
                $url .=  '@';
            }

            $url .= $parts['host'];

            // Only include the port if it is not the default port of the scheme
            if (isset($parts['port'])
                && !(($scheme == 'http' && $parts['port'] == 80)
                    || ($scheme == 'https' && $parts['port'] == 443))) {
                $url .= ':' . $parts['port'];
            }
        }

        if (empty($parts['path'])) {
            $url .= '/';
        } else {
            if ($parts['path'][0] != '/') {
                $url .= '/';
            }
            $url .= $parts['path'];
        }

        // Add the query string if present
        if (!empty($parts['query'])) {
            if ($parts['query'][0] != '?') {
                $url .= array_key_exists('query_prefix', $parts)
                      ? $parts['query_prefix'] : '?';
            }
            $url .= $parts['query'];
        }

        // Ensure that # is only added to the url if fragment contains anything.
        if (isset($parts['fragment']) && !empty($parts['fragment'])) {
            $url .= '#' . $parts['fragment'];
        }

        return $url;
    }

    /**
     * Create a new URL from URL parts
     *
     * @param string $scheme Scheme of the URL
     * @param string $host Host of the URL
     * @param string $username (optional) Username of the URL
     * @param string $password (optional) Password of the URL
     * @param int $port (optional) Port of the URL
     * @param string $path (optional) Path of the URL
     * @param QueryString|array|string $query (optional) Query string of the URL
     * @param string $fragment (optional) Fragment of the URL
     */
    public function __construct($scheme, $host, $username = null, $password = null, $port = null, $path = null, QueryString $query = null, $fragment = null)
    {
        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->fragment = $fragment;
        $this->setQuery($query ?: new QueryString());

        if ($path) {
            $this->setPath($path);
        }
    }

    /**
     * Clone the URL
     */
    public function __clone()
    {
        $this->query = clone $this->query;
    }

    /**
     * Returns the URL as a URL string
     *
     * @return string
     */
    public function __toString()
    {
        return self::buildUrl($this->getParts());
    }

    /**
     * Get the parts of the URL as an array
     *
     * @return array
     */
    public function getParts()
    {
        return array(
            'scheme' => $this->scheme,
            'user' => $this->username,
            'pass' => $this->password,
            'host' => $this->host,
            'port' => $this->port,
            'path' => $this->getPath(),
            'query' => (string) $this->query,
            'fragment' => $this->fragment,
            'query_prefix' => $this->query->getPrefix()
        );
    }

    /**
     * Set the host of the request.
     *
     * @param string $host Host to set (e.g. www.yahoo.com, yahoo.com)
     *
     * @return Url
     */
    public function setHost($host)
    {
        if (strpos($host, ':') === false) {
            $this->host = $host;
        } else {
            list($host, $port) = explode(':', $host);
            $this->host = $host;
            $this->setPort($port);
        }

        return $this;
    }

    /**
     * Get the host part of the URL
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set the scheme part of the URL (http, https, ftp, etc)
     *
     * @param string $scheme Scheme to set
     *
     * @return Url
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * Get the scheme part of the URL
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Set the port part of the URL
     *
     * @param int $port Port to set
     *
     * @return Url
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get the port part of the URl.  Will return the default port for a given
     * scheme if a port has not explicitly been set.
     *
     * @return int|null
     */
    public function getPort()
    {
        if ($this->port) {
            return $this->port;
        } else if ($this->scheme == 'http') {
            return 80;
        } else if ($this->scheme == 'https') {
            return 443;
        }

        return null;
    }

    /**
     * Set the path part of the URL
     *
     * @param array|string $path Path string or array of path segments
     *
     * @return Url
     */
    public function setPath($path)
    {
        if (is_array($path)) {
            $this->path = '/' . implode('/', $path);
        } else {
            $this->path = $path;
            if ($this->path != '*' && substr($this->path, 0, 1) != '/') {
                $this->path = '/' . $path;
            }
        }

        return $this;
    }

    /**
     * Normalize the URL so that double slashes and relative paths are removed
     *
     * @return Url
     */
    public function normalizePath()
    {
        if ($this->path == '*') {
            return $this;
        }

        if ($this->path && $this->path != '/') {

            // Replace // and /./ with /
            $this->path = str_replace(array('/./', '//'), '/', $this->path);

            // Remove trailing relative paths if possible
            $segments = $this->getPathSegments();
            $totalSegments = count($segments);
            $last = end($segments);
            $trailingSlash = false;
            if ($last === '') {
                array_pop($segments);
                $trailingSlash = true;
            }

            while ($last == '..' || $last == '.') {
                if ($last == '..') {
                    array_pop($segments);
                    $last = array_pop($segments);
                }
                if ($last == '.' || $last == '') {
                    $last = array_pop($segments);
                }
            }

            $this->path = implode('/', $segments);
            if ($trailingSlash) {
                $this->path .= '/';
            }
        }

        // Must always start with a slash
        if (substr($this->path, 0, 1) != '/') {
            $this->path = '/' . $this->path;
        }

        return $this;
    }

    /**
     * Add a relative path to the currently set path
     *
     * @param string $relativePath Relative path to add
     *
     * @return Url
     */
    public function addPath($relativePath)
    {
        if (!$relativePath || $relativePath == '/') {
            return $this;
        }

        // Add a leading slash if needed
        if ($relativePath[0] != '/') {
            $relativePath = '/' . $relativePath;
        }

        return $this->setPath(str_replace('//', '/', $this->getPath() . $relativePath));
    }

    /**
     * Get the path part of the URL
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path ?: '/';
    }

    /**
     * Get the path segments of the URL as an array
     *
     * @return array
     */
    public function getPathSegments()
    {
        return array_slice(explode('/', $this->getPath()), 1);
    }

    /**
     * Set the password part of the URL
     *
     * @param string $password Password to set
     *
     * @return Url
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the password part of the URL
     *
     * @return null|string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the username part of the URL
     *
     * @param string $username Username to set
     *
     * @return Url
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the username part of the URl
     *
     * @return null|string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get the query part of the URL as a QueryString object
     *
     * @return QueryString
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set the query part of the URL
     *
     * @param QueryString|string|array $query Query to set
     *
     * @return Url
     */
    public function setQuery($query)
    {
        if (is_string($query)) {
            $output = null;
            parse_str($query, $output);
            $this->query = new QueryString($output);
        } else if (is_array($query)) {
            $this->query = new QueryString($query);
        } else if ($query instanceof QueryString) {
            $this->query = $query;
        }

        return $this;
    }

    /**
     * Get the fragment part of the URL
     *
     * @return null|string
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * Set the fragment part of the URL
     *
     * @param string $fragment Fragment to set
     *
     * @return Url
     */
    public function setFragment($fragment)
    {
        $this->fragment = $fragment;

        return $this;
    }

    /**
     * Check if this is an absolute URL
     *
     * @return bool
     */
    public function isAbsolute()
    {
        return $this->scheme && $this->host;
    }

    /**
     * Combine the URL with another URL.  Every part of the second URL supersede
     * the current URL if that part is specified.
     *
     * @param string $url Relative URL to combine with
     *
     * @return Url
     * @throws InvalidArgumentException
     */
    public function combine($url)
    {
        $absolutePath = $url[0] == '/';
        $url = self::factory($url);

        if ($url->getScheme()) {
            $this->scheme = $url->getScheme();
        }

        if ($url->getHost()) {
            $this->host = $url->getHost();
        }

        if ($url->getPort()) {
            $this->port = $url->getPort();
        }

        if ($url->getUsername()) {
            $this->username = $url->getUsername();
        }

        if ($url->getPassword()) {
            $this->password = $url->getPassword();
        }

        if ($url->getFragment()) {
            $this->fragment = $url->getFragment();
        }

        if ($absolutePath) {
            // Replace the current URL and query if set
            if ($url->getPath()) {
                $this->path = $url->getPath();
            }
            if (count($url->getQuery())) {
                $this->query = clone $url->getQuery();
            }
        } else {
            // Append to the current path and query string
            if ($url->getPath()) {
                $this->addPath($url->getPath());
            }
            if ($url->getQuery()) {
                $this->query->merge($url->getQuery());
            }
        }

        return $this;
    }
}
