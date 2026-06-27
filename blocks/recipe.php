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
 * Module: Cocktails — blocks
 *
 * @category        Module
 * @author          XOOPS Development Team <https://xoops.org>
 * @copyright       2000-2026 XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 */

use XoopsModules\Cocktails\Helper;
use XoopsModules\Cocktails\Utility;

require_once \dirname(__DIR__) . '/bootstrap.php';

/**
 * Shape a list of Recipe objects into compact block rows.
 *
 * @param \XoopsObject[] $recipes
 * @return array<int,array<string,mixed>>
 */
function cocktails_block_rows(array $recipes): array
{
    $moduleDirName = 'cocktails';
    $rows          = [];
    foreach ($recipes as $recipeObject) {
        $image = (string)$recipeObject->getVar('image');
        $rows[] = [
            'id'          => (int)$recipeObject->getVar('id'),
            'title'       => (string)$recipeObject->getVar('title'),
            'url'         => $recipeObject->url(),
            'image_url'   => ('' !== $image && 'blank.png' !== $image)
                ? \Xoops\Helpers\Service\Url::moduleUpload($moduleDirName, 'recipe/' . \rawurlencode($image))
                : '',
            'rating_avg'  => \round((float)$recipeObject->getVar('rating_avg'), 1),
            'rating_html' => Utility::renderStars((float)$recipeObject->getVar('rating_avg')),
            'rating_count'=> (int)$recipeObject->getVar('rating_count'),
        ];
    }

    return $rows;
}

/**
 * Top rated cocktails.
 */
function showCocktailsTopRated($options)
{
    $helper = Helper::getInstance();
    $helper->loadLanguage('blocks');
    /** @var \XoopsModules\Cocktails\RecipeHandler $recipeHandler */
    $recipeHandler = $helper->getHandler('Recipe');

    $limit = \max(1, (int)($options[0] ?? 5));
    $criteria = new \CriteriaCompo();
    $criteria->add(new \Criteria('online', 1));
    $criteria->add(new \Criteria('rating_count', '0', '>'));
    $criteria->setSort('rating_avg');
    $criteria->setOrder('DESC');
    $criteria->setLimit($limit);

    return ['recipes' => cocktails_block_rows(\array_values($recipeHandler->getAll($criteria)))];
}

/**
 * Newest cocktails.
 */
function showCocktailsNewest($options)
{
    $helper = Helper::getInstance();
    $helper->loadLanguage('blocks');
    /** @var \XoopsModules\Cocktails\RecipeHandler $recipeHandler */
    $recipeHandler = $helper->getHandler('Recipe');

    $limit = \max(1, (int)($options[0] ?? 5));
    $criteria = new \CriteriaCompo();
    $criteria->add(new \Criteria('online', 1));
    $criteria->setSort('created');
    $criteria->setOrder('DESC');
    $criteria->setLimit($limit);

    return ['recipes' => cocktails_block_rows(\array_values($recipeHandler->getAll($criteria)))];
}

/**
 * One (or more) random cocktail(s).
 */
function showCocktailsRandom($options)
{
    $helper = Helper::getInstance();
    $helper->loadLanguage('blocks');
    /** @var \XoopsModules\Cocktails\RecipeHandler $recipeHandler */
    $recipeHandler = $helper->getHandler('Recipe');

    $limit = \max(1, (int)($options[0] ?? 1));
    $total = $recipeHandler->getCount(new \Criteria('online', 1));
    if ($total <= 0) {
        return ['recipes' => []];
    }
    $start    = $total > $limit ? \random_int(0, $total - $limit) : 0;
    $criteria = new \CriteriaCompo();
    $criteria->add(new \Criteria('online', 1));
    $criteria->setLimit($limit);
    $criteria->setStart($start);

    return ['recipes' => cocktails_block_rows(\array_values($recipeHandler->getAll($criteria)))];
}

/**
 * Shared edit form: number of cocktails to display.
 */
function editCocktailsCount($options)
{
    $count = (int)($options[0] ?? 5);
    $form  = \constant('_MB_COCKTAILS_COUNT') . " &nbsp; <input type='text' name='options[0]' size='5' value='" . $count . "'>";

    return $form;
}
