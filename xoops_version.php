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
$moduleDirName      = basename(__DIR__);
$moduleDirNameUpper = \mb_strtoupper($moduleDirName);

$modversion = [
    'version'             => '1.0.0',
    'module_status'       => 'Beta 1',
    'release_date'        => '2026/06/27',
    'name'                => _MI_COCKTAILS_NAME,
    'description'         => _MI_COCKTAILS_DESC,
    'author'              => 'XOOPS Development Team',
    'author_mail'         => 'name@site.com',
    'author_website_url'  => 'https://xoops.org',
    'author_website_name' => 'XOOPS Project',
    'credits'             => 'XOOPS Development Team. Domain model inspired by "Modern Application Development with PHP".',
    'license'             => 'GPL 2.0 or later',
    'license_url'         => 'www.gnu.org/licenses/gpl-2.0.html',

    'release_info' => 'release_info',
    'release_file' => XOOPS_URL . "/modules/{$moduleDirName}/docs/release_info file",

    'manual'              => 'Installation.txt',
    'manual_file'         => XOOPS_URL . "/modules/{$moduleDirName}/docs/install.txt",
    'min_php'             => '8.2',
    'min_xoops'           => '2.7.0',
    'min_admin'           => '1.2',
    'min_db'              => ['mysql' => '5.7'],
    'min_modules'         => ['mtools' => '1.2.0'],
    'image'               => 'assets/images/logoModule.png',
    'dirname'             => $moduleDirName,
    'modicons16'          => 'assets/images/icons/16',
    'modicons32'          => 'assets/images/icons/32',
    //About
    'demo_site_url'       => 'https://xoops.org',
    'demo_site_name'      => 'XOOPS Demo Site',
    'support_url'         => 'https://xoops.org/modules/newbb',
    'support_name'        => 'Support Forum',
    'module_website_url'  => 'www.xoops.org',
    'module_website_name' => 'XOOPS Project',
    // Admin system menu
    'system_menu'         => 1,
    // Admin things
    'hasAdmin'            => 1,
    'adminindex'          => 'admin/index.php',
    'adminmenu'           => 'admin/menu.php',
    // Menu
    'hasMain'             => 1,
    // Scripts to run upon installation or update
    'onInstall'           => 'include/oninstall.php',
    'onUpdate'            => 'include/onupdate.php',
    'onUninstall'         => 'include/onuninstall.php',
    // ------------------- Mysql -----------------------------
    'sqlfile'             => ['mysql' => 'sql/mysql.sql'],
    // ------------------- Tables ----------------------------
    'tables'              => [
        $moduleDirName . '_recipe',
        $moduleDirName . '_ingredient',
        $moduleDirName . '_recipe_ingredient',
        $moduleDirName . '_rating',
        $moduleDirName . '_favorite',
        $moduleDirName . '_category',
        $moduleDirName . '_glass',
        $moduleDirName . '_tag',
        $moduleDirName . '_recipe_tag',
    ],
];

// ------------------- Front sub-menu -------------------
$modversion['sub'][] = ['name' => _MI_COCKTAILS_SMNAME_BROWSE, 'url' => 'index.php?op=browse'];
$modversion['sub'][] = ['name' => _MI_COCKTAILS_SMNAME_ADD_RECIPE, 'url' => 'recipe.php?op=edit'];
$modversion['sub'][] = ['name' => _MI_COCKTAILS_SMNAME_INGREDIENTS, 'url' => 'ingredient.php'];
$modversion['sub'][] = ['name' => _MI_COCKTAILS_SMNAME_FAVORITES, 'url' => 'favorites.php'];

// ------------------- Search -----------------------------//
$modversion['hasSearch']      = 1;
$modversion['search']['file'] = 'include/search.inc.php';
$modversion['search']['func'] = 'cocktails_search';

