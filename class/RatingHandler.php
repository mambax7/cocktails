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
 * Class RatingHandler.
 */
class RatingHandler extends \XoopsPersistableObjectHandler
{
    public function __construct(?\XoopsDatabase $db = null, public $helper = null)
    {
        parent::__construct($db, 'cocktails_rating', Rating::class, 'id');
    }

    /**
     * Record (or change) a user's rating for a recipe, then refresh the recipe's cached aggregate.
     * One row per (recipe, user) is enforced; re-rating updates the existing row.
     *
     * @return bool true on success
     */
    public function rate(int $recipeId, int $uid, int $stars): bool
    {
        $stars = \max(1, \min(5, $stars));
        if ($recipeId <= 0 || $uid <= 0) {
            return false;
        }

        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('recipe_id', (string)$recipeId));
        $criteria->add(new \Criteria('uid', (string)$uid));
        $existing = $this->getAll($criteria);

        $rating = [] !== $existing ? \reset($existing) : $this->create();
        $rating->setVar('recipe_id', $recipeId);
        $rating->setVar('uid', $uid);
        $rating->setVar('stars', $stars);
        $rating->setVar('created', \time());

        $ok = (bool)$this->insert($rating, true);
        if ($ok) {
            /** @var RecipeHandler $recipeHandler */
            $recipeHandler = $this->helper->getHandler('Recipe');
            $recipeHandler->recalcRating($recipeId);
        }

        return $ok;
    }

    /**
     * The current user's star value for a recipe, or 0 if they have not rated it.
     */
    public function getUserStars(int $recipeId, int $uid): int
    {
        if ($uid <= 0) {
            return 0;
        }
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('recipe_id', (string)$recipeId));
        $criteria->add(new \Criteria('uid', (string)$uid));
        $rows = $this->getAll($criteria);

        return [] !== $rows ? (int)\reset($rows)->getVar('stars') : 0;
    }

    /**
     * @return array{0:int,1:int} [sum of stars, number of ratings]
     */
    public function aggregate(int $recipeId): array
    {
        $table = $this->db->prefix('cocktails_rating');
        $sql   = 'SELECT COALESCE(SUM(stars),0) AS s, COUNT(*) AS c FROM `' . $table . '` WHERE recipe_id = ' . $recipeId;
        $res   = $this->db->query($sql);
        if ($res && ($row = $this->db->fetchArray($res))) {
            return [(int)$row['s'], (int)$row['c']];
        }

        return [0, 0];
    }

    public function deleteByRecipe(int $recipeId): bool
    {
        return (bool)$this->deleteAll(new \Criteria('recipe_id', (string)$recipeId));
    }
}
