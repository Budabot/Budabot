<?php

namespace WebUi;

/**
 * @Instance
 */
class Template {

	/** @Inject */
	public $chatBot;

	/** @Inject("WebUi\LoginController") */
	public $login;

	/**
	 * @var \Twig_Environment
	 */
	private $twig;

	/**
	 * @Setup
	 */
	public function setup() {
		$loader = new \Twig_Loader_Filesystem( __DIR__ . '/resources/tmpl');
		$this->twig = new \Twig_Environment($loader, array());
	}

	public function render($name, $session, $parameters = array()) {
		global $version;
		$parameters = array_merge(array(
			'botname' => $this->chatBot->vars['name'],
			'version' => $version,
			'loggedIn' => $this->login->isLoggedIn($session)
		), $parameters);
		return $this->twig->render($name, $parameters);
	}

}
