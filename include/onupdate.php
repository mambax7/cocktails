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

use XoopsModules\Mtools\Common\Configurator;
use XoopsModules\Mtools\Module\Installer;
use XoopsModules\Cocktails\Helper;
use XoopsModules\Cocktails\Utility;

/** @var Helper $helper */
/** @var Utility $utility */
if ((!defined('XOOPS_ROOT_PATH')) || !$GLOBALS['xoopsUser'] instanceof \XoopsUser
    || !$GLOBALS['xoopsUser']->isAdmin()) {
    exit('Restricted access' . PHP_EOL);
}

require \dirname(__DIR__) . '/bootstrap.php';

/**
 * @param \XoopsModule $module
 * @return bool
 */
function xoops_module_pre_update_cocktails(\XoopsModule $module)
{
    $mtoolsDependencyError = cocktails_mtools_dependency_error();
    if ('' !== $mtoolsDependencyError) {
        $module->setErrors($mtoolsDependencyError);

        return false;
    }

    $utility      = new Utility();
    $xoopsSuccess = $utility::checkVerXoops($module);
    $phpSuccess   = $utility::checkVerPhp($module);

    Installer::createUploadFolders(new Configurator(\dirname(__DIR__)));

    return $xoopsSuccess && $phpSuccess;
}

/**
 * @param \XoopsModule $module
 * @param null|int     $previousVersion
 * @return bool
 */
function xoops_module_update_cocktails(\XoopsModule $module, $previousVersion = null)
{
    $mtoolsDependencyError = cocktails_mtools_dependency_error();
    if ('' !== $mtoolsDependencyError) {
        $module->setErrors($mtoolsDependencyError);

        return false;
    }

    $helper = Helper::getInstance();
    $helper->loadLanguage('common');
    $configurator = new Configurator(\dirname(__DIR__));

    Installer::removeOldAssets($module, $configurator);
    Installer::createUploadFolders($configurator);
    Installer::copyBlankFiles($configurator);
    Installer::purgeHtmlTemplates($module);

    return true;
}
