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

use XoopsModules\Cocktails\Helper;
use XoopsModules\Cocktails\Utility;

/** @var Helper $helper */
/** @var Utility $utility */
/** @var \XoopsModules\Cocktails\RecipeHandler $recipeHandler */
/** @var \XoopsModules\Cocktails\CategoryHandler $categoryHandler */
/** @var \XoopsModules\Cocktails\FavoriteHandler $favoriteHandler */

$GLOBALS['xoopsOption']['template_main'] = 'cocktails_favorites.tpl';
require __DIR__ . '/header.php';

$cocktailsUrl = \Xoops\Helpers\Service\Url::module('cocktails');
$uid          = cocktails_current_uid();
if ($uid <= 0) {
    redirect_header($cocktailsUrl . '/index.php', 3, _MD_COCKTAILS_FAVORITE_LOGIN);
    exit;
}

require XOOPS_ROOT_PATH . '/header.php';
cocktails_register_theme_assets($stylesheet);
cocktails_register_scripts();

$recipes = [];
foreach ($favoriteHandler->recipeIdsForUser($uid) as $recipeId) {
    $recipeObject = $recipeHandler->get($recipeId);
    if (\is_object($recipeObject) && (1 === (int)$recipeObject->getVar('online') || cocktails_user_can_edit_recipe($recipeObject))) {
        $recipes[] = cocktails_recipe_card($recipeObject, $utility, $helper, $categoryHandler, $favoriteHandler);
    }
}

$GLOBALS['xoopsTpl']->assign('recipes', $recipes);
$GLOBALS['xoopsTpl']->assign('result_count', \count($recipes));
$GLOBALS['xoopsTpl']->assign('cocktails_url', $cocktailsUrl);
$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', _MD_COCKTAILS_MY_FAVORITES);
$GLOBALS['xoopsTpl']->assign('copyright', $copyright);

$utility::metaKeywords($helper->getConfig('keywords'));
$utility::metaDescription(_MD_COCKTAILS_MY_FAVORITES);

require XOOPS_ROOT_PATH . '/footer.php';
