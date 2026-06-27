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
require \dirname(__DIR__) . '/bootstrap.php';

/**
 * @param \XoopsModule $module
 * @return bool
 */
function xoops_module_pre_install_cocktails(\XoopsModule $module)
{
    $mtoolsDependencyError = cocktails_mtools_dependency_error();
    if ('' !== $mtoolsDependencyError) {
        $module->setErrors($mtoolsDependencyError);

        return false;
    }

    $utility = new Utility();
    if (!$utility::checkVerXoops($module) || !$utility::checkVerPhp($module)) {
        return false;
    }

    Installer::prepare($module, new Configurator(\dirname(__DIR__)));

    return true;
}

/**
 * @param \XoopsModule $module
 * @return bool
 */
function xoops_module_install_cocktails(\XoopsModule $module)
{
    $mtoolsDependencyError = cocktails_mtools_dependency_error();
    if ('' !== $mtoolsDependencyError) {
        $module->setErrors($mtoolsDependencyError);

        return false;
    }

    $moduleDirName = \basename(\dirname(__DIR__));
    $helper        = Helper::getInstance();
    $helper->loadLanguage('admin');
    $helper->loadLanguage('modinfo');

    // Default permissions.
    $moduleId = $module->getVar('mid');
    /** @var \XoopsGroupPermHandler $grouppermHandler */
    $grouppermHandler = xoops_getHandler('groupperm');
    $grouppermHandler->addRight($moduleDirName . '_view', 1, \XOOPS_GROUP_ADMIN, $moduleId);
    $grouppermHandler->addRight($moduleDirName . '_view', 1, \XOOPS_GROUP_USERS, $moduleId);
    $grouppermHandler->addRight($moduleDirName . '_view', 1, \XOOPS_GROUP_ANONYMOUS, $moduleId);

    // Shared install filesystem work: upload folders + blank.png + test data + .html tpl purge.
    Installer::install($module, new Configurator(\dirname(__DIR__)));

    return true;
}
