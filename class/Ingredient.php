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

use XoopsModules\Cocktails\Domain\IngredientType;

/**
 * Class Ingredient — an entry in the admin-curated master ingredient list.
 */
class Ingredient extends \XoopsObject
{
    public $helper;

    public function __construct()
    {
        $this->initVar('id', \XOBJ_DTYPE_INT);
        $this->initVar('name', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('slug', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('type', \XOBJ_DTYPE_INT);
        $this->initVar('abv', \XOBJ_DTYPE_OTHER);
        $this->initVar('description', \XOBJ_DTYPE_TXTAREA);
        $this->initVar('image', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('weight', \XOBJ_DTYPE_INT);
        $this->initVar('online', \XOBJ_DTYPE_INT);
    }

    /**
     * @return Form\IngredientForm
     */
    public function getForm()
    {
        return new Form\IngredientForm($this);
    }

    public function type(): IngredientType
    {
        return IngredientType::fromInt((int)$this->getVar('type'));
    }
}
