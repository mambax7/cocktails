<?php declare(strict_types=1);

namespace XoopsModules\Cocktails;

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

/**
 * Central place for cocktails module constant values.
 */
interface Constants
{
    // Difficulty levels
    public const DIFFICULTY_EASY   = 1;
    public const DIFFICULTY_MEDIUM = 2;
    public const DIFFICULTY_HARD   = 3;

    // Ingredient types
    public const ING_SPIRIT  = 1;
    public const ING_LIQUEUR = 2;
    public const ING_MIXER   = 3;
    public const ING_JUICE   = 4;
    public const ING_GARNISH = 5;
    public const ING_OTHER   = 6;

    // Permission group names (Xmf permissions, gperm_name)
    public const PERM_ITEM_READ = 'cocktails_read';   // per-category read
    public const PERM_GLOBAL    = 'cocktails_ac';     // global access rights

    // Global access right ids (used with PERM_GLOBAL)
    public const AC_SUBMIT       = 8;   // submit from user side
    public const AC_AUTO_APPROVE = 16;  // auto-approve own submissions
    public const AC_EDIT_OWN     = 32;  // edit own recipes
    public const AC_RATE         = 64;  // rate recipes
}
