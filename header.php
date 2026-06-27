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

use XoopsModules\Cocktails\Constants;
use XoopsModules\Cocktails\Helper;
use XoopsModules\Cocktails\Utility;
use XoopsModules\Mtools;

/** @var Utility $utility */
/** @var Helper $helper */
require_once \dirname(__DIR__, 2) . '/mainfile.php';

require __DIR__ . '/bootstrap.php';

$mtoolsDependencyError = cocktails_mtools_dependency_error();
if ('' !== $mtoolsDependencyError) {
    redirect_header(XOOPS_URL, 3, $mtoolsDependencyError);
    exit;
}

require __DIR__ . '/include/common.php';
$moduleDirName = basename(__DIR__);

cocktails_ensure_core_config_defaults();

$helper       = Helper::getInstance();
$utility      = new Utility();
$configurator = Mtools\Common\Configurator::forModule($helper);
$copyright    = $configurator->modCopyright;

$modulePath = XOOPS_ROOT_PATH . '/modules/' . $moduleDirName;
$db         = \XoopsDatabaseFactory::getDatabaseConnection();

$myts = \MyTextSanitizer::getInstance();

$GLOBALS['xoopsTpl']->assign('commentsnav', '');
$GLOBALS['xoopsTpl']->assign('lang_notice', '');
$GLOBALS['xoopsTpl']->assign('comment_mode', '');

$stylesheet = "modules/{$moduleDirName}/assets/css/style.css";

/** @var \XoopsModules\Cocktails\RecipeHandler $recipeHandler */
$recipeHandler = $helper->getHandler('Recipe');
/** @var \XoopsModules\Cocktails\RecipeIngredientHandler $recipeIngredientHandler */
$recipeIngredientHandler = $helper->getHandler('RecipeIngredient');
/** @var \XoopsModules\Cocktails\IngredientHandler $ingredientHandler */
$ingredientHandler = $helper->getHandler('Ingredient');
/** @var \XoopsModules\Cocktails\CategoryHandler $categoryHandler */
$categoryHandler = $helper->getHandler('Category');
/** @var \XoopsModules\Cocktails\GlassHandler $glassHandler */
$glassHandler = $helper->getHandler('Glass');
/** @var \XoopsModules\Cocktails\TagHandler $tagHandler */
$tagHandler = $helper->getHandler('Tag');
/** @var \XoopsModules\Cocktails\RatingHandler $ratingHandler */
$ratingHandler = $helper->getHandler('Rating');
/** @var \XoopsModules\Cocktails\FavoriteHandler $favoriteHandler */
$favoriteHandler = $helper->getHandler('Favorite');

// Load language files
$helper->loadLanguage('main');
$helper->loadLanguage('common');
$helper->loadLanguage('blocks');
$helper->loadLanguage('admin');
$helper->loadLanguage('modinfo');

/**
 * Current front-end user id, or 0 for anonymous.
 */
function cocktails_current_uid(): int
{
    return ($GLOBALS['xoopsUser'] instanceof \XoopsUser) ? (int)$GLOBALS['xoopsUser']->getVar('uid') : 0;
}

/**
 * Whether the current user is a module admin (admins edit everything).
 */
function cocktails_is_admin(): bool
{
    return Helper::getInstance()->isUserAdmin();
}

/**
 * Whether the current user may add/edit cocktail recipes from the frontend.
 *
 * Uses XMF's Permission helper (admin-aware) against the global "Submit from user side"
 * right (`cocktails_ac` item 8).
 */
function cocktails_user_can_submit(): bool
{
    static $permission;
    if (null === $permission) {
        $permission = new \Xmf\Module\Helper\Permission();
    }

    return cocktails_is_admin() || $permission->checkPermission(Constants::PERM_GLOBAL, Constants::AC_SUBMIT);
}

/**
 * Whether the current user may rate recipes (`cocktails_ac` item 64).
 * Guests may rate only when the module config `allowGuestRating` is on; logged-in users
 * pass when they hold the right (admins always pass).
 */
function cocktails_user_can_rate(): bool
{
    static $permission;
    if (null === $permission) {
        $permission = new \Xmf\Module\Helper\Permission();
    }

    if (cocktails_is_admin()) {
        return true;
    }

    if (cocktails_current_uid() <= 0) {
        return (bool)Helper::getInstance()->getConfig('allowGuestRating');
    }

    return $permission->checkPermission(Constants::PERM_GLOBAL, Constants::AC_RATE);
}