//  ------------------- Templates -----------------------------//
$modversion['templates'] = [
    ['file' => 'cocktails_header.tpl', 'description' => ''],
    ['file' => 'cocktails_footer.tpl', 'description' => ''],
    ['file' => 'cocktails_card.tpl', 'description' => ''],
    ['file' => 'cocktails_index.tpl', 'description' => ''],
    ['file' => 'cocktails_recipe_list.tpl', 'description' => ''],
    ['file' => 'cocktails_recipe.tpl', 'description' => ''],
    ['file' => 'cocktails_category.tpl', 'description' => ''],
    ['file' => 'cocktails_ingredient.tpl', 'description' => ''],
    ['file' => 'cocktails_favorites.tpl', 'description' => ''],

    ['file' => 'admin/cocktails_admin_about.tpl', 'description' => ''],
    ['file' => 'admin/cocktails_admin_help.tpl', 'description' => ''],
    ['file' => 'admin/cocktails_admin_recipe.tpl', 'description' => ''],
    ['file' => 'admin/cocktails_admin_ingredient.tpl', 'description' => ''],
    ['file' => 'admin/cocktails_admin_category.tpl', 'description' => ''],
    ['file' => 'admin/cocktails_admin_glass.tpl', 'description' => ''],
    ['file' => 'admin/cocktails_admin_tag.tpl', 'description' => ''],
];

// ------------------- Blocks -----------------------------//
$modversion['blocks'][] = [
    'file'        => 'recipe.php',
    'name'        => _MI_COCKTAILS_TOPRATED_BLOCK,
    'description' => 'Top rated cocktails',
    'show_func'   => 'showCocktailsTopRated',
    'edit_func'   => 'editCocktailsCount',
    'options'     => '5',
    'template'    => 'cocktails_toprated_block.tpl',
];
$modversion['blocks'][] = [
    'file'        => 'recipe.php',
    'name'        => _MI_COCKTAILS_NEWEST_BLOCK,
    'description' => 'Newest cocktails',
    'show_func'   => 'showCocktailsNewest',
    'edit_func'   => 'editCocktailsCount',
    'options'     => '5',
    'template'    => 'cocktails_newest_block.tpl',
];
$modversion['blocks'][] = [
    'file'        => 'recipe.php',
    'name'        => _MI_COCKTAILS_RANDOM_BLOCK,
    'description' => 'Random cocktail',
    'show_func'   => 'showCocktailsRandom',
    'edit_func'   => 'editCocktailsCount',
    'options'     => '1',
    'template'    => 'cocktails_random_block.tpl',
];

// ------------------- Help files ------------------- //
$modversion['help']        = 'page=help';
$modversion['helpsection'] = [
    ['name' => _MI_COCKTAILS_OVERVIEW, 'link' => 'page=help'],
    ['name' => _MI_COCKTAILS_DISCLAIMER, 'link' => 'page=disclaimer'],
    ['name' => _MI_COCKTAILS_LICENSE, 'link' => 'page=license'],
    ['name' => _MI_COCKTAILS_SUPPORT, 'link' => 'page=support'],
];

// ------------------- Config Options -----------------------------//
xoops_load('xoopseditorhandler');
$editorHandler = \XoopsEditorHandler::getInstance();

$modversion['config'][] = [
    'name'        => 'cocktailsEditorAdmin',
    'title'       => '_MI_COCKTAILS_EDITOR_ADMIN',
    'description' => '_MI_COCKTAILS_EDITOR_DESC_ADMIN',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'options'     => array_flip($editorHandler->getList()),
    'default'     => 'tinymce',
];
$modversion['config'][] = [
    'name'        => 'cocktailsEditorUser',
    'title'       => '_MI_COCKTAILS_EDITOR_USER',
    'description' => '_MI_COCKTAILS_EDITOR_DESC_USER',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'options'     => array_flip($editorHandler->getList()),
    'default'     => 'dhtmltextarea',
];

// -------------- Groups --------------
/** @var \XoopsMemberHandler $memberHandler */
$memberHandler = xoops_getHandler('member');
$xoopsGroups   = $memberHandler->getGroupList();
$groups        = array_flip($xoopsGroups);

$modversion['config'][] = [
    'name'        => 'groups',
    'title'       => '_MI_COCKTAILS_GROUPS',
    'description' => '_MI_COCKTAILS_GROUPS_DESC',
    'formtype'    => 'select_multi',
    'valuetype'   => 'array',
    'options'     => $groups,
    'default'     => $groups,
];

