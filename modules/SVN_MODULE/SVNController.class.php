<?php

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'svn',
 *		accessLevel = 'admin',
 *		description = 'Updates bot from SVN repository',
 *		help        = 'svn.txt'
 *	)
 */
class SVNController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $settingManager;

	/** @Inject */
	public $text;

	/** @Inject */
	public $util;
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->settingManager->add($this->moduleName, "svnconflict", "How to handle conflicts", "edit", "options", "theirs-conflict", "theirs-conflict;mine-conflict;theirs-full;mine-full;postpone", '', "admin", "");
		$this->settingManager->add($this->moduleName, "svnpath", "Path to svn binary", "edit", "text", "svn", "svn;/usr/bin/svn");
	}
	
	/**
	 * @HandlesCommand("svn")
	 * @Matches("/^svn dry$/i")
	 */
	public function svnDryCommand($message, $channel, $sender, $sendto, $args) {
		$svnpath = $this->settingManager->get('svnpath');
		$command = "$svnpath merge --dry-run -r BASE:HEAD . 2>&1";
		$output = array();
		$return_var = '';
		exec($command, $output, $return_var);

		$blob = $command . "\n\n";
		forEach ($output as $line) {
			$blob .= $line . "\n";
		}

		$msg = $this->text->make_blob('svn merge --dry-run output', $blob);

		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("svn")
	 * @Matches("/^svn update$/i")
	 */
	public function svnUpdateCommand($message, $channel, $sender, $sendto, $args) {
		$svnpath = $this->settingManager->get('svnpath');
		$command = "$svnpath info 2>&1";
		$output = array();
		$return_var = '';
		exec($command, $output, $return_var);

		$blob = $command . "\n\n";
		forEach ($output as $line) {
			$blob .= $line . "\n";
		}
		$blob .= "\n";

		$command = "$svnpath update --accept " . $this->settingManager->get('svnconflict') . " 2>&1";
		$output = array();
		$return_var = '';
		exec($command, $output, $return_var);

		$blob .= $command . "\n\n";
		forEach ($output as $line) {
			$blob .= $line . "\n";
		}

		$msg = $this->text->make_blob('svn update output', $blob);

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("svn")
	 * @Matches("/^svn info$/i")
	 */
	public function svnInfoCommand($message, $channel, $sender, $sendto, $args) {
		$svnpath = $this->settingManager->get('svnpath');
		$command = "$svnpath info 2>&1";
		$output = array();
		$return_var = '';
		exec($command, $output, $return_var);

		$blob = $command . "\n\n";
		forEach ($output as $line) {
			$blob .= $line . "\n";
		}

		$msg = $this->text->make_blob('svn info output', $blob);

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("svn")
	 * @Matches("/^svn status$/i")
	 * @Matches("/^svn status (.*)$/i")
	 */
	public function svnStatusCommand($message, $channel, $sender, $sendto, $args) {
		$svnpath = $this->settingManager->get('svnpath');
		if (count($args) == 2) {
			$param = $args[1];
		}
		$command = "$svnpath status $param 2>&1";
		$output = array();
		$return_var = '';
		exec($command, $output, $return_var);

		$blob = $command . "\n\n";
		forEach ($output as $line) {
			$blob .= $line . "\n";
		}

		$msg = $this->text->make_blob("svn status $param output", $blob);

		$sendto->reply($msg);
	}
}

?>
