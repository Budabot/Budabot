<?php

namespace Budabot\User\Modules;

use Budabot\Core\AutoInject;
use \DateTimeZone;

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'timezone',
 *		accessLevel = 'superadmin',
 *		description = "Set the timezone",
 *		help        = 'timezone.txt',
 *		alias		= 'timezones'
 *	)
 */
class TimezoneController extends AutoInject {

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
	 * @HandlesCommand("timezone")
	 * @Matches("/^timezone$/i")
	 */
	public function timezoneCommand($message, $channel, $sender, $sendto, $args) {
		$timezoneAreas = $this->getTimezoneAreas();
		
		$blob = '';
		forEach ($timezoneAreas as $area => $code) {
			$blob .= $this->text->make_chatcmd($area, "/tell <myname> timezone $area") . "\n";
		}
		$msg = $this->text->make_blob("Timezone Areas", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("timezone")
	 * @Matches("/^timezone set ([^ ]*)$/i")
	 */
	public function timezoneSEtCommand($message, $channel, $sender, $sendto, $args) {
		$timezone = $args[1];
		
		$result = date_default_timezone_set($timezone);
		
		if ($result) {
			$msg = "Timezone has been set to <highlight>$timezone<end>.";
		} else {
			$msg = "<highlight>$timezone<end> is not a valid timezone.";
		}
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("timezone")
	 * @Matches("/^timezone ([^ ]*)$/i")
	 */
	public function timezoneAreaCommand($message, $channel, $sender, $sendto, $args) {
		$area = $args[1];
		
		$timezoneAreas = $this->getTimezoneAreas();
		$code = $timezoneAreas[$area];
		if (empty($code)) {
			return false;
		}
		
		$timezones = DateTimeZone::listIdentifiers($code);
		$count = count($timezone);
		
		$blob = '';
		forEach ($timezones as $timezone) {
			$blob .= $this->text->make_chatcmd($timezone, "/tell <myname> timezone set $timezone") . "\n";
		}
		$msg = $this->text->make_blob("Timezones for $area ($count)", $blob);
		$sendto->reply($msg);
	}
	
	public function getTimezoneAreas() {
		return array(
			'Africa' => 1,
			'America' => 2,
			'Antarctica' => 4,
			'Arctic' => 8,
			'Asia' => 16,
			'Atlantic' => 32,
			'Australia' => 64,
			'Europe' => 128,
			'Indian' => 256,
			'Pacific' => 512,
			'UTC' => 1024);
	}
}
