<?php
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
	 * @Setting("default_guild_color")
	 * @Description("default guild color")
	 * @Visibility("edit")
	 * @Type("color")
	 * @AccessLevel("mod")
	 */
	public $defaultDefaultGuildColor = "<font color='#84FFFF'>";

	/**
	 * @Setting("default_priv_color")
	 * @Description("default private channel color")
	 * @Visibility("edit")
	 * @Type("color")
	 * @AccessLevel("mod")
	 */
	public $defaultDefaultPrivColor = "<font color='#84FFFF'>";

	/**
	 * @Setting("default_window_color")
	 * @Description("default window color")
	 * @Visibility("edit")
	 * @Type("color")
	 * @AccessLevel("mod")
	 */
	public $defaultDefaultWindowColor = "<font color='#84FFFF'>";

	/**
	 * @Setting("default_tell_color")
	 * @Description("default tell color")
	 * @Visibility("edit")
	 * @Type("color")
	 * @AccessLevel("mod")
	 */
	public $defaultDefaultTellColor = "<font color='#DDDDDD'>";

	/**
	 * @Setting("default_highlight_color")
	 * @Description("default highlight color")
	 * @Visibility("edit")
	 * @Type("color")
	 * @AccessLevel("mod")
	 */
	public $defaultDefaultHighlightColor = "<font color='#9CC6E7'>";

	/**
	 * @Setting("default_header_color")
	 * @Description("default header color")
	 * @Visibility("edit")
	 * @Type("color")
	 * @AccessLevel("mod")
	 */
	public $defaultDefaultHeaderColor = "<font color='#FFFF00'>";
	
	/**
	 * @Setting("default_header2_color")
	 * @Description("default header2 color")
	 * @Visibility("edit")
	 * @Type("color")
	 * @AccessLevel("mod")
	 */
	public $defaultDefaultHeader2Color = "<font color='#FCA712'>";

	/**
	 * @Setting("default_clan_color")
	 * @Description("default clan color")
	 * @Visibility("edit")
	 * @Type("color")
	 * @AccessLevel("mod")
	 */
	public $defaultDefaultClanColor = "<font color='#F79410'>";

	/**
	 * @Setting("default_omni_color")
	 * @Description("default omni color")
	 * @Visibility("edit")
	 * @Type("color")
	 * @AccessLevel("mod")
	 */
	public $defaultDefaultOmniColor = "<font color='#00FFFF'>";

	/**
	 * @Setting("default_neut_color")
	 * @Description("default neut color")
	 * @Visibility("edit")
	 * @Type("color")
	 * @AccessLevel("mod")
	 */
	public $defaultDefaultNeutColor = "<font color='#EEEEEE'>";

	/**
	 * @Setting("default_unknown_color")
	 * @Description("default unknown color")
	 * @Visibility("edit")
	 * @Type("color")
	 * @AccessLevel("mod")
	 */
	public $defaultDefaultUnknownColor = "<font color='#FF0000'>";

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
		$data = $this->db->query("SELECT * FROM settings_<myname> WHERE `mode` != 'hide' ORDER BY `module`");
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

		$msg = $this->text->make_blob("Bot Settings", $blob);
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

			$msg = $this->text->make_blob("Settings Info for {$settingName}", $blob);
		}

		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("settings")
	 * @Matches("/^settings save ([a-z0-9_]+) (.+)$/i")
	 */
	public function saveCommand($message, $channel, $sender, $sendto, $args) {
		$name_setting = strtolower($args[1]);
		$change_to_setting = $args[2];
		$row = $this->db->queryRow("SELECT * FROM settings_<myname> WHERE `name` = ?", $name_setting);
		if ($row === null) {
			$msg = "Could not find setting <highlight>{$name_setting}<end>.";
		} else {
			$options = explode(";", $row->options);
			$new_setting = "";
			if ($row->type == "color") {
				if (preg_match("/^#([0-9a-f]{6})$/i", $change_to_setting)) {
					$new_setting = "<font color='$change_to_setting'>";
				} else {
					$msg = "<highlight>{$change_to_setting}<end> is not a valid HTML-Color (example: '#FF33DD').";
				}
			} else if ($row->type == "text") {
				if (strlen($change_to_setting) > 255) {
					$msg = "Your text can not be longer than 255 characters.";
				} else {
					$new_setting = $change_to_setting;
				}
			} else if ($row->type == "number") {
				if (preg_match("/^[0-9]+$/i", $change_to_setting)) {
					$new_setting = $change_to_setting;
				} else {
					$msg = "You must enter a number for this setting.";
				}
			} else if ($row->type == "options") {
				if ($row->intoptions != '') {
					$intoptions = explode(";", $row->intoptions);
					if (in_array($change_to_setting, $intoptions)) {
						$new_setting = $change_to_setting;
					} else {
						$msg = "This is not a correct option for this setting.";
					}
				} else {
					if (in_array($change_to_setting, $options)) {
						$new_setting = $change_to_setting;
					} else {
						$msg = "This is not a correct option for this setting.";
					}
				}
			} else if ($row->type == "time") {
				$time = $this->util->parseTime($change_to_setting);
				if ($time > 0) {
					$new_setting = $time;
				} else {
					$msg = "This is not a valid time for this setting.";
				}
			}
		}
		if ($new_setting != "") {
			if ($this->settingManager->save($name_setting, $new_setting)) {
				$msg = "Setting successfull saved.";
			} else {
				$msg = "Error! Setting could not be saved.";
			}
		}
		$sendto->reply($msg);
	}
}
