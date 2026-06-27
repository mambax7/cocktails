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

//It recovered the value of argument op in URL$
$op    = Request::getString('op', 'list', 'REQUEST');
$order = \strtolower(Request::getString('order', 'desc', 'GET'));
$order = \in_array($order, ['asc', 'desc'], true) ? $order : 'desc';
$sort  = Request::getString('sort', 'id', 'GET');
$sort  = \in_array($sort, ['id', 'pid', 'title', 'weight', 'color', 'online'], true) ? $sort : 'id';

$adminObject->displayNavigation(basename(__FILE__));
$permHelper = new Permission();
$uploadDir  = \Xoops\Helpers\Service\Path::moduleUpload('cocktails', 'category') . '/';
$uploadUrl  = \Xoops\Helpers\Service\Url::moduleUpload('cocktails', 'category') . '/';

switch ($op) {
    case 'new':
        $adminObject->addItemButton(_AM_COCKTAILS_CATEGORY_LIST, 'category.php', 'list');
        $adminObject->displayButton('left');

        $categoryObject = $categoryHandler->create();
        $form           = $categoryObject->getForm();
        $form->display();
        break;
    case 'save':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('category.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $result = $categoryHandler->saveFromRequest($helper);
        if ($result['ok']) {
            // Optional image upload (standard XOOPS uploader). Skip silently if no file was sent.
            $categoryObject = $result['object'];
            if (isset($_FILES['image']) && \is_array($_FILES['image']) && '' !== (string)($_FILES['image']['name'] ?? '')
                && \UPLOAD_ERR_NO_FILE !== (int)($_FILES['image']['error'] ?? \UPLOAD_ERR_NO_FILE)) {
                require_once XOOPS_ROOT_PATH . '/class/uploader.php';
                $allowedMimetypes = (array)$helper->getConfig('mimetypes');
                $maxSize          = (int)$helper->getConfig('maxsize');
                if (!\is_dir($uploadDir)) {
                    @\mkdir($uploadDir, 0755, true);
                }
                $uploader = new \XoopsMediaUploader($uploadDir, $allowedMimetypes, $maxSize);
                if ($uploader->fetchMedia('image')) {
                    $uploader->setPrefix('cat_');
                    if ($uploader->upload()) {
                        $categoryObject->setVar('image', $uploader->getSavedFileName());
                        $categoryHandler->insert($categoryObject, true);
                    }
                }
            }
            redirect_header('category.php?op=list', 2, _AM_COCKTAILS_FORMOK);
        }

        echo $result['errors'];
        $form = $result['object']->getForm();
        $form->display();
        break;
    case 'edit':
        $adminObject->addItemButton(_AM_COCKTAILS_ADD_CATEGORY, 'category.php?op=new', 'add');
        $adminObject->addItemButton(_AM_COCKTAILS_CATEGORY_LIST, 'category.php', 'list');
        $adminObject->displayButton('left');
        $categoryObject = $categoryHandler->get(Request::getInt('id', 0, 'GET'));
        $form           = $categoryObject->getForm();
        $form->display();
        break;
    case 'delete':
        $selectedIds = \array_filter(\array_map('intval', Request::getArray('category_id', [], 'POST')));
        $postedIds   = \array_filter(\array_map('intval', \explode(',', Request::getString('ids', '', 'POST'))));
        $deleteIds   = $postedIds ?: $selectedIds;

        if (1 === Request::getInt('ok', 0, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('category.php', 3, implode(', ', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ([] === $deleteIds) {
                $deleteIds = [Request::getInt('id', 0, 'POST')];
            }
            $deleted = 0;
            foreach ($deleteIds as $deleteId) {
                $categoryObject = $categoryHandler->get($deleteId);
                if (\is_object($categoryObject) && $categoryHandler->delete($categoryObject)) {
                    ++$deleted;
                }
            }
            if ($deleted > 0) {
                redirect_header('category.php', 3, _AM_COCKTAILS_FORMDELOK);
            } else {
                redirect_header('category.php', 3, _ERRORS);
            }
        } else {
            if ([] !== $deleteIds) {
                xoops_confirm(['ok' => 1, 'ids' => \implode(',', $deleteIds), 'op' => 'delete'], Request::getUrl('REQUEST_URI', '', 'SERVER'), sprintf(_AM_COCKTAILS_FORMSUREDEL, \implode(', ', $deleteIds)));
                break;
            }
            $categoryObject = $categoryHandler->get(Request::getInt('id', 0, 'GET'));
            if (!\is_object($categoryObject)) {
                redirect_header('category.php', 3, _ERRORS);
            }
            xoops_confirm(['ok' => 1, 'id' => Request::getInt('id', 0, 'GET'), 'op' => 'delete'], Request::getUrl('REQUEST_URI', '', 'SERVER'), sprintf(_AM_COCKTAILS_FORMSUREDEL, $categoryObject->getVar('title')));
        }
        break;
    case 'clone':
        if (1 === Request::getInt('ok', 0, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('category.php', 3, implode(', ', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            $id_field = Request::getInt('id', 0, 'POST');
            if ($utility::cloneRecord('cocktails_category', 'id', $id_field)) {
                redirect_header('category.php', 3, _AM_COCKTAILS_CLONED_OK);
            } else {
                redirect_header('category.php', 3, _AM_COCKTAILS_CLONED_FAILED);
            }
        } else {
            $id_field = Request::getInt('id', 0, 'GET');
            xoops_confirm(['ok' => 1, 'id' => $id_field, 'op' => 'clone'], Request::getUrl('REQUEST_URI', '', 'SERVER'), sprintf(_AM_COCKTAILS_FORMSURECLONE, $id_field));
        }

        break;
    case 'list':
    default:
        $adminObject->addItemButton(_AM_COCKTAILS_ADD_CATEGORY, 'category.php?op=new', 'add');
        $adminObject->displayButton('left');
        $start                   = Request::getInt('start', 0, 'GET');
        $categoryPaginationLimit = $helper->getConfig('adminpager');

        $categoryTempRows = $categoryHandler->getCount();

        $criteria = new \CriteriaCompo();
        $criteria->setSort($sort);
        $criteria->setOrder($order);
        $criteria->setLimit($categoryPaginationLimit);
        $criteria->setStart($start);

        $categoryCount     = $categoryHandler->getCount($criteria);
        $categoryTempArray = $categoryHandler->getAll($criteria);

        $GLOBALS['xoopsTpl']->assign('categoryRows', $categoryTempRows);
        $categoryArray = [];

        if ($categoryCount > 0) {
            foreach (array_keys($categoryTempArray) as $i) {
                $GLOBALS['xoopsTpl']->assign('selectorid', _AM_COCKTAILS_CATEGORY_ID);
                $categoryArray['id'] = $categoryTempArray[$i]->getVar('id');

                $selectorpid = $utility::selectSorting(_AM_COCKTAILS_CATEGORY_PID, 'pid', $helper);
                $GLOBALS['xoopsTpl']->assign('selectorpid', $selectorpid);
                $categoryArray['pid'] = $categoryTempArray[$i]->getVar('pid');

                $selectortitle = $utility::selectSorting(_AM_COCKTAILS_CATEGORY_TITLE, 'title', $helper);
                $GLOBALS['xoopsTpl']->assign('selectortitle', $selectortitle);
                $categoryArray['title'] = $utility::truncateHtml((string)$categoryTempArray[$i]->getVar('title'), $helper->getConfig('truncatelength'));

                $GLOBALS['xoopsTpl']->assign('selectordescription', _AM_COCKTAILS_CATEGORY_DESC);
                $categoryArray['description'] = $utility::truncateHtml((string)$categoryTempArray[$i]->getVar('description'), $helper->getConfig('truncatelength'));

                $GLOBALS['xoopsTpl']->assign('selectorimage', _AM_COCKTAILS_CATEGORY_IMAGE);
                $categoryImage          = (string)$categoryTempArray[$i]->getVar('image');
                $categoryArray['image'] = '' !== $categoryImage ? "<img src='" . $uploadUrl . \Xoops\Helpers\Utility\HtmlBuilder::escape($categoryImage) . "' alt='' style='max-width:100px'>" : '';

                $selectorweight = $utility::selectSorting(_AM_COCKTAILS_CATEGORY_WEIGHT, 'weight', $helper);
                $GLOBALS['xoopsTpl']->assign('selectorweight', $selectorweight);
                $categoryArray['weight'] = $categoryTempArray[$i]->getVar('weight');

                $selectorcolor = $utility::selectSorting(_AM_COCKTAILS_CATEGORY_COLOR, 'color', $helper);
                $GLOBALS['xoopsTpl']->assign('selectorcolor', $selectorcolor);
                $categoryArray['color'] = $categoryTempArray[$i]->getVar('color');

                $selectoronline = $utility::selectSorting(_AM_COCKTAILS_CATEGORY_ONLINE, 'online', $helper);
                $GLOBALS['xoopsTpl']->assign('selectoronline', $selectoronline);
                $categoryArray['online'] = $categoryTempArray[$i]->getVar('online');

                $categoryId                   = (int)$categoryArray['id'];
                $categoryArray['edit_delete'] = "<a href='category.php?op=edit&amp;id={$categoryId}'><img src='{$pathIcon16}/edit.png' alt='" . _EDIT . "' title='" . _EDIT . "'></a>
               <a href='category.php?op=delete&amp;id={$categoryId}'><img src='{$pathIcon16}/delete.png' alt='" . _DELETE . "' title='" . _DELETE . "'></a>
               <a href='category.php?op=clone&amp;id={$categoryId}'><img src='{$pathIcon16}/editcopy.png' alt='" . _CLONE . "' title='" . _CLONE . "'></a>";

                $GLOBALS['xoopsTpl']->appendByRef('categoryArrays', $categoryArray);
                unset($categoryArray);
            }
            unset($categoryTempArray);
            if ($categoryCount > $categoryPaginationLimit) {
                xoops_load('XoopsPageNav');
                $pagenav = new \XoopsPageNav(
                    $categoryCount,
                    $categoryPaginationLimit,
                    $start,
                    'start',
                    'op=list' . '&sort=' . $sort . '&order=' . $order
                );
                $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav(4));
            }

            echo $GLOBALS['xoopsTpl']->fetch(
                XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname') . '/templates/admin/cocktails_admin_category.tpl'
            );
        }

        break;
}
require_once __DIR__ . '/admin_footer.php';
