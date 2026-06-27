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
use XoopsModules\Cocktails\Helper;
use XoopsModules\Cocktails\Utility;

/** @var Helper $helper */
/** @var Utility $utility */
/** @var \XoopsModules\Cocktails\RecipeHandler $recipeHandler */
/** @var \XoopsModules\Cocktails\CategoryHandler $categoryHandler */
/** @var \XoopsModules\Cocktails\FavoriteHandler $favoriteHandler */

$GLOBALS['xoopsOption']['template_main'] = 'cocktails_category.tpl';
require __DIR__ . '/header.php';

$cocktailsUrl = \Xoops\Helpers\Service\Url::module('cocktails');
$id           = Request::getInt('id', 0, 'REQUEST');
$category     = $categoryHandler->get($id);
if (!\is_object($category) || 0 === (int)$category->getVar('online')) {
    redirect_header($cocktailsUrl . '/index.php', 3, _MD_COCKTAILS_NOT_FOUND);
    exit;
}

require XOOPS_ROOT_PATH . '/header.php';
cocktails_register_theme_assets($stylesheet);
cocktails_register_scripts();

$start = Request::getInt('start', 0, 'GET');
$limit = \max(1, (int)$helper->getConfig('userpager'));

$criteria = new \CriteriaCompo();
$criteria->add(new \Criteria('online', 1));
$criteria->add(new \Criteria('cid', (string)$id));
$criteria->setSort('rating_avg');
$criteria->setOrder('DESC');
$total = $recipeHandler->getCount($criteria);
$criteria->setLimit($limit);
$criteria->setStart($start);

$recipes = [];
foreach ($recipeHandler->getAll($criteria) as $recipeObject) {
    $recipes[] = cocktails_recipe_card($recipeObject, $utility, $helper, $categoryHandler, $favoriteHandler);
}

$GLOBALS['xoopsTpl']->assign('category', [
    'id'          => (int)$category->getVar('id'),
    'title'       => (string)$category->getVar('title'),
    'description' => cocktails_render_rich_text((string)$category->getVar('description', 'n')),
    'color'       => (string)$category->getVar('color'),
]);
$GLOBALS['xoopsTpl']->assign('recipes', $recipes);
$GLOBALS['xoopsTpl']->assign('result_count', $total);
if ($total > $limit) {
    xoops_load('XoopsPageNav');
    $pagenav = new \XoopsPageNav($total, $limit, $start, 'start', 'id=' . $id);
    $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav(4));
}

$utility::metaKeywords($helper->getConfig('keywords') . ', ' . $category->getVar('title'));
$utility::metaDescription(\strip_tags((string)$category->getVar('description', 'n')) ?: _MD_COCKTAILS_INDEX_INTRO);

$GLOBALS['xoopsTpl']->assign('cocktails_url', $cocktailsUrl);
$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $category->getVar('title'));
$GLOBALS['xoopsTpl']->assign('copyright', $copyright);

require XOOPS_ROOT_PATH . '/footer.php';
