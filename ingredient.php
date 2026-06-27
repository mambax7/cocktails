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

use Xmf\Request;
use XoopsModules\Cocktails\Domain\IngredientType;
use XoopsModules\Cocktails\Helper;
use XoopsModules\Cocktails\Utility;

/** @var Helper $helper */
/** @var Utility $utility */
/** @var \XoopsModules\Cocktails\RecipeHandler $recipeHandler */
/** @var \XoopsModules\Cocktails\IngredientHandler $ingredientHandler */
/** @var \XoopsModules\Cocktails\RecipeIngredientHandler $recipeIngredientHandler */
/** @var \XoopsModules\Cocktails\CategoryHandler $categoryHandler */
/** @var \XoopsModules\Cocktails\FavoriteHandler $favoriteHandler */

$GLOBALS['xoopsOption']['template_main'] = 'cocktails_ingredient.tpl';
require __DIR__ . '/header.php';

$cocktailsUrl = \Xoops\Helpers\Service\Url::module('cocktails');
$op           = Request::getString('op', 'list', 'REQUEST');

require XOOPS_ROOT_PATH . '/header.php';
cocktails_register_theme_assets($stylesheet);
cocktails_register_scripts();

if ('view' === $op) {
    // Recipes that use a given ingredient.
    $id  = Request::getInt('id', 0, 'GET');
    $ing = $ingredientHandler->get($id);
    if (!\is_object($ing)) {
        redirect_header($cocktailsUrl . '/ingredient.php', 3, _MD_COCKTAILS_NOT_FOUND);
        exit;
    }
    $ids     = $recipeIngredientHandler->recipeIdsForIngredient($id);
    $recipes = [];
    if ([] !== $ids) {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('id', '(' . \implode(',', $ids) . ')', 'IN'));
        $criteria->add(new \Criteria('online', 1));
        $criteria->setSort('rating_avg');
        $criteria->setOrder('DESC');
        foreach ($recipeHandler->getAll($criteria) as $recipeObject) {
            $recipes[] = cocktails_recipe_card($recipeObject, $utility, $helper, $categoryHandler, $favoriteHandler);
        }
    }
    $GLOBALS['xoopsTpl']->assign('view_mode', 'recipes');
    $GLOBALS['xoopsTpl']->assign('ingredient', [
        'id'    => (int)$ing->getVar('id'),
        'name'  => (string)$ing->getVar('name'),
        'type'  => IngredientType::fromInt((int)$ing->getVar('type'))->label(),
        'abv'   => (float)$ing->getVar('abv'),
        'description' => cocktails_render_rich_text((string)$ing->getVar('description', 'n')),
    ]);
    $GLOBALS['xoopsTpl']->assign('recipes', $recipes);
    $GLOBALS['xoopsTpl']->assign('result_count', \count($recipes));
    $GLOBALS['xoopsTpl']->assign('xoops_pagetitle', \sprintf(_MD_COCKTAILS_USES_INGREDIENT, $ing->getVar('name')));
} else {
    // Ingredient index grouped by type, with per-ingredient recipe counts.
    $criteria = new \CriteriaCompo();
    $criteria->add(new \Criteria('online', 1));
    $criteria->setSort('name');
    $criteria->setOrder('ASC');

    $groups = [];
    foreach (IngredientType::options() as $typeId => $typeLabel) {
        $groups[$typeId] = ['label' => $typeLabel, 'items' => []];
    }
    foreach ($ingredientHandler->getAll($criteria) as $ing) {
        $typeId = (int)$ing->getVar('type');
        if (!isset($groups[$typeId])) {
            $groups[$typeId] = ['label' => IngredientType::fromInt($typeId)->label(), 'items' => []];
        }
        $groups[$typeId]['items'][] = [
            'id'    => (int)$ing->getVar('id'),
            'name'  => (string)$ing->getVar('name'),
            'count' => $recipeIngredientHandler->countRecipesForIngredient((int)$ing->getVar('id')),
            'url'   => $cocktailsUrl . '/ingredient.php?op=view&id=' . (int)$ing->getVar('id'),
        ];
    }
    // Drop empty groups.
    $groups = \array_values(\array_filter($groups, static fn($g) => [] !== $g['items']));

    $GLOBALS['xoopsTpl']->assign('view_mode', 'index');
    $GLOBALS['xoopsTpl']->assign('ingredient_groups', $groups);
    $GLOBALS['xoopsTpl']->assign('xoops_pagetitle', _MD_COCKTAILS_BY_INGREDIENT);
}

$utility::metaKeywords($helper->getConfig('keywords'));
$utility::metaDescription(_MD_COCKTAILS_BY_INGREDIENT);
$GLOBALS['xoopsTpl']->assign('cocktails_url', $cocktailsUrl);
$GLOBALS['xoopsTpl']->assign('copyright', $copyright);

require XOOPS_ROOT_PATH . '/footer.php';
