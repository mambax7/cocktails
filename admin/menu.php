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
use XoopsModules\Cocktails\Helper;

/** @var Helper $helper */
require \dirname(__DIR__) . '/bootstrap.php';

$moduleDirName      = \basename(\dirname(__DIR__));
$moduleDirNameUpper = \mb_strtoupper($moduleDirName);

$helper = Helper::getInstance();
$helper->loadLanguage('admin');
$helper->loadLanguage('common');
$helper->loadLanguage('feedback');

// get path to icons
$pathIcon32    = Admin::menuIconPath('');
$pathModIcon32 = \Xoops\Helpers\Service\Url::module($moduleDirName, 'assets/images/icons/32/');
if (is_object($helper->getModule()) && false !== $helper->getModule()->getInfo('modicons32')) {
    $pathModIcon32 = $helper->url($helper->getModule()->getInfo('modicons32'));
}

$adminObject = Admin::getInstance();

$adminmenu[] = [
    'title' => _AM_COCKTAILS_DASHBOARD,
    'link'  => 'admin/index.php',
    'icon'  => "{$pathIcon32}/home.png",
];

$adminmenu[] = [
    'title' => _AM_COCKTAILS_RECIPES,
    'link'  => 'admin/recipe.php',
    'icon'  => "{$pathIcon32}/insert_table_row.png",
];

$adminmenu[] = [
    'title' => _AM_COCKTAILS_INGREDIENTS,
    'link'  => 'admin/ingredient.php',
    'icon'  => "{$pathIcon32}/insert_table_row.png",
];

$adminmenu[] = [
    'title' => _AM_COCKTAILS_CATEGORIES,
    'link'  => 'admin/category.php',
    'icon'  => "{$pathIcon32}/category.png",
];

$adminmenu[] = [
    'title' => _AM_COCKTAILS_GLASSES,
    'link'  => 'admin/glass.php',
    'icon'  => "{$pathIcon32}/category.png",
];

$adminmenu[] = [
    'title' => _AM_COCKTAILS_TAGS,
    'link'  => 'admin/tag.php',
    'icon'  => "{$pathIcon32}/category.png",
];

$adminmenu[] = [
    'title' => _AM_COCKTAILS_PERMISSIONS,
    'link'  => 'admin/permissions.php',
    'icon'  => "{$pathIcon32}/permissions.png",
];

$adminmenu[] = [
    'title' => constant('_CO_COCKTAILS_BLOCKS'),
    'link'  => 'admin/blocksadmin.php',
    'icon'  => "{$pathIcon32}/block.png",
];

$adminmenu[] = [
    'title' => _AM_COCKTAILS_FEEDBACK,
    'link'  => 'admin/feedback.php',
    'icon'  => "{$pathIcon32}/mail_foward.png",
];

if (is_object($helper->getModule()) && $helper->getConfig('displayDeveloperTools')) {
    $adminmenu[] = [
        'title' => _AM_COCKTAILS_MIGRATE,
        'link'  => 'admin/migrate.php',
        'icon'  => "{$pathIcon32}/database_go.png",
    ];
}

$adminmenu[] = [
    'title' => _AM_COCKTAILS_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => "{$pathIcon32}/about.png",
];
