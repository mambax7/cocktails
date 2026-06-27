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
use XoopsModules\Mtools\Common\TestdataSample;
use XoopsModules\Cocktails\Helper;

/** @var Helper $helper */
require \dirname(__DIR__, 3) . '/include/cp_header.php';
require \dirname(__DIR__) . '/bootstrap.php';

$op = Request::getCmd('op', '', 'REQUEST');

$helper             = Helper::getInstance();
$moduleDirNameUpper = \mb_strtoupper($helper->getDirname());
$helper->loadLanguage('common');

$testdataSample = new TestdataSample($helper);

switch ($op) {
    case 'load':
        if (Request::hasVar('ok', 'REQUEST') && 1 === Request::getInt('ok', 0, 'REQUEST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($helper->url('admin/index.php'), 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            $testdataSample->loadData();
            redirect_header($helper->url('admin/index.php'), 2, 'Sample data loaded.');
        } else {
            xoops_cp_header();
            xoops_confirm(['ok' => 1, 'op' => 'load'], 'index.php', constant('_CO_COCKTAILS_LOAD_SAMPLEDATA_CONFIRM'), constant('_CO_COCKTAILS_CONFIRM'));
            xoops_cp_footer();
        }
        break;
    case 'save':
        $testdataSample->saveData();
        redirect_header($helper->url('admin/index.php'), 2, 'OK');
        break;
    case 'clear':
        if (Request::hasVar('ok', 'REQUEST') && 1 === Request::getInt('ok', 0, 'REQUEST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($helper->url('admin/index.php'), 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            $testdataSample->clearData();
            redirect_header($helper->url('admin/index.php'), 2, 'OK');
        } else {
            xoops_cp_header();
            xoops_confirm(['ok' => 1, 'op' => 'clear'], 'index.php', constant('_CO_COCKTAILS_CLEAR_SAMPLEDATA_CONFIRM'), constant('_CO_COCKTAILS_CONFIRM'));
            xoops_cp_footer();
        }
        break;
}
