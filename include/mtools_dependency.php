<?php declare(strict_types=1);

use XoopsModules\Mtools\Module\ConsumerRuntime;

if (!function_exists('cocktails_mtools_dependency_error')) {
    /**
     * Thin, mtools-absence-safe shim. All version-check and message logic lives in
     * {@see ConsumerRuntime}; this only guards the case where mtools is missing
     * entirely (a class inside mtools cannot report its own non-existence).
     */
    function cocktails_mtools_dependency_error(): string
    {
        if (!class_exists(ConsumerRuntime::class)) {
            return 'The mtools module files are missing. Install mtools before installing or running Cocktails.';
        }

        return ConsumerRuntime::dependencyError('1.0.0', '1.2.0');
    }
}
