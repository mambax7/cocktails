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

use XoopsModules\Cocktails\Domain\Unit;

/**
 * Class RecipeIngredient — a single measured-ingredient line on a recipe.
 * The book's "MeasuredIngredient" value object, persisted as a child of the recipe aggregate.
 */
class RecipeIngredient extends \XoopsObject
{
    public function __construct()
    {
        $this->initVar('id', \XOBJ_DTYPE_INT);
        $this->initVar('recipe_id', \XOBJ_DTYPE_INT);
        $this->initVar('ingredient_id', \XOBJ_DTYPE_INT);
        $this->initVar('amount', \XOBJ_DTYPE_OTHER);
        $this->initVar('unit', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('note', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('is_optional', \XOBJ_DTYPE_INT);
        $this->initVar('weight', \XOBJ_DTYPE_INT);
    }

    /**
     * Format this line for display, e.g. "1½ fl oz — White rum (freshly chilled)".
     */
    public function display(string $ingredientName): string
    {
        $unit   = Unit::tryFromCode((string)$this->getVar('unit'));
        $amount = (float)$this->getVar('amount');
        $parts  = [];
        $measure = \trim($unit->format($amount));
        if ('' !== $measure) {
            $parts[] = $measure;
        }
        $parts[] = $ingredientName;
        $line    = \implode(' ', $parts);
        $note    = (string)$this->getVar('note');
        if ('' !== $note) {
            $line .= ' (' . $note . ')';
        }

        return $line;
    }
}
