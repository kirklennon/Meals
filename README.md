# Meals

This is a family meal planning web application created in PHP. The main page contains a static listing of meal slots for one week at a time such as "Thursday Dinner" or "Sunday Breakfast" and you can assign different meals from the database to each slot. You can create any number of new meals, giving each one a title, ingredients, steps, and a reference URL. The ingredients are added using an autocompleting text field that searches as you type for matching ingredients in the database (results returned using JSON). Any new ingredients in a recipe are added to the database.

## Minimum Viable Product

The core functionality is complete, and a meal's title, steps, and URL can be edited. I finally added the ability to remove ingredients from a meal but it needs additional CSS styling to make it more clear. You cannot delete a meal entirely. You can reassign a different meal to a slot but there is no way to assign no meal. I added the ability to clear out all slots to start a new week.

## Security

To make testing and demoing easy, I have not yet added login functionality.

## Demo

A live demo is available at [lennon.dev/meals](https://lennon.dev/meals)
