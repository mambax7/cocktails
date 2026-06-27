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
 * Class Category — a family of cocktails.
 */
class Category extends \XoopsObject
{
    public $helper;

    public function __construct()
    {
        $this->initVar('id', \XOBJ_DTYPE_INT);
        $this->initVar('pid', \XOBJ_DTYPE_INT);
        $this->initVar('title', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('slug', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('description', \XOBJ_DTYPE_TXTAREA);
        $this->initVar('image', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('color', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('weight', \XOBJ_DTYPE_INT);
        $this->initVar('online', \XOBJ_DTYPE_INT);
    }

    /**
     * @return Form\CategoryForm
     */
    public function getForm()
    {
        return new Form\CategoryForm($this);
    }

    public function url(): string
    {
        return \XOOPS_URL . '/modules/' . \basename(\dirname(__DIR__)) . '/category.php?id=' . (int)$this->getVar('id');
    }
}
