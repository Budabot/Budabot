<?php

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'git',
 *		accessLevel = 'admin',
 *		description = 'Updates bot from Git repository',
 *		help        = 'git.txt'
 *	)
 */
class GitController extends AutoInject {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/**
	 * @Setup
	 */
	public function setup() {
		//$this->settingManager->add($this->moduleName, "svnconflict", "How to handle conflicts", "edit", "options", "theirs-conflict", "theirs-conflict;mine-conflict;theirs-full;mine-full;postpone", '', "admin", "");
		$this->settingManager->add($this->moduleName, "gitpath", "Path to git binary", "edit", "text", "git", "git;/usr/bin/git;C:/Program Files (x86)/Git/bin/git.exe");
	}
	
	/**
	 * @HandlesCommand("git")
	 * @Matches("/^git diff$/i")
	 */
	public function gitDiffCommand($message, $channel, $sender, $sendto, $args) {
		$gitpath = $this->settingManager->get('gitpath');
		$command = "$gitpath fetch origin 2>&1";
		$this->executeCommand($command);
		
		$command = "$gitpath diff HEAD...origin/master 2>&1";
		
		$blob = $this->executeCommand($command);
		$msg = $this->text->make_blob("svn status $param output", $blob);
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("git")
	 * @Matches("/^git pull$/i")
	 */
	public function gitPullCommand($message, $channel, $sender, $sendto, $args) {
		$gitpath = $this->settingManager->get('gitpath');
		$command = "$gitpath pull 2>&1";
		
		$blob = $this->executeCommand($command);
		$msg = $this->text->make_blob("svn status $param output", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("git")
	 * @Matches("/^git log$/i")
	 */
	public function gitLogCommand($message, $channel, $sender, $sendto, $args) {
		$gitpath = $this->settingManager->get('gitpath');
		$command = "$gitpath log 2>&1";
		
		$blob = $this->executeCommand($command);
		$msg = $this->text->make_blob("svn status $param output", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("git")
	 * @Matches("/^git status$/i")
	 */
	public function gitStatusCommand($message, $channel, $sender, $sendto, $args) {
		$gitpath = $this->settingManager->get('gitpath');
		$command = "$gitpath status 2>&1";
		
		$blob = $this->executeCommand($command);
		$msg = $this->text->make_blob("svn status $param output", $blob);
		$sendto->reply($msg);
	}
	
	private function executeCommand($command) {
		$output = array();
		$return_var = '';
		exec($command, $output, $return_var);

		$blob = $command . "\n\n";
		forEach ($output as $line) {
			$blob .= $line . "\n";
		}
		return $blob;
	}
}

?>
