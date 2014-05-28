<?php

namespace Budabot\User\Modules;

use Budabot\Core\AutoInject;
use Budabot\Core\Registry;
use ReflectionClass;

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'reloadinstance',
 *		accessLevel = 'admin',
 *		description = "Manually reload instances",
 *		help        = 'reloadinstance.txt'
 *	)
 */
class ReloadInstanceController extends AutoInject {

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
	 * @HandlesCommand("reloadinstance")
	 * @Matches("/^reloadinstance all$/i")
	 */
	public function reloadinstanceAllCommand($message, $channel, $sender, $sendto, $args) {
		$instances = Registry::getAllInstances();
		$count = count($instances);
		$blob = '';
		forEach ($instances as $name =>$instance) {
			$blob .= $name . ' (' . get_class($instance) . ")\n";
			Registry::importChanges($instance);
			Registry::injectDependencies($instance);
		}
		$msg = $this->text->make_blob("All instances have been reloaded ($count)", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("reloadinstance")
	 * @Matches("/^reloadinstance (.+)$/i")
	 */
	public function reloadinstanceCommand($message, $channel, $sender, $sendto, $args) {
		$instanceName = $args[1];
		
		$instance = Registry::getInstance($instanceName);
		if ($instance === null) {
			$msg = "Could not find instance <highlight>$instanceName<end>.";
		} else {
			try {
				$reflection = new ReflectionClass($instance);
				$syntaxResult = $this->checkSyntax($reflection->getFileName());
			} catch(ReflectionException $e) {
				LegacyLogger::log("WARN", "Registry", "RUNKIT: Failed to reflect class, reason was: '" . $e->getMessage() . "'");
				return;
			}

			if (preg_match("/^No syntax errors detected/", $syntaxResult)) {
				Registry::getInstance($instanceName, true);
				$msg = "Instance <highlight>$instanceName<end> has been reloaded.";
			} else {
				$msg = "Error reloading instance <highlight>$instanceName<end>: $syntaxResult";
			}
		}
		$sendto->reply($msg);
	}
	
	public function checkSyntax($filename) {
		if (\budabot\core\isWindows()) {
			$command = ".\win32\php.exe -l $filename";
		} else {
			$command = "php -l $filename";
		}
		
		$output = array();
		$return_var = '';
		exec($command, $output, $return_var);

		return implode("\n", $output);
	}
}
