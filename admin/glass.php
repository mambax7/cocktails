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
use Xmf\Module\Helper\Permission;
use Xmf\Request;
use XoopsModules\Cocktails\Helper;
use XoopsModules\Cocktails\Utility;

/** @var Admin $adminObject */
/** @var Helper $helper */
/** @var Utility $utility */
require_once __DIR__ . '/admin_header.php';
xoops_cp_header();

$op    = Request::getString('op', 'list', 'REQUEST');
$order = \strtolower(Request::getString('order', 'asc', 'GET'));
$order = \in_array($order, ['asc', 'desc'], true) ? $order : 'asc';
$sort  = Request::getString('sort', 'weight', 'GET');
$sort  = \in_array($sort, ['id', 'name', 'weight'], true) ? $sort : 'weight';

$adminObject->displayNavigation(basename(__FILE__));
$permHelper = new Permission();

switch ($op) {
    case 'new':
        $adminObject->addItemButton(_AM_COCKTAILS_GLASS_LIST, 'glass.php', 'list');
        $adminObject->displayButton('left');
        $form = $glassHandler->create()->getForm();
        $form->display();
        break;
    case 'save':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('glass.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $result = $glassHandler->saveFromRequest($helper);
        if ($result['ok']) {
            redirect_header('glass.php?op=list', 2, _AM_COCKTAILS_FORMOK);
        }
        echo $result['errors'];
        $result['object']->getForm()->display();
        break;
    case 'edit':
        $adminObject->addItemButton(_AM_COCKTAILS_ADD_GLASS, 'glass.php?op=new', 'add');
        $adminObject->addItemButton(_AM_COCKTAILS_GLASS_LIST, 'glass.php', 'list');
        $adminObject->displayButton('left');
        $form = $glassHandler->get(Request::getInt('id', 0, 'GET'))->getForm();
        $form->display();
        break;
    case 'delete':
        if (1 === Request::getInt('ok', 0, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('glass.php', 3, implode(', ', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            $glassObject = $glassHandler->get(Request::getInt('id', 0, 'POST'));
            if (\is_object($glassObject) && $glassHandler->delete($glassObject)) {
                redirect_header('glass.php', 3, _AM_COCKTAILS_FORMDELOK);
            }
            redirect_header('glass.php', 3, _ERRORS);
        } else {
            $glassObject = $glassHandler->get(Request::getInt('id', 0, 'GET'));
            if (!\is_object($glassObject)) {
                redirect_header('glass.php', 3, _ERRORS);
            }
            xoops_confirm(['ok' => 1, 'id' => Request::getInt('id', 0, 'GET'), 'op' => 'delete'], Request::getUrl('REQUEST_URI', '', 'SERVER'), sprintf(_AM_COCKTAILS_FORMSUREDEL, $glassObject->getVar('name')));
        }
        break;
    case 'list':
    default:
        $adminObject->addItemButton(_AM_COCKTAILS_ADD_GLASS, 'glass.php?op=new', 'add');
        $adminObject->displayButton('left');
        $start              = Request::getInt('start', 0, 'GET');
        $glassPagination    = $helper->getConfig('adminpager');

        $criteria = new \CriteriaCompo();
        $criteria->setSort($sort);
        $criteria->setOrder($order);
        $criteria->setLimit($glassPagination);
        $criteria->setStart($start);

        $glassCount     = $glassHandler->getCount($criteria);
        $glassTempArray = $glassHandler->getAll($criteria);
        $GLOBALS['xoopsTpl']->assign('glassRows', $glassHandler->getCount());

        if ($glassCount > 0) {
            foreach (array_keys($glassTempArray) as $i) {
                $GLOBALS['xoopsTpl']->assign('selectorid', _AM_COCKTAILS_RECIPE_ID);
                $selectorname = $utility::selectSorting(_AM_COCKTAILS_GLASS_NAME, 'name', $helper);
                $GLOBALS['xoopsTpl']->assign('selectorname', $selectorname);
                $GLOBALS['xoopsTpl']->assign('selectordescription', _AM_COCKTAILS_GLASS_DESC);
                $selectorweight = $utility::selectSorting(_AM_COCKTAILS_GLASS_WEIGHT, 'weight', $helper);
                $GLOBALS['xoopsTpl']->assign('selectorweight', $selectorweight);

                $glassId    = (int)$glassTempArray[$i]->getVar('id');
                $glassArray = [
                    'id'          => $glassId,
                    'name'        => $glassTempArray[$i]->getVar('name'),
                    'description' => $utility::truncateHtml((string)$glassTempArray[$i]->getVar('description'), $helper->getConfig('truncatelength')),
                    'weight'      => $glassTempArray[$i]->getVar('weight'),
                    'edit_delete' => "<a href='glass.php?op=edit&amp;id={$glassId}'><img src='{$pathIcon16}/edit.png' alt='" . _EDIT . "' title='" . _EDIT . "'></a>
               <a href='glass.php?op=delete&amp;id={$glassId}'><img src='{$pathIcon16}/delete.png' alt='" . _DELETE . "' title='" . _DELETE . "'></a>",
                ];
                $GLOBALS['xoopsTpl']->appendByRef('glassArrays', $glassArray);
                unset($glassArray);
            }
            unset($glassTempArray);
            if ($glassCount > $glassPagination) {
                xoops_load('XoopsPageNav');
                $pagenav = new \XoopsPageNav($glassCount, $glassPagination, $start, 'start', 'op=list&sort=' . $sort . '&order=' . $order);
                $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav(4));
            }
            echo $GLOBALS['xoopsTpl']->fetch(
                XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname') . '/templates/admin/cocktails_admin_glass.tpl'
            );
        }
        break;
}
require_once __DIR__ . '/admin_footer.php';
