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
 * Class FavoriteHandler.
 */
class FavoriteHandler extends \XoopsPersistableObjectHandler
{
    public function __construct(?\XoopsDatabase $db = null, public $helper = null)
    {
        parent::__construct($db, 'cocktails_favorite', Favorite::class, 'id');
    }

    private function find(int $recipeId, int $uid): ?Favorite
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('recipe_id', (string)$recipeId));
        $criteria->add(new \Criteria('uid', (string)$uid));
        $rows = $this->getAll($criteria);

        return [] !== $rows ? \reset($rows) : null;
    }

    public function isFavorite(int $recipeId, int $uid): bool
    {
        return $uid > 0 && null !== $this->find($recipeId, $uid);
    }

    /**
     * Toggle a favorite on/off and keep the recipe's cached counter in sync.
     *
     * @return bool the new state (true = now a favorite)
     */
    public function toggle(int $recipeId, int $uid): bool
    {
        if ($recipeId <= 0 || $uid <= 0) {
            return false;
        }
        /** @var RecipeHandler $recipeHandler */
        $recipeHandler = $this->helper->getHandler('Recipe');

        $existing = $this->find($recipeId, $uid);
        if (null !== $existing) {
            $this->delete($existing, true);
            $recipeHandler->adjustFavorites($recipeId, -1);

            return false;
        }

        $fav = $this->create();
        $fav->setVar('recipe_id', $recipeId);
        $fav->setVar('uid', $uid);
        $fav->setVar('created', \time());
        $this->insert($fav, true);
        $recipeHandler->adjustFavorites($recipeId, 1);

        return true;
    }

    /**
     * Recipe ids a user has favorited, newest first.
     *
     * @return int[]
     */
    public function recipeIdsForUser(int $uid): array
    {
        if ($uid <= 0) {
            return [];
        }
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('uid', (string)$uid));
        $criteria->setSort('created');
        $criteria->setOrder('DESC');

        $ids = [];
        foreach ($this->getAll($criteria) as $fav) {
            $ids[] = (int)$fav->getVar('recipe_id');
        }

        return $ids;
    }

    public function deleteByRecipe(int $recipeId): bool
    {
        return (bool)$this->deleteAll(new \Criteria('recipe_id', (string)$recipeId));
    }
}
