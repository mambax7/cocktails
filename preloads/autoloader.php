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
// PSR-4 autoloader for XoopsModules\Cocktails\*  @see https://www.php-fig.org/psr/psr-4/

spl_autoload_register(static function ($class): void {
    // project-specific namespace prefix
    $prefix = 'XoopsModules\\' . ucfirst(basename(dirname(__DIR__)));

    // base directory for the namespace prefix
    $baseDir = \dirname(__DIR__) . '/class/';

    // does the class use the namespace prefix?
    $len = mb_strlen($prefix);

    if (0 !== strncmp($prefix, $class, $len)) {
        return;
    }

    // get the relative class name
    $relativeClass = mb_substr($class, $len);

    // replace the namespace separators with directory separators, append .php
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});
