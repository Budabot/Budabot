<?php

namespace Budabot\User\Modules;

use Budabot\Core\AutoInject;

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'cache',
 *		accessLevel = 'superadmin',
 *		description = "Manage cached files",
 *		help        = 'cache.txt'
 *	)
 */
class CacheController extends AutoInject {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/**
	 * @Setup
	 */
	public function setup() {

	}

	/**
	 * @HandlesCommand("cache")
	 * @Matches("/^cache$/i")
	 */
	public function cacheCommand($message, $channel, $sender, $sendto, $args) {
		$blob = '';
		forEach ($this->cacheManager->getGroups() as $group) {
			$blob .= $this->text->make_chatcmd($group, "/tell <myname> cache browse $group") . "\n";
		}
		$msg = $this->text->make_blob("Cache Groups", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("cache")
	 * @Matches("/^cache browse ([a-z0-9_-]+)$/i")
	 */
	public function cacheBrowseCommand($message, $channel, $sender, $sendto, $args) {
		$group = $args[1];
		
		$path = $this->chatBot->vars['cachefolder'] . $group;
	
		$blob = '';
		forEach ($this->cacheManager->getFilesInGroup($group) as $file) {
			$fileInfo = stat($path . "/" . $file);
			$blob .= "<highlight>$file<end>  " . $this->util->bytesConvert($fileInfo['size']) . " - Last modified " . $this->util->date($fileInfo['mtime']);
			$blob .= "  [" . $this->text->make_chatcmd("View", "/tell <myname> cache view $group $file") . "]";
			$blob .= "  [" . $this->text->make_chatcmd("Delete", "/tell <myname> cache rem $group $file") . "]\n";
		}
		$msg = $this->text->make_blob("Cache Group: $group", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("cache")
	 * @Matches("/^cache rem ([a-z0-9_-]+) ([a-z0-9_\.-]+)$/i")
	 */
	public function cacheRemCommand($message, $channel, $sender, $sendto, $args) {
		$group = $args[1];
		$file = $args[2];
		
		if ($this->cacheManager->cacheExists($group, $file)) {
			$contents = $this->cacheManager->remove($group, $file);
			$msg = "Cache file <highlight>$file<end> in cache group <highlight>$group<end> has been deleted.";
		} else {
			$msg = "Could not find file <highlight>$file<end> in cache group <highlight>$group<end>.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("cache")
	 * @Matches("/^cache view ([a-z0-9_-]+) ([a-z0-9_\.-]+)$/i")
	 */
	public function cacheViewCommand($message, $channel, $sender, $sendto, $args) {
		$group = $args[1];
		$file = $args[2];
		
		if ($this->cacheManager->cacheExists($group, $file)) {
			$contents = $this->cacheManager->retrieve($group, $file);
			$msg = $this->text->make_blob("Cache File: $group $file", htmlspecialchars ($contents));
		} else {
			$msg = "Could not find file <highlight>$file<end> in cache group <highlight>$group<end>.";
		}
		$sendto->reply($msg);
	}
}
