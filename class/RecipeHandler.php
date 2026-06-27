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
use Xmf\Request;
use XoopsModules\Cocktails\Domain\Unit;

/**
 * Class RecipeHandler.
 */
class RecipeHandler extends \XoopsPersistableObjectHandler
{
    public function __construct(?\XoopsDatabase $db = null, public $helper = null)
    {
        parent::__construct($db, 'cocktails_recipe', Recipe::class, 'id', 'title');
    }

    /**
     * @param bool $isNew
     * @return \XoopsObject
     */
    #[\Override]
    public function create($isNew = true)
    {
        $obj         = parent::create($isNew);
        $obj->helper = $this->helper;

        return $obj;
    }

    /**
     * Hydrate a Recipe from the POSTed form and persist it (header fields, child ingredient
     * lines and tags). Shared by the admin and frontend save handlers so the logic lives in
     * ONE place. Callers MUST do authorisation + CSRF BEFORE calling this.
     *
     * @return array{ok: bool, object: \XoopsObject, errors: string}
     */
    public function saveFromRequest(Helper $helper, bool $isAdmin = true): array
    {
        $id     = Request::getInt('id', 0, 'POST');
        $object = $id > 0 ? $this->get($id) : $this->create();
        if (!\is_object($object)) {
            $object = $this->create();
        }
        $isNew = $object->isNew();

        $title = \trim(Request::getString('title', '', 'POST'));
        $object->setVar('title', $title);
        $object->setVar('summary', Request::getString('summary', '', 'POST'));
        $object->setVar('method', Request::getText('method', '', 'POST'));
        $object->setVar('garnish', Request::getString('garnish', '', 'POST'));
        $object->setVar('cid', Request::getInt('cid', 0, 'POST'));
        $object->setVar('glass_id', Request::getInt('glass_id', 0, 'POST'));
        $object->setVar('difficulty', Request::getInt('difficulty', 1, 'POST'));
        $object->setVar('prep_time', Request::getInt('prep_time', 0, 'POST'));
        $object->setVar('servings', \max(1, Request::getInt('servings', 1, 'POST')));
        $object->setVar('is_alcoholic', 1 === Request::getInt('is_alcoholic', 1, 'POST') ? 1 : 0);

        // SEO slug: regenerate from the title, keep it unique, but never steal another row's slug.
        $base = Utility::slugify($title);
        $object->setVar('slug', Utility::uniqueSlug($this, $base, $id));

        // Owner stamp — set once on create, never trusted from POST.
        if ($isNew) {
            $object->setVar('uid', $this->currentUid());
            $object->setVar('created', \time());
            $object->setVar('views', 0);
            $object->setVar('rating_sum', 0);
            $object->setVar('rating_count', 0);
            $object->setVar('rating_avg', 0);
            $object->setVar('favorites_count', 0);
        }

        // Publish state: admins control it; non-admins are subject to the moderation setting and
        // the "auto approve" right. Any non-admin write goes to pending unless allowed.
        $canPublish = $isAdmin || !$helper->getConfig('moderateSubmissions')
                      || (new Permission())->checkPermission(Constants::PERM_GLOBAL, Constants::AC_AUTO_APPROVE);
        if ($isAdmin) {
            $object->setVar('online', 1 === Request::getInt('online', 0, 'POST') ? 1 : 0);
            $object->setVar('featured', 1 === Request::getInt('featured', 0, 'POST') ? 1 : 0);
        } else {
            $object->setVar('online', $canPublish ? 1 : 0);
        }

        $object->setVar('updated', \time());

        $ok = (bool)$this->insert($object, true);
        if ($ok) {
            $recipeId = (int)$object->getVar('id');
            $this->syncIngredientsFromRequest($recipeId);
            /** @var TagHandler $tagHandler */
            $tagHandler = $helper->getHandler('Tag');
            $tagHandler->syncForRecipe($recipeId, Request::getString('tags', '', 'POST'));
        }

        return ['ok' => $ok, 'object' => $object, 'errors' => $ok ? '' : $object->getHtmlErrors()];
    }

