<?php declare(strict_types=1);

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/
/**
 * Module: Cocktails
 *
 * @category        Module
 * @author          XOOPS Development Team <https://xoops.org>
 * @copyright       2000-2026 XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 */

// Admin menu
\define('_AM_COCKTAILS_ADMIN_PAGER', 'Items per page');
\define('_AM_COCKTAILS_DASHBOARD', 'Dashboard');
\define('_AM_COCKTAILS_RECIPES', 'Recipes');
\define('_AM_COCKTAILS_INGREDIENTS', 'Ingredients');
\define('_AM_COCKTAILS_CATEGORIES', 'Categories');
\define('_AM_COCKTAILS_GLASSES', 'Glassware');
\define('_AM_COCKTAILS_TAGS', 'Tags');
\define('_AM_COCKTAILS_PERMISSIONS', 'Permissions');
\define('_AM_COCKTAILS_BLOCKSADMIN', 'Blocks Admin');
\define('_AM_COCKTAILS_FEEDBACK', 'Feedback');
\define('_AM_COCKTAILS_ABOUT', 'About');
\define('_AM_COCKTAILS_MIGRATE', 'Migrate');

// Dashboard / statistics
\define('_AM_COCKTAILS_STATISTICS', 'Cocktails statistics');
\define('_AM_COCKTAILS_THEREARE_RECIPE', 'There are <strong>%s</strong> recipes');
\define('_AM_COCKTAILS_THEREARE_INGREDIENT', 'There are <strong>%s</strong> ingredients');
\define('_AM_COCKTAILS_THEREARE_CATEGORY', 'There are <strong>%s</strong> categories');
\define('_AM_COCKTAILS_THEREARE_GLASS', 'There are <strong>%s</strong> glasses');
\define('_AM_COCKTAILS_THEREARE_RATING', 'There are <strong>%s</strong> ratings');
\define('_AM_COCKTAILS_THEREARE_PENDING', 'There are <strong>%s</strong> recipes awaiting moderation');

// Generic form messages
\define('_AM_COCKTAILS_FORMOK', 'Saved successfully.');
\define('_AM_COCKTAILS_FORMDELOK', 'Deleted successfully.');
\define('_AM_COCKTAILS_FORMSUREDEL', 'Are you sure you want to delete: <strong>%s</strong>?');
\define('_AM_COCKTAILS_FORMSURECLONE', 'Are you sure you want to clone item id %s?');
\define('_AM_COCKTAILS_CLONED_OK', 'Item cloned successfully.');
\define('_AM_COCKTAILS_CLONED_FAILED', 'Clone failed.');
\define('_AM_COCKTAILS_NONE_YET', 'No items have been created yet.');
\define('_AM_COCKTAILS_FORM_ACTION', 'Action');
\define('_AM_COCKTAILS_SELECT', 'With selected');
\define('_AM_COCKTAILS_SELECTED_DELETE', 'Delete');
\define('_AM_COCKTAILS_SELECTED_ERROR', 'Please select at least one item.');
\define('_AM_COCKTAILS_NO_RECORDS', 'No records found.');
\define('_AM_COCKTAILS_ADMIN_HELP', 'Cocktails — Help');

// Recipe
\define('_AM_COCKTAILS_RECIPE_LIST', 'Recipe list');
\define('_AM_COCKTAILS_ADD_RECIPE', 'Add recipe');
\define('_AM_COCKTAILS_RECIPE_ADD', 'Add a recipe');
\define('_AM_COCKTAILS_RECIPE_EDIT', 'Edit recipe');
\define('_AM_COCKTAILS_RECIPE_ID', 'ID');
\define('_AM_COCKTAILS_RECIPE_TITLE', 'Title');
\define('_AM_COCKTAILS_RECIPE_CID', 'Category');
\define('_AM_COCKTAILS_RECIPE_GLASS', 'Glass');
\define('_AM_COCKTAILS_RECIPE_DIFFICULTY', 'Difficulty');
\define('_AM_COCKTAILS_RECIPE_PREPTIME', 'Prep time (minutes)');
\define('_AM_COCKTAILS_RECIPE_SERVINGS', 'Servings');
\define('_AM_COCKTAILS_RECIPE_ALCOHOLIC', 'Alcoholic?');
\define('_AM_COCKTAILS_RECIPE_SUMMARY', 'Short summary');
\define('_AM_COCKTAILS_RECIPE_SUMMARY_DESC', 'One-line teaser used in lists and search results.');
\define('_AM_COCKTAILS_RECIPE_METHOD', 'Method / instructions');
\define('_AM_COCKTAILS_RECIPE_GARNISH', 'Garnish');
\define('_AM_COCKTAILS_RECIPE_IMAGE', 'Photo');
\define('_AM_COCKTAILS_RECIPE_TAGS', 'Tags');
\define('_AM_COCKTAILS_RECIPE_TAGS_DESC', 'Comma-separated tags (e.g. "summer, refreshing, citrus").');
\define('_AM_COCKTAILS_RECIPE_FEATURED', 'Featured');
\define('_AM_COCKTAILS_RECIPE_ONLINE', 'Online');
\define('_AM_COCKTAILS_RECIPE_RATING', 'Rating');
\define('_AM_COCKTAILS_RECIPE_VIEWS', 'Views');
\define('_AM_COCKTAILS_RECIPE_CREATED', 'Created');
\define('_AM_COCKTAILS_RECIPE_UPDATED', 'Updated');
\define('_AM_COCKTAILS_SUBMITTER', 'Submitter');

