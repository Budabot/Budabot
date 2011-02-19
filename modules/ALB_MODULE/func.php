<?php
function loot_func($text, $first, $last, $newloot) {
	for ($i = $first; $i <= $last; $i++) {
		$ql = 250;
		if (isset($newloot[$i]["ql"])) {
			$ql = $newloot[$i]["ql"];
		}
		$recipeLink = Text::make_link("Tradeskill link to Recipebot", "/tell Recipebot search ".$newloot[$i]["name"], "chatcmd");
		$addlink = Text::make_link("Add to Loot List", "/tell <myname> albloot ".$i, "chatcmd");
		$ref = $newloot[$i]["ref"];
		$text .= Text::make_item($ref, $ref, $ql, "<img src=rdb://{$newloot[$i]["img"]}>");
		$text .= "\nItem: <highlight>".$newloot[$i]["name"]."<end>\n".$addlink."<end>\n".$recipeLink."\n\n";
	}
	
	$text .= "\n\nAlbtraum Module By Dare2005 (RK2)";
	return $text;
}
	
?>