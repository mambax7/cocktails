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
use XoopsModules\Cocktails\Domain\Difficulty;
use XoopsModules\Cocktails\Helper;
use XoopsModules\Cocktails\Utility;

/** @var Helper $helper */
/** @var Utility $utility */
/** @var \XoopsModules\Cocktails\RecipeHandler $recipeHandler */
/** @var \XoopsModules\Cocktails\CategoryHandler $categoryHandler */
/** @var \XoopsModules\Cocktails\FavoriteHandler $favoriteHandler */
/** @var \XoopsModules\Cocktails\TagHandler $tagHandler */
/** @var \XoopsModules\Cocktails\RecipeIngredientHandler $recipeIngredientHandler */
/** @var \XoopsModules\Cocktails\IngredientHandler $ingredientHandler */

require __DIR__ . '/header.php';

$op = Request::getString('op', 'index', 'GET');

$GLOBALS['xoopsOption']['template_main'] = 'browse' === $op ? 'cocktails_recipe_list.tpl' : 'cocktails_index.tpl';

require XOOPS_ROOT_PATH . '/header.php';

global $xoTheme;
/** @var xos_opal_Theme $xoTheme */
cocktails_register_theme_assets($stylesheet);
cocktails_register_scripts();

$cocktailsUrl = \Xoops\Helpers\Service\Url::module('cocktails');

