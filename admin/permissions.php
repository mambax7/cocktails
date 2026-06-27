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

/** @var Admin $adminObject */
require_once __DIR__ . '/admin_header.php';
xoops_cp_header();
require XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';

if ('' !== Request::getString('submit', '', 'POST')) {
    redirect_header(\Xoops\Helpers\Service\Url::module((string)$GLOBALS['xoopsModule']->dirname(), 'admin/permissions.php'), 1, _AM_COCKTAILS_PERMISSIONS_GPERMUPDATED);
}

$adminObject->displayNavigation(basename(__FILE__));

$permission                = Request::getInt('permission', 1, 'POST');
$permission                = \max(1, \min(2, $permission));
$selected                  = ['', ''];
$selected[$permission - 1] = ' selected';

echo "
<form method='post' name='fselperm' action='permissions.php'>
    <table border=0>
        <tr>
            <td>
                <select name='permission' onChange='document.fselperm.submit()'>
                    <option value='1'" . $selected[0] . '>' . _AM_COCKTAILS_PERMISSIONS_GLOBAL . "</option>
                    <option value='2'" . $selected[1] . '>' . _AM_COCKTAILS_PERMISSIONS_READ . '</option>
                </select>
            </td>
        </tr>
    </table>
</form>';

$module_id = $GLOBALS['xoopsModule']->getVar('mid');
if (1 === $permission) {
    $formTitle   = _AM_COCKTAILS_PERMISSIONS_GLOBAL;
    $permName    = 'cocktails_ac';
    $permDesc    = _AM_COCKTAILS_PERMISSIONS_GLOBAL_DESC;
    $globalPerms = [
        '8'  => _AM_COCKTAILS_PERM_AC_SUBMIT,
        '16' => _AM_COCKTAILS_PERM_AC_AUTO_APPROVE,
        '32' => _AM_COCKTAILS_PERM_AC_EDIT_OWN,
        '64' => _AM_COCKTAILS_PERM_AC_RATE,
    ];
    $permform = new \XoopsGroupPermForm($formTitle, $module_id, $permName, $permDesc, 'admin/permissions.php');
    foreach ($globalPerms as $perm_id => $perm_name) {
        $permform->addItem($perm_id, $perm_name);
    }
    echo $permform->render();
    echo '<br><br>';
} else {
    $formTitle = _AM_COCKTAILS_PERMISSIONS_READ;
    $permName  = 'cocktails_read';
    $permDesc  = _AM_COCKTAILS_PERMISSIONS_READ_DESC;
    $permform  = new \XoopsGroupPermForm($formTitle, $module_id, $permName, $permDesc, 'admin/permissions.php');

    $criteria = new \CriteriaCompo();
    $criteria->setSort('weight, title');
    $criteria->setOrder('ASC');
    $categoryArray = $categoryHandler->getAll($criteria);
    $categoryCount = \count($categoryArray);
    foreach (array_keys($categoryArray) as $i) {
        $permform->addItem((int)$categoryArray[$i]->getVar('id'), $categoryArray[$i]->getVar('title'));
    }
    if ($categoryCount > 0) {
        echo $permform->render();
        echo '<br><br>';
    } else {
        redirect_header('category.php?op=new', 3, _AM_COCKTAILS_PERMISSIONS_NOPERMSSET);
    }
}
unset($permform);
require_once __DIR__ . '/admin_footer.php';
