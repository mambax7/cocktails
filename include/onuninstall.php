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

use XoopsModules\Cocktails\Helper;

/**
 * @param \XoopsModule $module
 * @return bool
 */
function xoops_module_pre_uninstall_cocktails(\XoopsModule $module)
{
    return true;
}

/**
 * @param \XoopsModule $module
 * @return bool
 */
function xoops_module_uninstall_cocktails(\XoopsModule $module)
{
    require \dirname(__DIR__) . '/bootstrap.php';

    $helper = Helper::getInstance();
    $helper->loadLanguage('admin');
    $helper->loadLanguage('common');

    return true;
}
