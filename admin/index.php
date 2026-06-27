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
use XoopsModules\Mtools\Common\DirectoryChecker;
use XoopsModules\Mtools\Common\Configurator;
use XoopsModules\Mtools\Common\TestdataButtons;
use XoopsModules\Cocktails\Helper;
use XoopsModules\Cocktails\Utility;

/** @var Admin $adminObject */
/** @var Helper $helper */
/** @var Utility $utility */
require_once __DIR__ . '/admin_header.php';
xoops_cp_header();
$adminObject = \Xmf\Module\Admin::getInstance();

//count "total Recipe"
/** @var \XoopsPersistableObjectHandler $recipeHandler */
$totalRecipe = $recipeHandler->getCount();
//count "total Ingredient"
/** @var \XoopsPersistableObjectHandler $ingredientHandler */
$totalIngredient = $ingredientHandler->getCount();
//count "total Category"
/** @var \XoopsPersistableObjectHandler $categoryHandler */
$totalCategory = $categoryHandler->getCount();
//count "total Glass"
/** @var \XoopsPersistableObjectHandler $glassHandler */
$totalGlass = $glassHandler->getCount();
//count "total Rating"
/** @var \XoopsPersistableObjectHandler $ratingHandler */
$totalRating = $ratingHandler->getCount();
//count "pending" (recipes that are offline / awaiting moderation)
$criteriaPending = new \CriteriaCompo();
$criteriaPending->add(new \Criteria('online', 0));
$totalPending = $recipeHandler->getCount($criteriaPending);

// InfoBox Statistics
$adminObject->addInfoBox(_AM_COCKTAILS_STATISTICS);

$adminObject->addInfoBoxLine(sprintf(_AM_COCKTAILS_THEREARE_RECIPE, $totalRecipe));
$adminObject->addInfoBoxLine(sprintf(_AM_COCKTAILS_THEREARE_INGREDIENT, $totalIngredient));
$adminObject->addInfoBoxLine(sprintf(_AM_COCKTAILS_THEREARE_CATEGORY, $totalCategory));
$adminObject->addInfoBoxLine(sprintf(_AM_COCKTAILS_THEREARE_GLASS, $totalGlass));
$adminObject->addInfoBoxLine(sprintf(_AM_COCKTAILS_THEREARE_RATING, $totalRating));
$adminObject->addInfoBoxLine(sprintf(_AM_COCKTAILS_THEREARE_PENDING, $totalPending));

//------ check Upload Folders ---------------
$adminObject->addConfigBoxLine('');
$redirectFile = $_SERVER['SCRIPT_NAME'];

$configurator  = Configurator::forModule($helper);
$uploadFolders = $configurator->uploadFolders;

foreach (array_keys($uploadFolders) as $i) {
    $adminObject->addConfigBoxLine(DirectoryChecker::getDirectoryStatus($uploadFolders[$i], 0755, $redirectFile));
}

// Render Index
$adminObject->displayNavigation(basename(__FILE__));

//------------- Test Data Buttons ----------------------------
if ($helper->getConfig('displaySampleButton')) {
    TestdataButtons::loadButtonConfig($adminObject, $helper);
    $adminObject->displayButton('left', '');
}
$op = Request::getString('op', 0, 'GET');
switch ($op) {
    case 'hide_buttons':
        TestdataButtons::hideButtons($helper);
        break;
    case 'show_buttons':
        TestdataButtons::showButtons($helper);
        break;
}
//------------- End Test Data Buttons ----------------------------

$adminObject->displayIndex();
echo $utility::getServerStats();

require_once __DIR__ . '/admin_footer.php';
