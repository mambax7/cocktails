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

use Xmf\Module\Admin;
use XoopsModules\Mtools\{
    Helper as mtoolsHelper
};
use XoopsModules\Cocktails\{
    Helper,
    Utility
};

/** @var Admin $adminObject */
/** @var Helper $helper */
/** @var Utility $utility */
require \dirname(__DIR__, 3) . '/include/cp_header.php';
require_once \dirname(__DIR__, 3) . '/class/xoopsformloader.php';

require \dirname(__DIR__) . '/bootstrap.php';

$mtoolsDependencyError = cocktails_mtools_dependency_error();
if ('' !== $mtoolsDependencyError) {
    redirect_header(XOOPS_URL . '/admin.php', 3, $mtoolsDependencyError);
    exit;
}

require \dirname(__DIR__) . '/include/common.php';

$helper = Helper::getInstance();

$moduleDirName = \basename(\dirname(__DIR__));

$utility     = new Utility();
$adminObject = Admin::getInstance();

$db = \XoopsDatabaseFactory::getDatabaseConnection();

$pathIcon16    = Admin::iconUrl('', '16');
$pathIcon32    = Admin::iconUrl('', '32');
$pathModIcon32 = $helper->getConfig('modicons32');

/** @var \XoopsPersistableObjectHandler $recipeHandler */
$recipeHandler = $helper->getHandler('Recipe');
/** @var \XoopsPersistableObjectHandler $ingredientHandler */
$ingredientHandler = $helper->getHandler('Ingredient');
/** @var \XoopsPersistableObjectHandler $recipeIngredientHandler */
$recipeIngredientHandler = $helper->getHandler('RecipeIngredient');
/** @var \XoopsPersistableObjectHandler $ratingHandler */
$ratingHandler = $helper->getHandler('Rating');
/** @var \XoopsPersistableObjectHandler $favoriteHandler */
$favoriteHandler = $helper->getHandler('Favorite');
/** @var \XoopsPersistableObjectHandler $categoryHandler */
$categoryHandler = $helper->getHandler('Category');
/** @var \XoopsPersistableObjectHandler $glassHandler */
$glassHandler = $helper->getHandler('Glass');
/** @var \XoopsPersistableObjectHandler $tagHandler */
$tagHandler = $helper->getHandler('Tag');

$myts = \MyTextSanitizer::getInstance();
if (!isset($xoopsTpl) || !is_object($xoopsTpl)) {
    require XOOPS_ROOT_PATH . '/class/template.php';
    $xoopsTpl = new \XoopsTpl();
}

// Load language files
$helper->loadLanguage('admin');
$helper->loadLanguage('modinfo');
$helper->loadLanguage('common');

//xoops_cp_header();

/**
 * Resolve a submitter username from a XOOPS uid (memoised). Returns '-' for anonymous/unknown.
 */
function cocktails_admin_uname(int $uid): string
{
    static $cache = [];
    if ($uid <= 0) {
        return '-';
    }
    if (!\array_key_exists($uid, $cache)) {
        /** @var \XoopsMemberHandler $memberHandler */
        $memberHandler = \xoops_getHandler('member');
        $user          = $memberHandler->getUser($uid);
        $cache[$uid]   = \is_object($user) ? $user->getVar('uname') : '-';
    }

    return $cache[$uid];
}