$criteria = new \CriteriaCompo();
$criteria->add(new \Criteria('group_type', 'Admin'));
$adminXoopsGroups = $memberHandler->getGroupList($criteria);
$admin_groups     = array_flip($adminXoopsGroups);

$modversion['config'][] = [
    'name'        => 'admin_groups',
    'title'       => '_MI_COCKTAILS_ADMINGROUPS',
    'description' => '_MI_COCKTAILS_ADMINGROUPS_DESC',
    'formtype'    => 'select_multi',
    'valuetype'   => 'array',
    'options'     => $admin_groups,
    'default'     => $admin_groups,
];

$modversion['config'][] = [
    'name'        => 'keywords',
    'title'       => '_MI_COCKTAILS_KEYWORDS',
    'description' => '_MI_COCKTAILS_KEYWORDS_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'cocktails, recipe, drinks, mixology, bartender',
];

$modversion['config'][] = [
    'name'        => 'measurementSystem',
    'title'       => '_MI_COCKTAILS_MEASURESYS',
    'description' => '_MI_COCKTAILS_MEASURESYS_DESC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'options'     => ['_MI_COCKTAILS_METRIC' => 'metric', '_MI_COCKTAILS_IMPERIAL' => 'imperial'],
    'default'     => 'metric',
];

$modversion['config'][] = [
    'name'        => 'moderateSubmissions',
    'title'       => '_MI_COCKTAILS_MODERATE',
    'description' => '_MI_COCKTAILS_MODERATE_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'allowGuestRating',
    'title'       => '_MI_COCKTAILS_GUESTRATE',
    'description' => '_MI_COCKTAILS_GUESTRATE_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'maxsize',
    'title'       => '_MI_COCKTAILS_MAXSIZE',
    'description' => '_MI_COCKTAILS_MAXSIZE_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 5000000,
];

$modversion['config'][] = [
    'name'        => 'mimetypes',
    'title'       => '_MI_COCKTAILS_MIMETYPES',
    'description' => '_MI_COCKTAILS_MIMETYPES_DESC',
    'formtype'    => 'select_multi',
    'valuetype'   => 'array',
    'default'     => ['image/gif', 'image/jpeg', 'image/jpg', 'image/png', 'image/webp'],
    'options'     => [
        'gif'   => 'image/gif',
        'jpeg'  => 'image/jpeg',
        'jpg'   => 'image/jpg',
        'png'   => 'image/png',
        'webp'  => 'image/webp',
    ],
];

$modversion['config'][] = [
    'name'        => 'thumbWidth',
    'title'       => '_MI_COCKTAILS_THUMBW',
    'description' => '_MI_COCKTAILS_THUMBW_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 600,
];

$modversion['config'][] = [
    'name'        => 'thumbHeight',
    'title'       => '_MI_COCKTAILS_THUMBH',
    'description' => '_MI_COCKTAILS_THUMBH_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 400,
];

$modversion['config'][] = [
    'name'        => 'adminpager',
    'title'       => '_MI_COCKTAILS_ADMINPAGER',
    'description' => '_MI_COCKTAILS_ADMINPAGER_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 15,
];

$modversion['config'][] = [
    'name'        => 'userpager',
    'title'       => '_MI_COCKTAILS_USERPAGER',
    'description' => '_MI_COCKTAILS_USERPAGER_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 12,
];

$modversion['config'][] = [
    'name'        => 'bookmarks',
    'title'       => '_MI_COCKTAILS_BOOKMARKS',
    'description' => '_MI_COCKTAILS_BOOKMARKS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'truncatelength',
    'title'       => '_CO_COCKTAILS_TRUNCATE_LENGTH',
    'description' => '_CO_COCKTAILS_TRUNCATE_LENGTH_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 120,
];

$modversion['config'][] = [
    'name'        => 'displaySampleButton',
    'title'       => '_CO_COCKTAILS_SHOW_SAMPLE_BUTTON',
    'description' => '_CO_COCKTAILS_SHOW_SAMPLE_BUTTON_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'displayDeveloperTools',
    'title'       => '_CO_COCKTAILS_SHOW_DEV_TOOLS',
    'description' => '_CO_COCKTAILS_SHOW_DEV_TOOLS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];
