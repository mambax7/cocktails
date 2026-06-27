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

use Xmf\Module\Helper\Permission;
use XoopsModules\Cocktails\Domain\Difficulty;

/**
 * Class Recipe — the cocktail aggregate root.
 */
class Recipe extends \XoopsObject
{
    public $helper;
    public $permHelper;

    public function __construct()
    {
        $this->permHelper = new Permission();

        $this->initVar('id', \XOBJ_DTYPE_INT);
        $this->initVar('title', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('slug', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('cid', \XOBJ_DTYPE_INT);
        $this->initVar('glass_id', \XOBJ_DTYPE_INT);
        $this->initVar('difficulty', \XOBJ_DTYPE_INT);
        $this->initVar('prep_time', \XOBJ_DTYPE_INT);
        $this->initVar('servings', \XOBJ_DTYPE_INT);
        $this->initVar('is_alcoholic', \XOBJ_DTYPE_INT);
        $this->initVar('summary', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('method', \XOBJ_DTYPE_TXTAREA);
        $this->initVar('garnish', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('image', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('rating_sum', \XOBJ_DTYPE_INT);
        $this->initVar('rating_count', \XOBJ_DTYPE_INT);
        $this->initVar('rating_avg', \XOBJ_DTYPE_OTHER);
        $this->initVar('favorites_count', \XOBJ_DTYPE_INT);
        $this->initVar('views', \XOBJ_DTYPE_INT);
        $this->initVar('featured', \XOBJ_DTYPE_INT);
        $this->initVar('uid', \XOBJ_DTYPE_INT);
        $this->initVar('online', \XOBJ_DTYPE_INT);
        $this->initVar('created', \XOBJ_DTYPE_INT);
        $this->initVar('updated', \XOBJ_DTYPE_INT);
    }

    /**
     * @return Form\RecipeForm
     */
    public function getForm()
    {
        return new Form\RecipeForm($this);
    }

    /**
     * Human-friendly difficulty value object.
     */
    public function difficulty(): Difficulty
    {
        return Difficulty::fromLevel((int)$this->getVar('difficulty'));
    }

    /**
     * Canonical SEO URL for this recipe.
     */
    public function url(): string
    {
        $slug = (string)$this->getVar('slug');
        $id   = (int)$this->getVar('id');

        return \XOOPS_URL . '/modules/' . \basename(\dirname(__DIR__)) . '/recipe.php?op=view&id=' . $id . ('' !== $slug ? '&slug=' . $slug : '');
    }
}
