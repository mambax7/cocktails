<?php declare(strict_types=1);

/**
 * Cocktails module bootstrap.
 *
 * Registers our PSR-4 autoloader, then loads the public mtools bootstrap so we can
 * consume the shared XoopsModules\Mtools\* classes without reaching into mtools internals.
 */

require_once __DIR__ . '/preloads/autoloader.php';

if (defined('XOOPS_ROOT_PATH')) {
    $mtoolsBootstrap = XOOPS_ROOT_PATH . '/modules/mtools/bootstrap.php';
    if (is_file($mtoolsBootstrap)) {
        require_once $mtoolsBootstrap;
    }
}

require_once __DIR__ . '/include/mtools_dependency.php';