if ('browse' === $op) {
    // ---------------------------------------------------------------------
    // Browse with filters: cat, difficulty, sort, q, tag, ingredient
    // ---------------------------------------------------------------------
    $start = Request::getInt('start', 0, 'GET');
    $limit = \max(1, (int)$helper->getConfig('userpager'));

    $cat        = Request::getInt('cat', 0, 'GET');
    $difficulty = Request::getInt('difficulty', 0, 'GET');
    $sort       = Request::getString('sort', 'newest', 'GET');
    $q          = \trim(Request::getString('q', '', 'GET'));
    $tagId      = Request::getInt('tag', 0, 'GET');
    $ingredient = Request::getInt('ingredient', 0, 'GET');

    $criteria = new \CriteriaCompo();
    $criteria->add(new \Criteria('online', 1));

    if ($cat > 0) {
        $criteria->add(new \Criteria('cid', (string)$cat));
    }
    if (\in_array($difficulty, [1, 2, 3], true)) {
        $criteria->add(new \Criteria('difficulty', (string)$difficulty));
    }
    if ('' !== $q) {
        $escaped = $GLOBALS['xoopsDB']->escape($q);
        $search  = new \CriteriaCompo();
        $search->add(new \Criteria('title', '%' . $escaped . '%', 'LIKE'));
        $search->add(new \Criteria('summary', '%' . $escaped . '%', 'LIKE'), 'OR');
        $criteria->add($search);
    }
    // Restrict to a tag or ingredient via their id lists.
    if ($tagId > 0) {
        $ids = $tagHandler->recipeIdsForTag($tagId);
        $criteria->add(new \Criteria('id', '(' . ([] !== $ids ? \implode(',', $ids) : '0') . ')', 'IN'));
    }
    if ($ingredient > 0) {
        $ids = $recipeIngredientHandler->recipeIdsForIngredient($ingredient);
        $criteria->add(new \Criteria('id', '(' . ([] !== $ids ? \implode(',', $ids) : '0') . ')', 'IN'));
    }

    switch ($sort) {
        case 'rating':
            $criteria->setSort('rating_avg');
            $criteria->setOrder('DESC');
            break;
        case 'popular':
            $criteria->setSort('views');
            $criteria->setOrder('DESC');
            break;
        case 'title':
            $criteria->setSort('title');
            $criteria->setOrder('ASC');
            break;
        case 'newest':
        default:
            $sort = 'newest';
            $criteria->setSort('created');
            $criteria->setOrder('DESC');
            break;
    }

    $total = $recipeHandler->getCount($criteria);
    $criteria->setLimit($limit);
    $criteria->setStart($start);

    $recipes = [];
    foreach ($recipeHandler->getAll($criteria) as $recipeObject) {
        $recipes[] = cocktails_recipe_card($recipeObject, $utility, $helper, $categoryHandler, $favoriteHandler);
    }

    // Build base query for the filter form / pagination (preserve active filters).
    $baseParams = [];
    if ($cat > 0) {
        $baseParams['cat'] = $cat;
    }
    if ($difficulty > 0) {
        $baseParams['difficulty'] = $difficulty;
    }
    if ('' !== $q) {
        $baseParams['q'] = $q;
    }
    if ($tagId > 0) {
        $baseParams['tag'] = $tagId;
    }
    if ($ingredient > 0) {
        $baseParams['ingredient'] = $ingredient;
    }
    $baseParams['sort'] = $sort;
    $extraArgs = 'op=browse&' . \http_build_query($baseParams);

    if ($total > $limit) {
        xoops_load('XoopsPageNav');
        $pagenav = new \XoopsPageNav($total, $limit, $start, 'start', $extraArgs);
        $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav(4));
    }

    $GLOBALS['xoopsTpl']->assign('recipes', $recipes);
    $GLOBALS['xoopsTpl']->assign('result_count', $total);
    $GLOBALS['xoopsTpl']->assign('filters', [
        'cat'        => $cat,
        'difficulty' => $difficulty,
        'sort'       => $sort,
        'q'          => $q,
        'tag'        => $tagId,
        'ingredient' => $ingredient,
    ]);
    $GLOBALS['xoopsTpl']->assign('categories', $categoryHandler->getSelectList(true));
    $GLOBALS['xoopsTpl']->assign('difficulties', Difficulty::options());
    $GLOBALS['xoopsTpl']->assign('sort_options', [
        'rating'  => _MD_COCKTAILS_SORT_RATING,
        'newest'  => _MD_COCKTAILS_SORT_NEWEST,
        'popular' => _MD_COCKTAILS_SORT_POPULAR,
        'title'   => _MD_COCKTAILS_SORT_TITLE,
    ]);
    $utility::metaKeywords($helper->getConfig('keywords'));
    $utility::metaDescription(_MD_COCKTAILS_INDEX_INTRO);
} else {
    // ---------------------------------------------------------------------
    // Landing page: featured, top rated, newest + category chips.
    // ---------------------------------------------------------------------
    $shape = static function (\XoopsObject $recipeObject) use ($utility, $helper, $categoryHandler, $favoriteHandler): array {
        return cocktails_recipe_card($recipeObject, $utility, $helper, $categoryHandler, $favoriteHandler);
    };

    // Featured (online + featured), newest first.
    $featuredCriteria = new \CriteriaCompo();
    $featuredCriteria->add(new \Criteria('online', 1));
    $featuredCriteria->add(new \Criteria('featured', 1));
    $featuredCriteria->setSort('updated');
    $featuredCriteria->setOrder('DESC');
    $featuredCriteria->setLimit(6);
    $featured = \array_map($shape, \array_values($recipeHandler->getAll($featuredCriteria)));

    // Top rated.
    $topCriteria = new \CriteriaCompo();
    $topCriteria->add(new \Criteria('online', 1));
    $topCriteria->add(new \Criteria('rating_count', '0', '>'));
    $topCriteria->setSort('rating_avg');
    $topCriteria->setOrder('DESC');
    $topCriteria->setLimit(6);
    $topRated = \array_map($shape, \array_values($recipeHandler->getAll($topCriteria)));

    // Newest.
    $newestCriteria = new \CriteriaCompo();
    $newestCriteria->add(new \Criteria('online', 1));
    $newestCriteria->setSort('created');
    $newestCriteria->setOrder('DESC');
    $newestCriteria->setLimit(6);
    $newest = \array_map($shape, \array_values($recipeHandler->getAll($newestCriteria)));

    // Category chips.
    $catCriteria = new \CriteriaCompo();
    $catCriteria->add(new \Criteria('online', 1));
    $catCriteria->setSort('weight ASC, title');
    $catCriteria->setOrder('ASC');
    $categories = [];
    foreach ($categoryHandler->getAll($catCriteria) as $categoryObject) {
        $categories[] = [
            'id'    => (int)$categoryObject->getVar('id'),
            'title' => (string)$categoryObject->getVar('title'),
            'color' => (string)$categoryObject->getVar('color'),
            'url'   => \Xoops\Helpers\Service\Url::module('cocktails', 'category.php', ['op' => 'view', 'id' => (int)$categoryObject->getVar('id')]),
        ];
    }

    $onlineCriteria = new \CriteriaCompo(new \Criteria('online', 1));

    $GLOBALS['xoopsTpl']->assign('featured', $featured);
    $GLOBALS['xoopsTpl']->assign('top_rated', $topRated);
    $GLOBALS['xoopsTpl']->assign('newest', $newest);
    $GLOBALS['xoopsTpl']->assign('categories', $categories);
    $GLOBALS['xoopsTpl']->assign('can_submit', cocktails_user_can_submit());
    $GLOBALS['xoopsTpl']->assign('cocktails_stats', [
        'recipes'     => $recipeHandler->getCount($onlineCriteria),
        'categories'  => $categoryHandler->getCount(new \Criteria('online', 1)),
        'ingredients' => $ingredientHandler->getCount(new \Criteria('online', 1)),
    ]);

    $utility::metaKeywords($helper->getConfig('keywords'));
    $utility::metaDescription(_MD_COCKTAILS_INDEX_INTRO);
}

$GLOBALS['xoopsTpl']->assign('xoops_mpageurl', $cocktailsUrl . '/index.php');
$GLOBALS['xoopsTpl']->assign('cocktails_url', $cocktailsUrl);
$GLOBALS['xoopsTpl']->assign('bookmarks', $helper->getConfig('bookmarks'));
$GLOBALS['xoopsTpl']->assign('fbcomments', 0);
$GLOBALS['xoopsTpl']->assign('admin', COCKTAILS_ADMIN);
$GLOBALS['xoopsTpl']->assign('copyright', $copyright);

require XOOPS_ROOT_PATH . '/footer.php';
