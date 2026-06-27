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
use XoopsModules\Mtools;
use XoopsModules\Cocktails\Helper;

/** @var Admin $adminObject */
/** @var Helper $helper */
require_once __DIR__ . '/admin_header.php';
xoops_cp_header();

$adminObject->displayNavigation(basename(__FILE__));
$tokenHtml = $GLOBALS['xoopsSecurity']->getTokenHTML();

echo <<<EOF
    <form method="post" class="form-inline">
    <div class="form-group">
    <input name="show" class="btn btn-default" type="submit" value="Show SQL">
    </div>
    <div class="form-group">
    <input name="migrate" class="btn btn-default" type="submit" value="Do Migration">
    </div>
    <div class="form-group">
    <input name="schema" class="btn btn-default" type="submit" value="Write Schema">
    </div>
    {$tokenHtml}
    </form>
    EOF;

/** @var Mtools\Common\Configurator $configurator */
$configurator = Mtools\Common\Configurator::forModule($helper);
/** @var Mtools\Common\Migrate $migrator */
$migrator = new Mtools\Common\Migrate($configurator);

$op        = Request::getCmd('op', 'show', 'REQUEST');
$opShow    = Request::getCmd('show', '', 'POST');
$opMigrate = Request::getCmd('migrate', '', 'POST');
$opSchema  = Request::getCmd('schema', '', 'POST');
$op        = !empty($opShow) ? 'show' : $op;
$op        = !empty($opMigrate) ? 'migrate' : $op;
$op        = !empty($opSchema) ? 'schema' : $op;

$message = '';

switch ($op) {
    case 'show':
    default:
        $queue = $migrator->getSynchronizeDDL();
        if (!empty($queue)) {
            echo "<pre>\n";
            foreach ($queue as $line) {
                echo $line . ";\n";
            }
            echo "</pre>\n";
        }
        break;
    case 'migrate':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('migrate.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $migrator->synchronizeSchema();
        $message = constant('_CO_COCKTAILS_MIGRATE_OK');
        break;
    case 'schema':
        xoops_confirm(['op' => 'confirmwrite'], 'migrate.php', constant('_CO_COCKTAILS_MIGRATE_WARNING'), constant('_CO_COCKTAILS_CONFIRM'));
        break;
    case 'confirmwrite':
        if ($GLOBALS['xoopsSecurity']->check()) {
            $migrator->saveCurrentSchema();
            $message = constant('_CO_COCKTAILS_MIGRATE_SCHEMA_OK');
        }
        break;
}

echo "<div>$message</div>";

require_once __DIR__ . '/admin_footer.php';
