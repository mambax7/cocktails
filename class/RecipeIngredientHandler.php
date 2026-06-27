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
 * Class RecipeIngredientHandler.
 */
class RecipeIngredientHandler extends \XoopsPersistableObjectHandler
{
    public function __construct(?\XoopsDatabase $db = null, public $helper = null)
    {
        parent::__construct($db, 'cocktails_recipe_ingredient', RecipeIngredient::class, 'id');
    }

    /**
     * Ordered measured-ingredient lines for a recipe.
     *
     * @return RecipeIngredient[]
     */
    public function getByRecipe(int $recipeId): array
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('recipe_id', (string)$recipeId));
        $criteria->setSort('weight');
        $criteria->setOrder('ASC');

        return \array_values($this->getAll($criteria));
    }

    public function deleteByRecipe(int $recipeId): bool
    {
        return (bool)$this->deleteAll(new \Criteria('recipe_id', (string)$recipeId));
    }

    /**
     * Count distinct recipes that use a given master ingredient (for "browse by ingredient").
     */
    public function countRecipesForIngredient(int $ingredientId): int
    {
        $table = $this->db->prefix('cocktails_recipe_ingredient');
        $sql   = 'SELECT COUNT(DISTINCT recipe_id) AS c FROM `' . $table . '` WHERE ingredient_id = ' . $ingredientId;
        $res   = $this->db->query($sql);
        if ($res && ($row = $this->db->fetchArray($res))) {
            return (int)$row['c'];
        }

        return 0;
    }

    /**
     * Online recipe ids that use a given ingredient.
     *
     * @return int[]
     */
    public function recipeIdsForIngredient(int $ingredientId): array
    {
        $ri      = $this->db->prefix('cocktails_recipe_ingredient');
        $recipe  = $this->db->prefix('cocktails_recipe');
        $sql     = 'SELECT DISTINCT ri.recipe_id FROM `' . $ri . '` ri '
                 . 'INNER JOIN `' . $recipe . '` r ON r.id = ri.recipe_id '
                 . 'WHERE ri.ingredient_id = ' . $ingredientId . ' AND r.online = 1';
        $res     = $this->db->query($sql);
        $ids     = [];
        if ($res) {
            while ($row = $this->db->fetchArray($res)) {
                $ids[] = (int)$row['recipe_id'];
            }
        }

        return $ids;
    }
}
