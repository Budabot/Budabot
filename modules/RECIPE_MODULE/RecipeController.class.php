<?php

namespace Budabot\User\Modules;

/**
 * Authors:
 *  - Tyrence
 *
 * Based on a module written by Captainzero (RK1) of the same name for an earlier version of Budabot
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'recipe',
 *		accessLevel = 'all',
 *		description = 'Search for a recipe',
 *		help        = 'recipe.txt'
 *	)
 */
class RecipeController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $db;

	/** @Inject */
	public $http;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $itemsController;
	
	private $baseUrl = "http://aodevnet.com/recipes/api";

	/** @Setup */
	public function setup() {
		
	}
	
	/**
	 * @HandlesCommand("recipe")
	 * @Matches("/^recipe ([0-9]+)$/i")
	 */
	public function recipeShowCommand($message, $channel, $sender, $sendto, $args) {
		$id = $args[1];
		
		$url = "/show/id/" . $id . "/format/json/bot/budabot";
		$that = $this;
		$this->http->get($this->baseUrl . $url)->withCallback(function($response) use ($that, $sendto) {
			$obj = json_decode($response->body);
			if (!empty($obj->error)) {
				$msg = "Error showing recipe: " . $obj->error;
			} else {
				$recipe_name = $obj->recipe_name;
				$author = empty($obj->recipe_author) ? "Unknown" : $obj->recipe_author;

				$recipeText = "Author: <highlight>$author<end>\n\n";
				$recipeText .= $that->formatRecipeText($obj->recipe_text);

				$recipeText .= $that->getAORecipebookFooter();

				$msg = $that->text->make_blob("Recipe for $recipe_name", $recipeText);
			}
			$sendto->reply($msg);
		});
	}
	
	/**
	 * @HandlesCommand("recipe")
	 * @Matches("/^recipe (.+)$/i")
	 */
	public function recipeSearchCommand($message, $channel, $sender, $sendto, $args) {
		if (preg_match('/<a href="itemref:\/\/(\d+)\/(\d+)\/(\d+)">([^<]+)<\/a>/', $args[1], $matches)) {
			$lowId = $matches[1];
			$search = $matches[4];
			
			$url = "/byitem/id/$lowId/mode/default/format/json/bot/budabot";
		} else {
			$search = $args[1];
			
			$url = "/search/kw/" . rawurlencode($search) . "/mode/default/format/json/bot/budabot";
		}

		$that = $this;
		$this->http->get($this->baseUrl . $url)->withCallback(function($response) use ($that, $search, $sendto) {
			$obj = json_decode($response->body);
			if (!empty($obj->error)) {
				$msg = "Error searching for recipe: " . $obj->error;
			} else {
				$blob = '';
				$count = count($obj);
				forEach ($obj as $recipe) {
					$blob .= $that->text->make_chatcmd($recipe->recipe_name, "/tell <myname> recipe $recipe->recipe_id") . "\n";
				}

				$blob .= $that->getAORecipebookFooter();

				$msg = $that->text->make_blob("Recipes matching '$search' ($count)", $blob);
			}
			$sendto->reply($msg);
		});
	}
	
	public function getAORecipebookFooter() {
		return "\n\n<highlight>Powered by " . $this->text->make_chatcmd("AORecipebook.com", "/start http://aorecipebook.com") . "<end>\n" .
			"For more information, " . $this->text->make_chatcmd("/tell recipebook about", "/tell recipebook about");
	}
	
	public function formatRecipeText($input) {
		$input = str_replace("\\n", "\n", $input);
		$input = preg_replace_callback('/#L "([^"]+)" "([0-9]+)"/', array($this, 'replaceItem'), $input);
		$input = preg_replace('/#L "([^"]+)" "([^"]+)"/', "<a href='chatcmd://\\2'>\\1</a>", $input);

		$input = str_replace("#C09","</font><font color=#FFFFFF>",
			str_replace("#C12","</font><font color=#FF0000>",
			str_replace("#C13","</font><font color=#FFFFFF>",
			str_replace("#C14","</font><font color=#FFFFFF>",
			str_replace("#C15","</font><font color=#FFFFFF>",
			str_replace("#C16","</font><font color=#FFFF00>",
			str_replace("#C18","</font><font color=#AAFF00>",
			str_replace("#C20","</font><font color=#009B00>",$input))))))));
			
		return $input;
	}

	private function replaceItem($arr) {
		$id = $arr[2];
		$row = $this->itemsController->findById($id);
		if ($row !== null) {
			$output = $this->text->make_item($row->lowid, $row->highid, $row->highql, $row->name);
		} else {
			$obj = $this->itemsController->doXyphosLookup($id);
			if (null == $obj) {
				$output = "#L \"{$arr[1]}\" \"/tell <myname> itemid {$arr[2]}\"";
			} else if ($obj->icon == 0) {  // for perks and items that aren't displayable in game
				$output = $this->text->make_chatcmd($obj->name, "/start http://www.xyphos.com/ao/aodb.php?id={$obj->lowid}");
			} else {
				$output = $this->text->make_item($obj->lowid, $obj->highid, $obj->highql, $obj->name);
			}
		}
		return $output;
	}
}