// Measured ingredients (rich editor)
\define('_AM_COCKTAILS_INGLINES', 'Ingredients (measured)');
\define('_AM_COCKTAILS_INGLINES_DESC', 'Add one row per ingredient. Pick the ingredient, the amount and a unit; optional note for things like "freshly squeezed".');
\define('_AM_COCKTAILS_ING_AMOUNT', 'Amount');
\define('_AM_COCKTAILS_ING_UNIT', 'Unit');
\define('_AM_COCKTAILS_ING_NOTE', 'Note');
\define('_AM_COCKTAILS_ING_OPTIONAL', 'Optional');
\define('_AM_COCKTAILS_ING_ADDROW', 'Add ingredient');
\define('_AM_COCKTAILS_ING_REMOVE', 'Remove');
\define('_AM_COCKTAILS_ING_PICK', '— choose ingredient —');

// Ingredient (master list)
\define('_AM_COCKTAILS_INGREDIENT_LIST', 'Ingredient list');
\define('_AM_COCKTAILS_ADD_INGREDIENT', 'Add ingredient');
\define('_AM_COCKTAILS_INGREDIENT_ADD', 'Add an ingredient');
\define('_AM_COCKTAILS_INGREDIENT_EDIT', 'Edit ingredient');
\define('_AM_COCKTAILS_INGREDIENT_NAME', 'Name');
\define('_AM_COCKTAILS_INGREDIENT_TYPE', 'Type');
\define('_AM_COCKTAILS_INGREDIENT_ABV', 'ABV (% alcohol)');
\define('_AM_COCKTAILS_INGREDIENT_DESC', 'Description');
\define('_AM_COCKTAILS_INGREDIENT_IMAGE', 'Image');
\define('_AM_COCKTAILS_INGREDIENT_WEIGHT', 'Weight');
\define('_AM_COCKTAILS_INGREDIENT_ONLINE', 'Online');

// Category
\define('_AM_COCKTAILS_CATEGORY_LIST', 'Category list');
\define('_AM_COCKTAILS_ADD_CATEGORY', 'Add category');
\define('_AM_COCKTAILS_CATEGORY_ADD', 'Add a category');
\define('_AM_COCKTAILS_CATEGORY_EDIT', 'Edit category');
\define('_AM_COCKTAILS_CATEGORY_ID', 'ID');
\define('_AM_COCKTAILS_CATEGORY_PID', 'Parent');
\define('_AM_COCKTAILS_CATEGORY_TITLE', 'Title');
\define('_AM_COCKTAILS_CATEGORY_DESC', 'Description');
\define('_AM_COCKTAILS_CATEGORY_IMAGE', 'Image');
\define('_AM_COCKTAILS_CATEGORY_COLOR', 'Accent color');
\define('_AM_COCKTAILS_CATEGORY_WEIGHT', 'Weight');
\define('_AM_COCKTAILS_CATEGORY_ONLINE', 'Online');
\define('_AM_COCKTAILS_CATEGORY_NONE', '— none (top level) —');

// Glass
\define('_AM_COCKTAILS_GLASS_LIST', 'Glass list');
\define('_AM_COCKTAILS_ADD_GLASS', 'Add glass');
\define('_AM_COCKTAILS_GLASS_ADD', 'Add a glass');
\define('_AM_COCKTAILS_GLASS_EDIT', 'Edit glass');
\define('_AM_COCKTAILS_GLASS_NAME', 'Name');
\define('_AM_COCKTAILS_GLASS_DESC', 'Description');
\define('_AM_COCKTAILS_GLASS_IMAGE', 'Image');
\define('_AM_COCKTAILS_GLASS_WEIGHT', 'Weight');

// Tag
\define('_AM_COCKTAILS_TAG_LIST', 'Tag list');
\define('_AM_COCKTAILS_ADD_TAG', 'Add tag');
\define('_AM_COCKTAILS_TAG_ADD', 'Add a tag');
\define('_AM_COCKTAILS_TAG_EDIT', 'Edit tag');
\define('_AM_COCKTAILS_TAG_NAME', 'Name');
\define('_AM_COCKTAILS_TAG_USES', 'Uses');

// Permissions
\define('_AM_COCKTAILS_PERM_GLOBAL', 'Global access rights');
\define('_AM_COCKTAILS_PERM_READ', 'Category read permissions');
\define('_AM_COCKTAILS_PERM_AC_SUBMIT', 'Submit recipes from the user side');
\define('_AM_COCKTAILS_PERM_AC_AUTO_APPROVE', 'Auto-approve own submissions');
\define('_AM_COCKTAILS_PERM_AC_EDIT_OWN', 'Edit own recipes');
\define('_AM_COCKTAILS_PERM_AC_RATE', 'Rate recipes');

// Permissions
\define('_AM_COCKTAILS_PERMISSIONS_GPERMUPDATED', 'Permissions updated.');
\define('_AM_COCKTAILS_PERMISSIONS_GLOBAL', 'Global access rights');
\define('_AM_COCKTAILS_PERMISSIONS_GLOBAL_DESC', 'Choose which groups have each global right.');
\define('_AM_COCKTAILS_PERMISSIONS_READ', 'Category read');
\define('_AM_COCKTAILS_PERMISSIONS_READ_DESC', 'Choose which groups can view each category.');
\define('_AM_COCKTAILS_PERMISSIONS_NOPERMSSET', 'Please create a category first.');

// About
\define('_AM_COCKTAILS_DIRNAME', 'Directory name');
\define('_AM_COCKTAILS_NEED_TITLE', 'A recipe title is required.');
