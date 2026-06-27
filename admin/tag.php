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
$sort  = Request::getString('sort', 'name', 'GET');
$sort  = \in_array($sort, ['id', 'name'], true) ? $sort : 'name';

$adminObject->displayNavigation(basename(__FILE__));

switch ($op) {
    case 'new':
        $adminObject->addItemButton(_AM_COCKTAILS_TAG_LIST, 'tag.php', 'list');
        $adminObject->displayButton('left');
        $tagHandler->create()->getForm()->display();
        break;
    case 'save':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('tag.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $result = $tagHandler->saveFromRequest($helper);
        if ($result['ok']) {
            redirect_header('tag.php?op=list', 2, _AM_COCKTAILS_FORMOK);
        }
        echo $result['errors'];
        $result['object']->getForm()->display();
        break;
    case 'edit':
        $adminObject->addItemButton(_AM_COCKTAILS_ADD_TAG, 'tag.php?op=new', 'add');
        $adminObject->addItemButton(_AM_COCKTAILS_TAG_LIST, 'tag.php', 'list');
        $adminObject->displayButton('left');
        $tagHandler->get(Request::getInt('id', 0, 'GET'))->getForm()->display();
        break;
    case 'delete':
        if (1 === Request::getInt('ok', 0, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('tag.php', 3, implode(', ', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            $tagObject = $tagHandler->get(Request::getInt('id', 0, 'POST'));
            if (\is_object($tagObject) && $tagHandler->delete($tagObject)) {
                redirect_header('tag.php', 3, _AM_COCKTAILS_FORMDELOK);
            }
            redirect_header('tag.php', 3, _ERRORS);
        } else {
            $tagObject = $tagHandler->get(Request::getInt('id', 0, 'GET'));
            if (!\is_object($tagObject)) {
                redirect_header('tag.php', 3, _ERRORS);
            }
            xoops_confirm(['ok' => 1, 'id' => Request::getInt('id', 0, 'GET'), 'op' => 'delete'], Request::getUrl('REQUEST_URI', '', 'SERVER'), sprintf(_AM_COCKTAILS_FORMSUREDEL, $tagObject->getVar('name')));
        }
        break;
    case 'list':
    default:
        $adminObject->addItemButton(_AM_COCKTAILS_ADD_TAG, 'tag.php?op=new', 'add');
        $adminObject->displayButton('left');
        $start         = Request::getInt('start', 0, 'GET');
        $tagPagination = $helper->getConfig('adminpager');

        $criteria = new \CriteriaCompo();
        $criteria->setSort($sort);
        $criteria->setOrder($order);
        $criteria->setLimit($tagPagination);
        $criteria->setStart($start);

        $tagCount     = $tagHandler->getCount($criteria);
        $tagTempArray = $tagHandler->getAll($criteria);
        $GLOBALS['xoopsTpl']->assign('tagRows', $tagHandler->getCount());

        if ($tagCount > 0) {
            foreach (array_keys($tagTempArray) as $i) {
                $GLOBALS['xoopsTpl']->assign('selectorid', _AM_COCKTAILS_RECIPE_ID);
                $selectorname = $utility::selectSorting(_AM_COCKTAILS_TAG_NAME, 'name', $helper);
                $GLOBALS['xoopsTpl']->assign('selectorname', $selectorname);
                $GLOBALS['xoopsTpl']->assign('selectoruses', _AM_COCKTAILS_TAG_USES);

                $tagId    = (int)$tagTempArray[$i]->getVar('id');
                $tagArray = [
                    'id'          => $tagId,
                    'name'        => $tagTempArray[$i]->getVar('name'),
                    'uses'        => \count($tagHandler->recipeIdsForTag($tagId)),
                    'edit_delete' => "<a href='tag.php?op=edit&amp;id={$tagId}'><img src='{$pathIcon16}/edit.png' alt='" . _EDIT . "' title='" . _EDIT . "'></a>
               <a href='tag.php?op=delete&amp;id={$tagId}'><img src='{$pathIcon16}/delete.png' alt='" . _DELETE . "' title='" . _DELETE . "'></a>",
                ];
                $GLOBALS['xoopsTpl']->appendByRef('tagArrays', $tagArray);
                unset($tagArray);
            }
            unset($tagTempArray);
            if ($tagCount > $tagPagination) {
                xoops_load('XoopsPageNav');
                $pagenav = new \XoopsPageNav($tagCount, $tagPagination, $start, 'start', 'op=list&sort=' . $sort . '&order=' . $order);
                $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav(4));
            }
            echo $GLOBALS['xoopsTpl']->fetch(
                XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname') . '/templates/admin/cocktails_admin_tag.tpl'
            );
        }
        break;
}
require_once __DIR__ . '/admin_footer.php';
