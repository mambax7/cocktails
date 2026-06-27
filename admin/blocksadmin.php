<?php declare(strict_types=1);

/**
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * @category        Module
 * @author          XOOPS Development Team
 * @copyright       XOOPS Project
 * @link            https://xoops.org
 * @license         GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 */

use Xmf\Module\Admin;
use Xmf\Request;
use XoopsModules\Mtools\Common\Blocksadmin;
use XoopsModules\Cocktails\Helper;

/** @var Admin $adminObject */
/** @var Helper $helper */
require __DIR__ . '/admin_header.php';
xoops_cp_header();

$moduleDirName = $helper->getDirname();

/** @var \XoopsMySQLDatabase $xoopsDB */
$xoopsDB     = \XoopsDatabaseFactory::getDatabaseConnection();
$blocksadmin = new Blocksadmin($xoopsDB, $helper);

if (!is_object($GLOBALS['xoopsUser']) || !is_object($xoopsModule)
    || !$GLOBALS['xoopsUser']->isAdmin($xoopsModule->mid())) {
    exit(constant('_CO_COCKTAILS_ERROR403'));
}

require_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';
$op  = Request::getString('op', 'list', 'REQUEST');
$bid = 0;
if (in_array($op, ['edit', 'delete', 'delete_ok', 'clone'], true)) {
    $bid = Request::getInt('bid', 0, 'REQUEST');
}

if ('list' === $op) {
    $blocksadmin->listBlocks();
    require_once __DIR__ . '/admin_footer.php';
    exit();
}

if ('order' === $op) {
    $blocksadmin->orderBlock(
        Request::getArray('bid', [], 'POST'),
        Request::getArray('oldtitle', [], 'POST'),
        Request::getArray('oldside', [], 'POST'),
        Request::getArray('oldweight', [], 'POST'),
        Request::getArray('oldvisible', [], 'POST'),
        Request::getArray('oldgroups', [], 'POST'),
        Request::getArray('oldbcachetime', [], 'POST'),
        Request::getArray('oldbmodule', [], 'POST'),
        Request::getArray('title', [], 'POST'),
        Request::getArray('weight', [], 'POST'),
        Request::getArray('visible', [], 'POST'),
        Request::getArray('side', [], 'POST'),
        Request::getArray('bcachetime', [], 'POST'),
        Request::getArray('groups', [], 'POST'),
        Request::getArray('bmodule', [], 'POST')
    );
}

if ('clone' === $op) {
    $blocksadmin->cloneBlock($bid);
}

if ('delete' === $op) {
    if (Request::hasVar('ok', 'POST') && 1 === Request::getInt('ok', 0, 'POST')) {
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($helper->url('admin/blocksadmin.php'), 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $blocksadmin->deleteBlock($bid);
    } else {
        xoops_confirm(['ok' => 1, 'op' => 'delete', 'bid' => $bid], 'blocksadmin.php', constant('_CO_COCKTAILS_DELETE_BLOCK_CONFIRM'), constant('_CO_COCKTAILS_CONFIRM'));
        xoops_cp_footer();
    }
}

if ('edit' === $op) {
    $blocksadmin->editBlock($bid);
}

if ('edit_ok' === $op) {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header($helper->url('admin/blocksadmin.php'), 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
    }
    $bid        = Request::getInt('bid', 0, 'POST');
    $btitle     = Request::getString('btitle', '', 'POST');
    $bside      = Request::getString('bside', '', 'POST');
    $bweight    = Request::getString('bweight', '', 'POST');
    $bvisible   = Request::getString('bvisible', '', 'POST');
    $bcachetime = Request::getString('bcachetime', '', 'POST');
    $bmodule    = Request::getArray('bmodule', [], 'POST');
    $options    = Request::getArray('options', [], 'POST');
    $groups     = Request::getArray('groups', [], 'POST');
    $blocksadmin->updateBlock($bid, $btitle, $bside, $bweight, $bvisible, $bcachetime, $bmodule, $options, $groups);
}

if ('clone_ok' === $op) {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header($helper->url('admin/blocksadmin.php'), 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
    }
    $bid        = Request::getInt('bid', 0, 'POST');
    $bside      = Request::getString('bside', '', 'POST');
    $bweight    = Request::getString('bweight', '', 'POST');
    $bvisible   = Request::getString('bvisible', '', 'POST');
    $bcachetime = Request::getString('bcachetime', '', 'POST');
    $bmodule    = Request::getArray('bmodule', [], 'POST');
    $options    = Request::getArray('options', [], 'POST');
    $groups     = Request::getArray('groups', [], 'POST');
    $blocksadmin->isBlockCloned($bid, $bside, $bweight, $bvisible, $bcachetime, $bmodule, $options, $groups);
}

require __DIR__ . '/admin_footer.php';
