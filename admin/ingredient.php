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
$sort  = \in_array($sort, ['id', 'name', 'type', 'abv', 'weight', 'online'], true) ? $sort : 'id';

$adminObject->displayNavigation(basename(__FILE__));
$permHelper = new Permission();
$uploadDir  = \Xoops\Helpers\Service\Path::moduleUpload('cocktails', 'ingredient') . '/';
$uploadUrl  = \Xoops\Helpers\Service\Url::moduleUpload('cocktails', 'ingredient') . '/';

switch ($op) {
    case 'new':
        $adminObject->addItemButton(_AM_COCKTAILS_INGREDIENT_LIST, 'ingredient.php', 'list');
        $adminObject->displayButton('left');

        $ingredientObject = $ingredientHandler->create();
        $form             = $ingredientObject->getForm();
        $form->display();
        break;
    case 'save':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('ingredient.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $result = $ingredientHandler->saveFromRequest($helper);
        if ($result['ok']) {
            // Optional image upload (standard XOOPS uploader). Skip silently if no file was sent.
            $ingredientObject = $result['object'];
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
                    $uploader->setPrefix('ing_');
                    if ($uploader->upload()) {
                        $ingredientObject->setVar('image', $uploader->getSavedFileName());
                        $ingredientHandler->insert($ingredientObject, true);
                    }
                }
            }
            redirect_header('ingredient.php?op=list', 2, _AM_COCKTAILS_FORMOK);
        }

        echo $result['errors'];
        $form = $result['object']->getForm();
        $form->display();
        break;
    case 'edit':
        $adminObject->addItemButton(_AM_COCKTAILS_ADD_INGREDIENT, 'ingredient.php?op=new', 'add');
        $adminObject->addItemButton(_AM_COCKTAILS_INGREDIENT_LIST, 'ingredient.php', 'list');
        $adminObject->displayButton('left');
        $ingredientObject = $ingredientHandler->get(Request::getInt('id', 0, 'GET'));
        $form             = $ingredientObject->getForm();
        $form->display();
        break;
    case 'delete':
        $selectedIds = \array_filter(\array_map('intval', Request::getArray('ingredient_id', [], 'POST')));
        $postedIds   = \array_filter(\array_map('intval', \explode(',', Request::getString('ids', '', 'POST'))));
        $deleteIds   = $postedIds ?: $selectedIds;

        if (1 === Request::getInt('ok', 0, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('ingredient.php', 3, implode(', ', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ([] === $deleteIds) {
                $deleteIds = [Request::getInt('id', 0, 'POST')];
            }
            $deleted = 0;
            foreach ($deleteIds as $deleteId) {
                $ingredientObject = $ingredientHandler->get($deleteId);
                if (\is_object($ingredientObject) && $ingredientHandler->delete($ingredientObject)) {
                    ++$deleted;
                }
            }
            if ($deleted > 0) {
                redirect_header('ingredient.php', 3, _AM_COCKTAILS_FORMDELOK);
            } else {
                redirect_header('ingredient.php', 3, _ERRORS);
            }
        } else {
            if ([] !== $deleteIds) {
                xoops_confirm(['ok' => 1, 'ids' => \implode(',', $deleteIds), 'op' => 'delete'], Request::getUrl('REQUEST_URI', '', 'SERVER'), sprintf(_AM_COCKTAILS_FORMSUREDEL, \implode(', ', $deleteIds)));
                break;
            }
            $ingredientObject = $ingredientHandler->get(Request::getInt('id', 0, 'GET'));
            if (!\is_object($ingredientObject)) {
                redirect_header('ingredient.php', 3, _ERRORS);
            }
            xoops_confirm(['ok' => 1, 'id' => Request::getInt('id', 0, 'GET'), 'op' => 'delete'], Request::getUrl('REQUEST_URI', '', 'SERVER'), sprintf(_AM_COCKTAILS_FORMSUREDEL, $ingredientObject->getVar('name')));
        }
        break;
    case 'clone':
        if (1 === Request::getInt('ok', 0, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('ingredient.php', 3, implode(', ', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            $id_field = Request::getInt('id', 0, 'POST');
            if ($utility::cloneRecord('cocktails_ingredient', 'id', $id_field)) {
                redirect_header('ingredient.php', 3, _AM_COCKTAILS_CLONED_OK);
            } else {
                redirect_header('ingredient.php', 3, _AM_COCKTAILS_CLONED_FAILED);
            }
        } else {
            $id_field = Request::getInt('id', 0, 'GET');
            xoops_confirm(['ok' => 1, 'id' => $id_field, 'op' => 'clone'], Request::getUrl('REQUEST_URI', '', 'SERVER'), sprintf(_AM_COCKTAILS_FORMSURECLONE, $id_field));
        }

        break;
    case 'list':
    default:
        $adminObject->addItemButton(_AM_COCKTAILS_ADD_INGREDIENT, 'ingredient.php?op=new', 'add');
        $adminObject->displayButton('left');
        $start                     = Request::getInt('start', 0, 'GET');
        $ingredientPaginationLimit = $helper->getConfig('adminpager');

        $ingredientTempRows = $ingredientHandler->getCount();

        $criteria = new \CriteriaCompo();
        $criteria->setSort($sort);
        $criteria->setOrder($order);
        $criteria->setLimit($ingredientPaginationLimit);
        $criteria->setStart($start);

        $ingredientCount     = $ingredientHandler->getCount($criteria);
        $ingredientTempArray = $ingredientHandler->getAll($criteria);

        $GLOBALS['xoopsTpl']->assign('ingredientRows', $ingredientTempRows);
        $ingredientArray = [];

        if ($ingredientCount > 0) {
            foreach (array_keys($ingredientTempArray) as $i) {
                $GLOBALS['xoopsTpl']->assign('selectorid', _AM_COCKTAILS_RECIPE_ID);
                $ingredientArray['id'] = $ingredientTempArray[$i]->getVar('id');

                $selectorname = $utility::selectSorting(_AM_COCKTAILS_INGREDIENT_NAME, 'name', $helper);
                $GLOBALS['xoopsTpl']->assign('selectorname', $selectorname);
                $ingredientArray['name'] = $utility::truncateHtml((string)$ingredientTempArray[$i]->getVar('name'), $helper->getConfig('truncatelength'));

                $selectortype = $utility::selectSorting(_AM_COCKTAILS_INGREDIENT_TYPE, 'type', $helper);
                $GLOBALS['xoopsTpl']->assign('selectortype', $selectortype);
                $ingredientArray['type'] = $ingredientTempArray[$i]->getVar('type');

                $selectorabv = $utility::selectSorting(_AM_COCKTAILS_INGREDIENT_ABV, 'abv', $helper);
                $GLOBALS['xoopsTpl']->assign('selectorabv', $selectorabv);
                $ingredientArray['abv'] = $ingredientTempArray[$i]->getVar('abv');

                $GLOBALS['xoopsTpl']->assign('selectordescription', _AM_COCKTAILS_INGREDIENT_DESC);
                $ingredientArray['description'] = $utility::truncateHtml((string)$ingredientTempArray[$i]->getVar('description'), $helper->getConfig('truncatelength'));

                $GLOBALS['xoopsTpl']->assign('selectorimage', _AM_COCKTAILS_INGREDIENT_IMAGE);
                $ingredientImage          = (string)$ingredientTempArray[$i]->getVar('image');
                $ingredientArray['image'] = '' !== $ingredientImage ? "<img src='" . $uploadUrl . \Xoops\Helpers\Utility\HtmlBuilder::escape($ingredientImage) . "' alt='' style='max-width:100px'>" : '';

                $selectorweight = $utility::selectSorting(_AM_COCKTAILS_INGREDIENT_WEIGHT, 'weight', $helper);
                $GLOBALS['xoopsTpl']->assign('selectorweight', $selectorweight);
                $ingredientArray['weight'] = $ingredientTempArray[$i]->getVar('weight');

                $selectoronline = $utility::selectSorting(_AM_COCKTAILS_INGREDIENT_ONLINE, 'online', $helper);
                $GLOBALS['xoopsTpl']->assign('selectoronline', $selectoronline);
                $ingredientArray['online'] = $ingredientTempArray[$i]->getVar('online');

                $ingredientId                   = (int)$ingredientArray['id'];
                $ingredientArray['edit_delete'] = "<a href='ingredient.php?op=edit&amp;id={$ingredientId}'><img src='{$pathIcon16}/edit.png' alt='" . _EDIT . "' title='" . _EDIT . "'></a>
               <a href='ingredient.php?op=delete&amp;id={$ingredientId}'><img src='{$pathIcon16}/delete.png' alt='" . _DELETE . "' title='" . _DELETE . "'></a>
               <a href='ingredient.php?op=clone&amp;id={$ingredientId}'><img src='{$pathIcon16}/editcopy.png' alt='" . _CLONE . "' title='" . _CLONE . "'></a>";

                $GLOBALS['xoopsTpl']->appendByRef('ingredientArrays', $ingredientArray);
                unset($ingredientArray);
            }
            unset($ingredientTempArray);
            if ($ingredientCount > $ingredientPaginationLimit) {
                xoops_load('XoopsPageNav');
                $pagenav = new \XoopsPageNav(
                    $ingredientCount,
                    $ingredientPaginationLimit,
                    $start,
                    'start',
                    'op=list' . '&sort=' . $sort . '&order=' . $order
                );
                $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav(4));
            }

            echo $GLOBALS['xoopsTpl']->fetch(
                XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname') . '/templates/admin/cocktails_admin_ingredient.tpl'
            );
        }

        break;
}
require_once __DIR__ . '/admin_footer.php';