/**
 * Whether the current user holds the "edit own recipes" right (`cocktails_ac` item 32).
 */
function cocktails_user_can_edit_own(): bool
{
    static $permission;
    if (null === $permission) {
        $permission = new \Xmf\Module\Helper\Permission();
    }

    return $permission->checkPermission(Constants::PERM_GLOBAL, Constants::AC_EDIT_OWN);
}

/**
 * May the current user edit this recipe? Admin OR (owner AND has the edit-own right).
 */
function cocktails_user_can_edit_recipe($recipeObject): bool
{
    if (!\is_object($recipeObject)) {
        return false;
    }
    if (cocktails_is_admin()) {
        return true;
    }
    $uid = cocktails_current_uid();

    return $uid > 0
        && $uid === (int)$recipeObject->getVar('uid')
        && cocktails_user_can_edit_own();
}

function cocktails_ensure_core_config_defaults(): void
{
    global $xoopsConfig;

    if (!\is_array($xoopsConfig)) {
        $xoopsConfig = [];
    }

    $defaultTheme = \is_dir(\XOOPS_ROOT_PATH . '/themes/xbootstrap5') ? 'xbootstrap5' : 'default';
    $xoopsConfig += [
        'language'          => 'english',
        'debug_mode'        => 0,
        'theme_set'         => $defaultTheme,
        'theme_set_allowed' => [$defaultTheme],
        'template_set'      => 'default',
        'module_cache'      => [],
        'sitename'          => 'XOOPS',
        'slogan'            => '',
        'banners'           => 0,
        'startpage'         => '--',
        'default_TZ'        => '0.0',
        'server_TZ'         => '0.0',
        'use_ssl'           => 0,
        'usercookie'        => '',
        'anonymous'         => 'Anonymous',
        'adminmail'         => '',
        'from'              => '',
        'mailmethod'        => 'mail',
        'sendmailpath'      => '/usr/sbin/sendmail',
        'smtphost'          => [],
    ];

    if (!\is_array($xoopsConfig['theme_set_allowed'])) {
        $xoopsConfig['theme_set_allowed'] = [$xoopsConfig['theme_set']];
    }
    if (!\is_array($xoopsConfig['module_cache'])) {
        $xoopsConfig['module_cache'] = [];
    }
    if (!\is_array($xoopsConfig['smtphost'])) {
        $xoopsConfig['smtphost'] = [];
    }

    $GLOBALS['xoopsConfig'] = $xoopsConfig;
}

function cocktails_register_theme_assets(string $stylesheet): void
{
    global $xoTheme;

    if (\is_object($xoTheme) && \file_exists($GLOBALS['xoops']->path($stylesheet))) {
        $stylesheetPath = $GLOBALS['xoops']->path($stylesheet);
        $stylesheetUrl  = $GLOBALS['xoops']->url("www/{$stylesheet}") . '?v=' . (string)\filemtime($stylesheetPath);
        $xoTheme->addStylesheet($stylesheetUrl);
    } elseif (isset($GLOBALS['xoopsTpl']) && $GLOBALS['xoopsTpl'] instanceof \XoopsTpl) {
        $href = $GLOBALS['xoops']->url("www/{$stylesheet}");
        $GLOBALS['xoopsTpl']->assign('xoops_module_header', '<link rel="stylesheet" type="text/css" href="' . $href . '">');
    }

    if (isset($GLOBALS['xoopsTpl']) && $GLOBALS['xoopsTpl'] instanceof \XoopsTpl) {
        $GLOBALS['xoopsTpl']->assign('xoops_meta_robots', $GLOBALS['xoopsTpl']->getTemplateVars('xoops_meta_robots') ?: 'index,follow');
        $GLOBALS['xoopsTpl']->assign('xoops_meta_rating', $GLOBALS['xoopsTpl']->getTemplateVars('xoops_meta_rating') ?: 'general');
        $GLOBALS['xoopsTpl']->assign('xoops_meta_author', $GLOBALS['xoopsTpl']->getTemplateVars('xoops_meta_author') ?: 'XOOPS');
        $GLOBALS['xoopsTpl']->assign('xoops_meta_copyright', $GLOBALS['xoopsTpl']->getTemplateVars('xoops_meta_copyright') ?: '');
        $GLOBALS['xoopsTpl']->assign('xoops_meta_description', $GLOBALS['xoopsTpl']->getTemplateVars('xoops_meta_description') ?: '');
        $GLOBALS['xoopsTpl']->assign('xoops_meta_keywords', $GLOBALS['xoopsTpl']->getTemplateVars('xoops_meta_keywords') ?: '');
    }
}

