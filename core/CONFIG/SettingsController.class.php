<?php

namespace Budabot\Core\Modules;

use Exception;

/**
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'settings', 
 *		accessLevel = 'mod', 
 *		description = 'Change settings on the bot', 
 *		help        = 'settings.txt',
 *		defaultStatus = '1'
 *	)
 */
class SettingsController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $text;

	/** @Inject */
	public $db;

	/** @Inject */
	public $settingManager;

	/** @Inject */
	public $helpManager;

	/** @Inject */
	public $util;

	/** @Inject */
	public $commandManager;

	/**
	 * @Setup
	 * This handler is called on bot startup.
	 */
	public function setup() {
		$this->settingManager->upload();
	}

	/**
	 * @HandlesCommand("settings")
	 * @Matches("/^settings$/i")
	 */
	public function settingsCommand($message, $channel, $sender, $sendto, $args) {
		$blob = '';
		$blob .= "Changing any of these settings will take effect immediately. Please note that some of these settings are read-only and cannot be changed.\n\n";
		$data = $this->db->query("SELECT * FROM settings_<myname> ORDER BY `module`");
		$cur = '';
		forEach ($data as $row) {
			if ($row->module != $cur) {
				$blob .= "\n<pagebreak><header2>".str_replace("_", " ", $row->module)."<end>\n";
				$cur = $row->module;
			}
			$blob .= "  *" . $row->description;

			if ($row->mode == "edit") {
				$editLink = $this->text->make_chatcmd('Modify', "/tell <myname> settings change {$row->name}");
				$blob .= " ($editLink)";
			}

			$settingHandler = $this->settingManager->getSettingHandler($row);
			$blob .= ": " . $settingHandler->displayValue() . "\n";
		}

		$msg = $this->text->makeBlob("Bot Settings", $blob);
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("settings")
	 * @Matches("/^settings change ([a-z0-9_]+)$/i")
	 */
	public function changeCommand($message, $channel, $sender, $sendto, $args) {
		$settingName = strtolower($args[1]);
		$row = $this->db->queryRow("SELECT * FROM settings_<myname> WHERE `name` = ?", $settingName);
		if ($row === null) {
			$msg = "Could not find setting <highlight>{$settingName}<end>.";
		} else {
			$settingHandler = $this->settingManager->getSettingHandler($row);
			$blob = "Name: <highlight>{$row->name}<end>\n";
			$blob .= "Module: <highlight>{$row->module}<end>\n";
			$blob .= "Descrption: <highlight>{$row->description}<end>\n";
			$blob .= "Current Value: " . $settingHandler->displayValue() . "\n\n";
			$blob .= $settingHandler->getDescription();
			$blob .= $settingHandler->getOptions();

			// show help topic if there is one
			$help = $this->helpManager->find($settingName, $sender);
			if ($help !== false) {
				$blob .= "\n\n<header2>Help ($settingName)<end>\n\n" . $help;
			}

			$msg = $this->text->makeBlob("Settings Info for {$settingName}", $blob);
		}

		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("settings")
	 * @Matches("/^settings save ([a-z0-9_]+) (.+)$/i")
	 */
	public function saveCommand($message, $channel, $sender, $sendto, $args) {
		$name = strtolower($args[1]);
		$change_to_setting = $args[2];
		$row = $this->db->queryRow("SELECT * FROM settings_<myname> WHERE `name` = ?", $name);
		if ($row === null) {
			$msg = "Could not find setting <highlight>{$name}<end>.";
		} else {
			$settingHandler = $this->settingManager->getSettingHandler($row);
			try {
				$new_setting = $settingHandler->save($change_to_setting);
				if ($this->settingManager->save($name, $new_setting)) {
					$msg = "Setting <highlight>$name<end> has been saved.";
				} else {
					$msg = "Error! Setting <highlight>$name<end> could not be saved.";
				}
			} catch (Exception $e) {
				$msg = $e->getMessage();
			}
		}
		$sendto->reply($msg);
	}
}
