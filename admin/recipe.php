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
$sort  = \in_array($sort, ['id', 'title', 'cid', 'rating_avg', 'online', 'created', 'updated'], true) ? $sort : 'id';

$adminObject->displayNavigation(basename(__FILE__));
$permHelper = new Permission();
$uploadDir  = \Xoops\Helpers\Service\Path::moduleUpload('cocktails', 'recipe') . '/';
$uploadUrl  = \Xoops\Helpers\Service\Url::moduleUpload('cocktails', 'recipe') . '/';

switch ($op) {
    case 'new':
        $adminObject->addItemButton(_AM_COCKTAILS_RECIPE_LIST, 'recipe.php', 'list');
        $adminObject->displayButton('left');

        $recipeObject = $recipeHandler->create();
        $form         = $recipeObject->getForm();
        $form->display();
        break;
    case 'save':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('recipe.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        // Shared hydrate/insert logic (header fields, child ingredient lines and tags).
        $result = $recipeHandler->saveFromRequest($helper, true);
        if ($result['ok']) {
            // Optional image upload (standard XOOPS uploader). Skip silently if no file was sent.
            $recipeObject = $result['object'];
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
                    $uploader->setPrefix('recipe_');
                    if ($uploader->upload()) {
                        $recipeObject->setVar('image', $uploader->getSavedFileName());
                        $recipeHandler->insert($recipeObject, true);
                    }
                }
            }
            redirect_header('recipe.php?op=list', 2, _AM_COCKTAILS_FORMOK);
        }

        echo $result['errors'];
        $form = $result['object']->getForm();
        $form->display();
        break;
    case 'edit':
        $adminObject->addItemButton(_AM_COCKTAILS_ADD_RECIPE, 'recipe.php?op=new', 'add');
        $adminObject->addItemButton(_AM_COCKTAILS_RECIPE_LIST, 'recipe.php', 'list');
        $adminObject->displayButton('left');
        $recipeObject = $recipeHandler->get(Request::getInt('id', 0, 'GET'));
        $form         = $recipeObject->getForm();
        $form->display();
        break;
    case 'delete':
        $selectedIds = \array_filter(\array_map('intval', Request::getArray('recipe_id', [], 'POST')));
        $postedIds   = \array_filter(\array_map('intval', \explode(',', Request::getString('ids', '', 'POST'))));
        $deleteIds   = $postedIds ?: $selectedIds;

        if (1 === Request::getInt('ok', 0, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('recipe.php', 3, implode(', ', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ([] === $deleteIds) {
                $deleteIds = [Request::getInt('id', 0, 'POST')];
            }
            $deleted = 0;
            foreach ($deleteIds as $deleteId) {
                $recipeObject = $recipeHandler->get($deleteId);
                if (\is_object($recipeObject) && $recipeHandler->delete($recipeObject)) {
                    ++$deleted;
                }
            }
            if ($deleted > 0) {
                redirect_header('recipe.php', 3, _AM_COCKTAILS_FORMDELOK);
            } else {
                redirect_header('recipe.php', 3, _ERRORS);
            }
        } else {
            if ([] !== $deleteIds) {
                xoops_confirm(['ok' => 1, 'ids' => \implode(',', $deleteIds), 'op' => 'delete'], Request::getUrl('REQUEST_URI', '', 'SERVER'), sprintf(_AM_COCKTAILS_FORMSUREDEL, \implode(', ', $deleteIds)));
                break;
            }
            $recipeObject = $recipeHandler->get(Request::getInt('id', 0, 'GET'));
            if (!\is_object($recipeObject)) {
                redirect_header('recipe.php', 3, _ERRORS);
            }
            xoops_confirm(['ok' => 1, 'id' => Request::getInt('id', 0, 'GET'), 'op' => 'delete'], Request::getUrl('REQUEST_URI', '', 'SERVER'), sprintf(_AM_COCKTAILS_FORMSUREDEL, $recipeObject->getVar('title')));
        }
        break;
    case 'clone':
        // State-changing action: require a POST confirmation with a valid CSRF token.
        if (1 === Request::getInt('ok', 0, 'POST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('recipe.php', 3, implode(', ', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            $id_field = Request::getInt('id', 0, 'POST');
            if ($utility::cloneRecord('cocktails_recipe', 'id', $id_field)) {
                redirect_header('recipe.php', 3, _AM_COCKTAILS_CLONED_OK);
            } else {
                redirect_header('recipe.php', 3, _AM_COCKTAILS_CLONED_FAILED);
            }
        } else {
            $id_field = Request::getInt('id', 0, 'GET');
            xoops_confirm(['ok' => 1, 'id' => $id_field, 'op' => 'clone'], Request::getUrl('REQUEST_URI', '', 'SERVER'), sprintf(_AM_COCKTAILS_FORMSURECLONE, $id_field));
        }

        break;
    case 'list':
    default:
        $adminObject->addItemButton(_AM_COCKTAILS_ADD_RECIPE, 'recipe.php?op=new', 'add');
        $adminObject->displayButton('left');
        $start                 = Request::getInt('start', 0, 'GET');
        $recipePaginationLimit = $helper->getConfig('adminpager');

        $recipeTempRows = $recipeHandler->getCount();

        $criteria = new \CriteriaCompo();
        $criteria->setSort($sort);
        $criteria->setOrder($order);
        $criteria->setLimit($recipePaginationLimit);
        $criteria->setStart($start);

        $recipeCount     = $recipeHandler->getCount($criteria);
        $recipeTempArray = $recipeHandler->getAll($criteria);

        $GLOBALS['xoopsTpl']->assign('recipeRows', $recipeTempRows);
        $recipeArray = [];

        if ($recipeCount > 0) {
            foreach (array_keys($recipeTempArray) as $i) {
                $GLOBALS['xoopsTpl']->assign('selectorid', _AM_COCKTAILS_RECIPE_ID);
                $recipeArray['id'] = $recipeTempArray[$i]->getVar('id');

                $selectortitle = $utility::selectSorting(_AM_COCKTAILS_RECIPE_TITLE, 'title', $helper);
                $GLOBALS['xoopsTpl']->assign('selectortitle', $selectortitle);
                $recipeArray['title'] = $utility::truncateHtml((string)$recipeTempArray[$i]->getVar('title'), $helper->getConfig('truncatelength'));

                $selectorcid = $utility::selectSorting(_AM_COCKTAILS_RECIPE_CID, 'cid', $helper);
                $GLOBALS['xoopsTpl']->assign('selectorcid', $selectorcid);
                $categoryObject       = $categoryHandler->get($recipeTempArray[$i]->getVar('cid'));
                $recipeArray['cid']   = \is_object($categoryObject) ? $categoryObject->getVar('title') : '-';

                $GLOBALS['xoopsTpl']->assign('selectorimage', _AM_COCKTAILS_RECIPE_IMAGE);
                $recipeImage          = (string)$recipeTempArray[$i]->getVar('image');
                $recipeArray['image'] = '' !== $recipeImage ? "<img src='" . $uploadUrl . \Xoops\Helpers\Utility\HtmlBuilder::escape($recipeImage) . "' alt='' style='max-width:100px'>" : '';

                $selectorrating = $utility::selectSorting(_AM_COCKTAILS_RECIPE_RATING, 'rating_avg', $helper);
                $GLOBALS['xoopsTpl']->assign('selectorrating', $selectorrating);
                $recipeArray['rating_avg'] = $recipeTempArray[$i]->getVar('rating_avg');

                $selectoronline = $utility::selectSorting(_AM_COCKTAILS_RECIPE_ONLINE, 'online', $helper);
                $GLOBALS['xoopsTpl']->assign('selectoronline', $selectoronline);
                $recipeArray['online'] = $recipeTempArray[$i]->getVar('online');

                $GLOBALS['xoopsTpl']->assign('selectorsubmitter', _AM_COCKTAILS_SUBMITTER);
                $recipeArray['submitter'] = cocktails_admin_uname((int)$recipeTempArray[$i]->getVar('uid'));

                $selectorcreated = $utility::selectSorting(_AM_COCKTAILS_RECIPE_CREATED, 'created', $helper);
                $GLOBALS['xoopsTpl']->assign('selectorcreated', $selectorcreated);
                $recipeArray['created'] = formatTimestamp($recipeTempArray[$i]->getVar('created'), 's');

                $selectorupdated = $utility::selectSorting(_AM_COCKTAILS_RECIPE_UPDATED, 'updated', $helper);
                $GLOBALS['xoopsTpl']->assign('selectorupdated', $selectorupdated);
                $recipeArray['updated'] = formatTimestamp($recipeTempArray[$i]->getVar('updated'), 's');

                $recipeId                   = (int)$recipeArray['id'];
                $recipeArray['edit_delete'] = "<a href='recipe.php?op=edit&amp;id={$recipeId}'><img src='{$pathIcon16}/edit.png' alt='" . _EDIT . "' title='" . _EDIT . "'></a>
               <a href='recipe.php?op=delete&amp;id={$recipeId}'><img src='{$pathIcon16}/delete.png' alt='" . _DELETE . "' title='" . _DELETE . "'></a>
               <a href='recipe.php?op=clone&amp;id={$recipeId}'><img src='{$pathIcon16}/editcopy.png' alt='" . _CLONE . "' title='" . _CLONE . "'></a>";

                $GLOBALS['xoopsTpl']->appendByRef('recipeArrays', $recipeArray);
                unset($recipeArray);
            }
            unset($recipeTempArray);
            // Display Navigation
            if ($recipeCount > $recipePaginationLimit) {
                xoops_load('XoopsPageNav');
                $pagenav = new \XoopsPageNav(
                    $recipeCount,
                    $recipePaginationLimit,
                    $start,
                    'start',
                    'op=list' . '&sort=' . $sort . '&order=' . $order
                );
                $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav(4));
            }

            echo $GLOBALS['xoopsTpl']->fetch(
                XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname') . '/templates/admin/cocktails_admin_recipe.tpl'
            );
        }

        break;
}
require_once __DIR__ . '/admin_footer.php';
