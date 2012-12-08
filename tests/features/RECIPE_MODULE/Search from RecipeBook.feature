Feature: Search from RecipeBook
	In order to build weird stuff
	As a tradeskiller
	I need to be able to search recipes from RecipeBook service

	Background:
		Given "RECIPE_MODULE" module is enabled
		And RecipeBook service is online

	Scenario: Search is succesful
		When I give command "!rb blood plasma"
		Then the response should contain phrase "Basic Treatment Laboratory/First-Aid Kit"
		And the response should contain phrase "Blood Plasma"
		And the response should contain phrase "Emergency Treatment Laboratory"

	Scenario: No recipes found
		When I give command "!rb non-existing thingy"
		Then the response should contain phrase "Error searching for recipe: Search could not be found"

