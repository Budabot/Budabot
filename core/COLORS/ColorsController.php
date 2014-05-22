<?php

namespace Budabot\Core\Modules;

/**
 * @Instance
 */
class ColorsController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/**
	 * @Setting("default_guild_color")
	 * @Description("default guild color")
	 * @Visibility("edit")
	 * @Type("color")
	 * @AccessLevel("mod")
	 */
	public $defaultDefaultGuildColor = "<font color='#89D2E8'>";

	/**
	 * @Setting("default_priv_color")
	 * @Description("default private channel color")
	 * @Visibility("edit")
	 * @Type("color")
	 * @AccessLevel("mod")
	 */
	public $defaultDefaultPrivColor = "<font color='#89D2E8'>";

	/**
	 * @Setting("default_window_color")
	 * @Description("default window color")
	 * @Visibility("edit")
	 * @Type("color")
	 * @AccessLevel("mod")
	 */
	public $defaultDefaultWindowColor = "<font color='#89D2E8'>";

	/**
	 * @Setting("default_tell_color")
	 * @Description("default tell color")
	 * @Visibility("edit")
	 * @Type("color")
	 * @AccessLevel("mod")
	 */
	public $defaultDefaultTellColor = "<font color='#89D2E8'>";

	/**
	 * @Setting("default_highlight_color")
	 * @Description("default highlight color")
	 * @Visibility("edit")
	 * @Type("color")
	 * @AccessLevel("mod")
	 */
	public $defaultDefaultHighlightColor = "<font color='#FFFFFF'>";

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
	public $defaultDefaultOmniColor = "<font color='#FA8484'>";

	/**
	 * @Setting("default_neut_color")
	 * @Description("default neut color")
	 * @Visibility("edit")
	 * @Type("color")
	 * @AccessLevel("mod")
	 */
	public $defaultDefaultNeutColor = "<font color='#E6E1A6'>";

	/**
	 * @Setting("default_unknown_color")
	 * @Description("default unknown color")
	 * @Visibility("edit")
	 * @Type("color")
	 * @AccessLevel("mod")
	 */
	public $defaultDefaultUnknownColor = "<font color='#FF0000'>";
}
