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
use XoopsModules\Cocktails\Helper;
use XoopsModules\Cocktails\Utility;

/** @var Admin $adminObject */
/** @var Utility $utility */
/** @var Helper $helper */
require \dirname(__DIR__) . '/bootstrap.php';

$moduleDirName      = \basename(\dirname(__DIR__));
$moduleDirNameUpper = \mb_strtoupper($moduleDirName);

/** @var \XoopsDatabase $db */
$db      = \XoopsDatabaseFactory::getDatabaseConnection();
$helper  = Helper::getInstance();
$utility = new Utility();

$helper->loadLanguage('common');

$pathIcon16 = Admin::iconUrl('', '16');
$pathIcon32 = Admin::iconUrl('', '32');

// Standard {UP}_* path/URL constants from the shared, helper-backed module context.
$context = \XoopsModules\Mtools\Module\ModuleContext::fromHelper($helper);
$context->defineConstants();

// Module-specific constants not covered by the standard set.
if (!defined($moduleDirNameUpper . '_CONSTANTS_DEFINED')) {
    define($moduleDirNameUpper . '_RECIPE_IMAGES_URL', $context->uploadUrl('recipe'));
    define($moduleDirNameUpper . '_RECIPE_IMAGES_PATH', $context->uploadPath('recipe'));
    define($moduleDirNameUpper . '_RECIPE_THUMBS_URL', $context->uploadUrl('recipe/thumbs'));
    define($moduleDirNameUpper . '_RECIPE_THUMBS_PATH', $context->uploadPath('recipe/thumbs'));
    define($moduleDirNameUpper . '_ING_IMAGES_URL', $context->uploadUrl('ingredient'));
    define($moduleDirNameUpper . '_ING_IMAGES_PATH', $context->uploadPath('ingredient'));
    define($moduleDirNameUpper . '_CAT_IMAGES_URL', $context->uploadUrl('category'));
    define($moduleDirNameUpper . '_CAT_IMAGES_PATH', $context->uploadPath('category'));
    define($moduleDirNameUpper . '_GLASS_IMAGES_URL', $context->uploadUrl('glass'));
    define($moduleDirNameUpper . '_GLASS_IMAGES_PATH', $context->uploadPath('glass'));
    define($moduleDirNameUpper . '_CONSTANTS_DEFINED', 1);
}

$icons = [
    'edit'    => "<img src='" . $pathIcon16 . "/edit.png'  alt='" . _EDIT . "' align='middle'>",
    'delete'  => "<img src='" . $pathIcon16 . "/delete.png' alt='" . _DELETE . "' align='middle'>",
    'clone'   => "<img src='" . $pathIcon16 . "/editcopy.png' alt='" . _CLONE . "' align='middle'>",
    'add'     => "<img src='" . $pathIcon16 . "/add.png' alt='" . _ADD . "' align='middle'>",
    '0'       => "<img src='" . $pathIcon16 . "/0.png' alt='0' align='middle'>",
    '1'       => "<img src='" . $pathIcon16 . "/1.png' alt='1' align='middle'>",
];

$debug = false;
$myts  = \MyTextSanitizer::getInstance();

if (!isset($GLOBALS['xoopsTpl']) || !($GLOBALS['xoopsTpl'] instanceof \XoopsTpl)) {
    require_once $GLOBALS['xoops']->path('class/template.php');
    $GLOBALS['xoopsTpl'] = new \XoopsTpl();
}

$GLOBALS['xoopsTpl']->assign('mod_url', $helper->url());
if (is_object($helper->getModule())) {
    $pathModIcon16 = $helper->getModule()->getInfo('modicons16');
    $pathModIcon32 = $helper->getModule()->getInfo('modicons32');
    $GLOBALS['xoopsTpl']->assign('pathModIcon16', \Xoops\Helpers\Service\Url::module($moduleDirName, (string)$pathModIcon16));
    $GLOBALS['xoopsTpl']->assign('pathModIcon32', $pathModIcon32);
}

if (!\function_exists('cocktails_render_rich_text')) {
    /**
     * Render user/admin supplied HTML safely. Uses HTMLPurifier when available,
     * otherwise falls back to the XOOPS text sanitizer.
     */
    function cocktails_render_rich_text(string $text): string
    {
        $text = \trim($text);
        if ('' === $text) {
            return '';
        }

        if (!\class_exists(\HTMLPurifier::class) && \defined('XOOPS_TRUST_PATH')) {
            $purifierAutoloader = \XOOPS_TRUST_PATH . '/vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php';
            if (\is_file($purifierAutoloader)) {
                require_once $purifierAutoloader;
            }
        }

        if (\class_exists(\HTMLPurifier::class) && \class_exists(\HTMLPurifier_Config::class)) {
            $config = \HTMLPurifier_Config::createDefault();
            $config->set('HTML.Allowed', 'p,br,strong,b,em,i,u,s,blockquote,span[class],ul,ol,li,a[href|title|target|rel],h3,h4');
            $config->set('Attr.AllowedFrameTargets', ['_blank']);
            $config->set('Cache.DefinitionImpl', null);

            return (new \HTMLPurifier($config))->purify($text);
        }

        return \MyTextSanitizer::getInstance()->displayTarea($text, 1, 1, 1, 1, 0);
    }
}

xoops_loadLanguage('main', $moduleDirName);
