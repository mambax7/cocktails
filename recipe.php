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
/** @var \XoopsModules\Cocktails\RecipeIngredientHandler $recipeIngredientHandler */
/** @var \XoopsModules\Cocktails\IngredientHandler $ingredientHandler */
/** @var \XoopsModules\Cocktails\CategoryHandler $categoryHandler */
/** @var \XoopsModules\Cocktails\GlassHandler $glassHandler */
/** @var \XoopsModules\Cocktails\TagHandler $tagHandler */
/** @var \XoopsModules\Cocktails\RatingHandler $ratingHandler */
/** @var \XoopsModules\Cocktails\FavoriteHandler $favoriteHandler */

require __DIR__ . '/header.php';

$op           = Request::getString('op', 'list', 'REQUEST');
$cocktailsUrl = \Xoops\Helpers\Service\Url::module('cocktails');

/**
 * Detect whether this is an AJAX request (fetch from cocktails.js sets ajax=1 / X-Requested-With).
 */
$cocktailsIsAjax = static function (): bool {
    if (1 === Request::getInt('ajax', 0, 'REQUEST')) {
        return true;
    }
    $header = (string)($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');

    return 'xmlhttprequest' === \strtolower($header);
};

/**
 * Emit a JSON payload and stop (used by rate/favorite AJAX responses).
 */
$cocktailsJson = static function (array $payload): void {
    while (\ob_get_level() > 0) {
        \ob_end_clean();
    }
    \header('Content-Type: application/json; charset=utf-8');
    echo \json_encode($payload, \JSON_THROW_ON_ERROR);
    exit;
};

switch ($op) {
    // -----------------------------------------------------------------
    case 'rate':
        $recipeId = Request::getInt('id', 0, 'POST');
        $stars    = Request::getInt('stars', 0, 'POST');
        $uid      = cocktails_current_uid();

        if (!$GLOBALS['xoopsSecurity']->check()) {
            if ($cocktailsIsAjax()) {
                $cocktailsJson(['ok' => false, 'error' => \implode(', ', $GLOBALS['xoopsSecurity']->getErrors())]);
            }
            redirect_header($cocktailsUrl . '/recipe.php', 3, \implode(', ', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        if (!cocktails_user_can_rate()) {
            if ($cocktailsIsAjax()) {
                $cocktailsJson(['ok' => false, 'error' => _MD_COCKTAILS_RATE_LOGIN]);
            }
            redirect_header($cocktailsUrl . '/recipe.php?op=view&id=' . $recipeId, 3, _MD_COCKTAILS_RATE_LOGIN);
        }

        $recipeObject = $recipeHandler->get($recipeId);
        $ok           = \is_object($recipeObject) && $uid > 0 && $ratingHandler->rate($recipeId, $uid, $stars);
        $fresh        = $recipeHandler->get($recipeId);

        if ($cocktailsIsAjax()) {
            $cocktailsJson([
                'ok'    => $ok,
                'avg'   => \is_object($fresh) ? \round((float)$fresh->getVar('rating_avg'), 1) : 0,
                'count' => \is_object($fresh) ? (int)$fresh->getVar('rating_count') : 0,
                'stars' => $stars,
            ]);
        }
        redirect_header($cocktailsUrl . '/recipe.php?op=view&id=' . $recipeId, 2, _MD_COCKTAILS_RATE_THANKS);
        break;

    // -----------------------------------------------------------------
    case 'favorite':
        $recipeId = Request::getInt('id', 0, 'POST');
        $uid      = cocktails_current_uid();

        if (!$GLOBALS['xoopsSecurity']->check()) {
            if ($cocktailsIsAjax()) {
                $cocktailsJson(['ok' => false, 'error' => \implode(', ', $GLOBALS['xoopsSecurity']->getErrors())]);
            }
            redirect_header($cocktailsUrl . '/recipe.php', 3, \implode(', ', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        if ($uid <= 0) {
            if ($cocktailsIsAjax()) {
                $cocktailsJson(['ok' => false, 'error' => _MD_COCKTAILS_FAVORITE_LOGIN]);
            }
            redirect_header($cocktailsUrl . '/recipe.php?op=view&id=' . $recipeId, 3, _MD_COCKTAILS_FAVORITE_LOGIN);
        }

        $state = $favoriteHandler->toggle($recipeId, $uid);

        if ($cocktailsIsAjax()) {
            $cocktailsJson(['ok' => true, 'state' => $state]);
        }
        redirect_header($cocktailsUrl . '/recipe.php?op=view&id=' . $recipeId, 2, $state ? _MD_COCKTAILS_FAVORITE_ADD : _MD_COCKTAILS_FAVORITE_REMOVE);
        break;

    // -----------------------------------------------------------------
    case 'edit':
        $editId       = Request::getInt('id', 0, 'GET');
        $recipeObject = $editId > 0 ? $recipeHandler->get($editId) : $recipeHandler->create();
        if ($editId > 0) {
            if (!cocktails_user_can_edit_recipe($recipeObject)) {
                redirect_header($cocktailsUrl . '/recipe.php', 3, _MD_COCKTAILS_NOPERM);
            }
        } elseif (!cocktails_user_can_submit()) {
            redirect_header($cocktailsUrl . '/recipe.php', 3, _MD_COCKTAILS_NOPERM);
        }
        require XOOPS_ROOT_PATH . '/header.php';
        cocktails_register_theme_assets($stylesheet);
        cocktails_register_scripts();
        echo '<div class="cocktails-shell"><div class="cocktails-form-wrap">';
        $recipeObject->getForm()->display();
        echo '</div></div>';
        require XOOPS_ROOT_PATH . '/footer.php';
        break;

    // -----------------------------------------------------------------
    case 'save':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($cocktailsUrl . '/recipe.php', 3, \implode(', ', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $saveId = Request::getInt('id', 0, 'POST');
        if ($saveId > 0) {
            if (!cocktails_user_can_edit_recipe($recipeHandler->get($saveId))) {
                redirect_header($cocktailsUrl . '/recipe.php', 3, _MD_COCKTAILS_NOPERM);
            }
        } elseif (!cocktails_user_can_submit()) {
            redirect_header($cocktailsUrl . '/recipe.php', 3, _MD_COCKTAILS_NOPERM);
        }

        $result = $recipeHandler->saveFromRequest($helper, cocktails_is_admin());
        if (!$result['ok']) {
            require XOOPS_ROOT_PATH . '/header.php';
            cocktails_register_theme_assets($stylesheet);
            cocktails_register_scripts();
            echo '<div class="cocktails-shell"><div class="cocktails-form-wrap">';
            echo $result['errors'];
            $result['object']->getForm()->display();
            echo '</div></div>';
            require XOOPS_ROOT_PATH . '/footer.php';
            break;
        }

        $recipeObject = $result['object'];
        $recipeId     = (int)$recipeObject->getVar('id');

        // Optional image upload -> uploads/cocktails/recipe, then re-persist the filename.
        if (!empty($_FILES['image']['name']) && \defined('COCKTAILS_RECIPE_IMAGES_PATH')) {
            $uploadDir = \COCKTAILS_RECIPE_IMAGES_PATH;
            if (!\is_dir($uploadDir)) {
                Utility::prepareFolder($uploadDir);
            }
            $allowedMimes = (array)$helper->getConfig('mimetypes');
            $maxSize      = (int)$helper->getConfig('maxsize');
            $uploader     = new \XoopsMediaUploader($uploadDir, $allowedMimes, $maxSize);
            $uploader->setPrefix('cocktail_');
            if ($uploader->fetchMedia('image') && $uploader->upload()) {
                $recipeObject->setVar('image', $uploader->getSavedFileName());
                $recipeHandler->insert($recipeObject, true);
            }
        }

        $online   = (int)$recipeObject->getVar('online');
        $message  = $online ? _MD_COCKTAILS_SUBMIT_OK : _MD_COCKTAILS_SUBMIT_PENDING;
        $redirect = $online
            ? $cocktailsUrl . '/recipe.php?op=view&id=' . $recipeId
            : $cocktailsUrl . '/index.php';
        redirect_header($redirect, 2, $message);
        break;

    // -----------------------------------------------------------------
    case 'delete':
        $recipeId     = Request::getInt('id', 0, 'REQUEST');
        $recipeObject = $recipeHandler->get($recipeId);
        if (!cocktails_user_can_edit_recipe($recipeObject)) {
            redirect_header($cocktailsUrl . '/recipe.php', 3, _MD_COCKTAILS_NOPERM);
        }
        if (1 === Request::getInt('ok', 0, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($cocktailsUrl . '/recipe.php', 3, \implode(', ', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            $recipeHandler->delete($recipeObject, true);
            redirect_header($cocktailsUrl . '/index.php', 2, _MD_COCKTAILS_DELETE_OK);
        }
        require XOOPS_ROOT_PATH . '/header.php';
        xoops_confirm(
            ['op' => 'delete', 'id' => $recipeId, 'ok' => 1],
            $cocktailsUrl . '/recipe.php',
            \sprintf(_MD_COCKTAILS_DELETE_CONFIRM, $recipeObject->getVar('title'))
        );
        require XOOPS_ROOT_PATH . '/footer.php';
        break;

    // -----------------------------------------------------------------
    case 'view':
        $recipeId     = Request::getInt('id', 0, 'GET');
        $recipeObject = $recipeHandler->get($recipeId);
        if (!\is_object($recipeObject)) {
            redirect_header($cocktailsUrl . '/index.php', 3, _MD_COCKTAILS_NOT_FOUND);
            exit;
        }
        $isOwnerOrAdmin = cocktails_user_can_edit_recipe($recipeObject);
        if (0 === (int)$recipeObject->getVar('online') && !$isOwnerOrAdmin) {
            redirect_header($cocktailsUrl . '/index.php', 3, _MD_COCKTAILS_NOT_FOUND);
            exit;
        }

        $GLOBALS['xoopsOption']['template_main'] = 'cocktails_recipe.tpl';
        require XOOPS_ROOT_PATH . '/header.php';
        cocktails_register_theme_assets($stylesheet);
        cocktails_register_scripts();

        $recipeHandler->incrementViews($recipeId);

        $uid           = cocktails_current_uid();
        $categoryObj   = $categoryHandler->get((int)$recipeObject->getVar('cid'));
        $glassObj      = $glassHandler->get((int)$recipeObject->getVar('glass_id'));
        $difficulty    = $recipeObject->difficulty();
        $submitterUid  = (int)$recipeObject->getVar('uid');
        $submitterName = '';
        if ($submitterUid > 0) {
            /** @var \XoopsMemberHandler $memberHandler */
            $memberHandler = \xoops_getHandler('member');
            $submitterUser = $memberHandler->getUser($submitterUid);
            $submitterName = \is_object($submitterUser) ? (string)$submitterUser->getVar('uname') : '';
        }

        // Ingredient lines (join master ingredient names).
        $ingredientLines = [];
        foreach ($recipeIngredientHandler->getByRecipe($recipeId) as $line) {
            $ingObj  = $ingredientHandler->get((int)$line->getVar('ingredient_id'));
            $ingName = \is_object($ingObj) ? (string)$ingObj->getVar('name') : '';
            if ('' === $ingName) {
                continue;
            }
            $ingredientLines[] = [
                'text'        => $line->display($ingName),
                'ingredient'  => $ingName,
                'is_optional' => (int)$line->getVar('is_optional'),
                'ingredient_url' => \Xoops\Helpers\Service\Url::module('cocktails', 'ingredient.php', ['op' => 'view', 'id' => (int)$line->getVar('ingredient_id')]),
            ];
        }

        // Tags.
        $tags = [];
        foreach ($tagHandler->getForRecipe($recipeId) as $tag) {
            $tags[] = [
                'id'    => (int)$tag->getVar('id'),
                'name'  => (string)$tag->getVar('name'),
                'url'   => \Xoops\Helpers\Service\Url::module('cocktails', 'index.php', ['op' => 'browse', 'tag' => (int)$tag->getVar('id')]),
            ];
        }

        $recipe = [
            'id'               => $recipeId,
            'title'            => (string)$recipeObject->getVar('title'),
            'image_url'        => cocktails_recipe_image_url((string)$recipeObject->getVar('image')),
            'summary'          => (string)$recipeObject->getVar('summary'),
            'method'           => cocktails_render_rich_text((string)$recipeObject->getVar('method', 'n')),
            'garnish'          => (string)$recipeObject->getVar('garnish'),
            'category'         => \is_object($categoryObj) ? (string)$categoryObj->getVar('title') : '',
            'category_id'      => (int)$recipeObject->getVar('cid'),
            'category_url'     => \is_object($categoryObj) ? \Xoops\Helpers\Service\Url::module('cocktails', 'category.php', ['op' => 'view', 'id' => (int)$categoryObj->getVar('id')]) : '',
            'glass'            => \is_object($glassObj) ? (string)$glassObj->getVar('name') : '',
            'difficulty_label' => $difficulty->label(),
            'difficulty_class' => $difficulty->cssClass(),
            'prep_time'        => (int)$recipeObject->getVar('prep_time'),
            'servings'         => (int)$recipeObject->getVar('servings'),
            'is_alcoholic'     => (int)$recipeObject->getVar('is_alcoholic'),
            'rating_avg'       => \round((float)$recipeObject->getVar('rating_avg'), 1),
            'rating_count'     => (int)$recipeObject->getVar('rating_count'),
            'rating_html'      => $utility::renderStars((float)$recipeObject->getVar('rating_avg')),
            'favorites_count'  => (int)$recipeObject->getVar('favorites_count'),
            'views'            => (int)$recipeObject->getVar('views') + 1,
            'featured'         => (int)$recipeObject->getVar('featured'),
            'online'           => (int)$recipeObject->getVar('online'),
            'submitter'        => $submitterName,
            'created'          => formatTimestamp((int)$recipeObject->getVar('created'), 's'),
            'updated'          => (int)$recipeObject->getVar('updated') > 0 ? formatTimestamp((int)$recipeObject->getVar('updated'), 's') : '',
            'url'              => $recipeObject->url(),
            'ingredients'      => $ingredientLines,
            'tags'             => $tags,
            'user_stars'       => $ratingHandler->getUserStars($recipeId, $uid),
            'is_favorite'      => $uid > 0 && $favoriteHandler->isFavorite($recipeId, $uid),
            'can_edit'         => $isOwnerOrAdmin,
        ];

        $GLOBALS['xoopsTpl']->assign('recipe', $recipe);
        $GLOBALS['xoopsTpl']->assign('can_rate', cocktails_user_can_rate());
        $GLOBALS['xoopsTpl']->assign('is_logged_in', $uid > 0);
        $GLOBALS['xoopsTpl']->assign('security_token', $GLOBALS['xoopsSecurity']->getTokenHTML());
        $GLOBALS['xoopsTpl']->assign('rate_url', $cocktailsUrl . '/recipe.php');
        $GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $recipe['title']);

        $utility::metaKeywords($helper->getConfig('keywords') . ', ' . $recipe['title']);
        $utility::metaDescription($recipe['summary'] !== '' ? \strip_tags($recipe['summary']) : _MD_COCKTAILS_INDEX_INTRO);

        $GLOBALS['xoopsTpl']->assign('xoops_mpageurl', $cocktailsUrl . '/recipe.php');
        $GLOBALS['xoopsTpl']->assign('cocktails_url', $cocktailsUrl);
        $GLOBALS['xoopsTpl']->assign('bookmarks', $helper->getConfig('bookmarks'));
        $GLOBALS['xoopsTpl']->assign('fbcomments', 0);
        $GLOBALS['xoopsTpl']->assign('admin', COCKTAILS_ADMIN);
        $GLOBALS['xoopsTpl']->assign('copyright', $copyright);

        require XOOPS_ROOT_PATH . '/footer.php';
        break;

    // -----------------------------------------------------------------
    case 'list':
    default:
        redirect_header($cocktailsUrl . '/index.php?op=browse', 0, '');
        break;
}