/**
 * Register the bundled cocktails.js once per request, after the body, so the rating /
 * favorite widgets and the ingredient editor are wired up on the public pages too.
 */
function cocktails_register_scripts(): void
{
    global $xoTheme;
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $moduleDirName = \basename(__DIR__);
    $relative      = "modules/{$moduleDirName}/assets/js/cocktails.js";
    $path          = $GLOBALS['xoops']->path($relative);
    $url           = $GLOBALS['xoops']->url("www/{$relative}");
    if (\is_file($path)) {
        $url .= '?v=' . (string)\filemtime($path);
    }
    if (\is_object($xoTheme)) {
        $xoTheme->addScript($url);
    }
}

/**
 * Public URL to a recipe image (empty/blank => no image).
 */
function cocktails_recipe_image_url(string $image): string
{
    if ('' === $image || 'blank.png' === \strtolower($image)) {
        return '';
    }
    if (\defined('COCKTAILS_RECIPE_IMAGES_URL')) {
        return \COCKTAILS_RECIPE_IMAGES_URL . '/' . \rawurlencode($image);
    }

    return \Xoops\Helpers\Service\Url::moduleUpload('cocktails', 'recipe/' . \rawurlencode($image));
}

/**
 * Build a display-ready row for one Recipe object, used by index/browse/category/favorite
 * card grids. Keeps all templates fed from a single shaping function.
 *
 * @return array<string,mixed>
 */
function cocktails_recipe_card(
    \XoopsObject $recipeObject,
    \XoopsModules\Cocktails\Utility $utility,
    \XoopsModules\Cocktails\Helper $helper,
    \XoopsPersistableObjectHandler $categoryHandler,
    \XoopsPersistableObjectHandler $favoriteHandler
): array {
    $id            = (int)$recipeObject->getVar('id');
    $categoryObj   = $categoryHandler->get((int)$recipeObject->getVar('cid'));
    $difficulty    = \XoopsModules\Cocktails\Domain\Difficulty::fromLevel((int)$recipeObject->getVar('difficulty'));
    $ratingAvg     = (float)$recipeObject->getVar('rating_avg');
    $truncate      = (int)$helper->getConfig('truncatelength');
    $uid           = cocktails_current_uid();

    return [
        'id'               => $id,
        'title'            => (string)$recipeObject->getVar('title'),
        'url'              => $recipeObject->url(),
        'image_url'        => cocktails_recipe_image_url((string)$recipeObject->getVar('image')),
        'summary'          => $utility::truncateHtml(\strip_tags((string)$recipeObject->getVar('summary', 'n')), $truncate > 0 ? $truncate : 120),
        'rating_avg'       => \round($ratingAvg, 1),
        'rating_count'     => (int)$recipeObject->getVar('rating_count'),
        'rating_html'      => $utility::renderStars($ratingAvg),
        'difficulty_label' => $difficulty->label(),
        'difficulty_class' => $difficulty->cssClass(),
        'prep_time'        => (int)$recipeObject->getVar('prep_time'),
        'servings'         => (int)$recipeObject->getVar('servings'),
        'is_alcoholic'     => (int)$recipeObject->getVar('is_alcoholic'),
        'featured'         => (int)$recipeObject->getVar('featured'),
        'views'            => (int)$recipeObject->getVar('views'),
        'favorites_count'  => (int)$recipeObject->getVar('favorites_count'),
        'category'         => \is_object($categoryObj) ? (string)$categoryObj->getVar('title') : '',
        'category_id'      => (int)$recipeObject->getVar('cid'),
        'is_favorite'      => $uid > 0 && $favoriteHandler->isFavorite($id, $uid),
        'can_edit'         => cocktails_user_can_edit_recipe($recipeObject),
        'online'           => (int)$recipeObject->getVar('online'),
    ];
}