    /**
     * Replace a recipe's measured-ingredient lines from the parallel POST arrays produced by
     * the dynamic ingredient editor: ing_id[], ing_amount[], ing_unit[], ing_note[], ing_optional[].
     */
    public function syncIngredientsFromRequest(int $recipeId): void
    {
        /** @var RecipeIngredientHandler $lineHandler */
        $lineHandler = $this->helper->getHandler('RecipeIngredient');
        $lineHandler->deleteByRecipe($recipeId);

        $ids      = Request::getArray('ing_id', [], 'POST');
        $amounts  = Request::getArray('ing_amount', [], 'POST');
        $units    = Request::getArray('ing_unit', [], 'POST');
        $notes    = Request::getArray('ing_note', [], 'POST');
        $optional = Request::getArray('ing_optional', [], 'POST');

        $weight = 0;
        foreach ($ids as $i => $ingId) {
            $ingId = (int)$ingId;
            if ($ingId <= 0) {
                continue;
            }
            $line = $lineHandler->create();
            $line->setVar('recipe_id', $recipeId);
            $line->setVar('ingredient_id', $ingId);
            $line->setVar('amount', (float)($amounts[$i] ?? 0));
            $unitCode = (string)($units[$i] ?? '');
            $line->setVar('unit', Unit::isValidCode($unitCode) ? \strtolower(\trim($unitCode)) : '');
            $line->setVar('note', \trim((string)($notes[$i] ?? '')));
            $line->setVar('is_optional', isset($optional[$i]) && $optional[$i] ? 1 : 0);
            $line->setVar('weight', $weight++);
            $lineHandler->insert($line, true);
        }
    }

    /**
     * Recompute and persist the cached rating aggregate for a recipe.
     */
    public function recalcRating(int $recipeId): void
    {
        /** @var RatingHandler $ratingHandler */
        $ratingHandler = $this->helper->getHandler('Rating');
        [$sum, $count] = $ratingHandler->aggregate($recipeId);

        $recipe = $this->get($recipeId);
        if (!\is_object($recipe)) {
            return;
        }
        $avg = $count > 0 ? \round($sum / $count, 2) : 0;
        $recipe->setVar('rating_sum', $sum);
        $recipe->setVar('rating_count', $count);
        $recipe->setVar('rating_avg', $avg);
        $this->insert($recipe, true);
    }

    /**
     * Increment the view counter without bumping `updated` or touching other fields.
     */
    public function incrementViews(int $recipeId): void
    {
        $table = $this->db->prefix('cocktails_recipe');
        $this->db->queryF('UPDATE `' . $table . '` SET views = views + 1 WHERE id = ' . $recipeId);
    }

    /**
     * Adjust the cached favorites counter by +1 / -1.
     */
    public function adjustFavorites(int $recipeId, int $delta): void
    {
        $table = $this->db->prefix('cocktails_recipe');
        $op    = $delta >= 0 ? 'favorites_count + ' . (int)$delta : 'GREATEST(0, favorites_count - ' . \abs($delta) . ')';
        $this->db->queryF('UPDATE `' . $table . '` SET favorites_count = ' . $op . ' WHERE id = ' . $recipeId);
    }

    /**
     * Delete a recipe AND its child rows (ingredient lines, ratings, favorites, tag links).
     *
     * @param \XoopsObject $object
     * @param bool         $force
     * @return bool
     */
    #[\Override]
    public function delete(\XoopsObject $object, $force = false)
    {
        $recipeId = (int)$object->getVar('id');
        if ($recipeId > 0) {
            $this->helper->getHandler('RecipeIngredient')->deleteByRecipe($recipeId);
            $this->helper->getHandler('Rating')->deleteByRecipe($recipeId);
            $this->helper->getHandler('Favorite')->deleteByRecipe($recipeId);
            $this->helper->getHandler('Tag')->clearForRecipe($recipeId);
        }

        return parent::delete($object, $force);
    }

    private function currentUid(): int
    {
        return ($GLOBALS['xoopsUser'] instanceof \XoopsUser) ? (int)$GLOBALS['xoopsUser']->getVar('uid') : 0;
    }
}
