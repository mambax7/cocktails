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

use Xmf\Request;

/**
 * Class TagHandler. Owns both the tag table and the recipe_tag pivot.
 */
class TagHandler extends \XoopsPersistableObjectHandler
{
    public function __construct(?\XoopsDatabase $db = null, public $helper = null)
    {
        parent::__construct($db, 'cocktails_tag', Tag::class, 'id', 'name');
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
     * Find a tag by name, creating it if it does not exist. Returns the tag id.
     */
    public function firstOrCreate(string $name): int
    {
        $name = \trim($name);
        if ('' === $name) {
            return 0;
        }
        $slug     = Utility::slugify($name);
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('slug', $slug));
        $rows = $this->getAll($criteria);
        if ([] !== $rows) {
            return (int)\reset($rows)->getVar('id');
        }

        $tag = $this->create();
        $tag->setVar('name', $name);
        $tag->setVar('slug', $slug);
        $this->insert($tag, true);

        return (int)$tag->getVar('id');
    }

    /**
     * Replace a recipe's tags from a comma-separated string.
     */
    public function syncForRecipe(int $recipeId, string $commaList): void
    {
        $this->clearForRecipe($recipeId);
        $names = \array_filter(\array_map('trim', \explode(',', $commaList)));
        $pivot = $this->db->prefix('cocktails_recipe_tag');
        $seen  = [];
        foreach ($names as $name) {
            $tagId = $this->firstOrCreate($name);
            if ($tagId > 0 && !isset($seen[$tagId])) {
                $seen[$tagId] = true;
                $this->db->queryF('INSERT INTO `' . $pivot . '` (recipe_id, tag_id) VALUES (' . $recipeId . ', ' . $tagId . ')');
            }
        }
    }

    public function clearForRecipe(int $recipeId): void
    {
        $pivot = $this->db->prefix('cocktails_recipe_tag');
        $this->db->queryF('DELETE FROM `' . $pivot . '` WHERE recipe_id = ' . $recipeId);
    }

    /**
     * Tag rows for a recipe.
     *
     * @return Tag[]
     */
    public function getForRecipe(int $recipeId): array
    {
        $pivot = $this->db->prefix('cocktails_recipe_tag');
        $tags  = $this->db->prefix('cocktails_tag');
        $sql   = 'SELECT t.* FROM `' . $tags . '` t INNER JOIN `' . $pivot . '` p ON p.tag_id = t.id '
               . 'WHERE p.recipe_id = ' . $recipeId . ' ORDER BY t.name';
        $res   = $this->db->query($sql);
        $out   = [];
        if ($res) {
            while ($row = $this->db->fetchArray($res)) {
                $tag = $this->create(false);
                $tag->assignVars($row);
                $out[] = $tag;
            }
        }

        return $out;
    }

    /**
     * Online recipe ids carrying a given tag.
     *
     * @return int[]
     */
    public function recipeIdsForTag(int $tagId): array
    {
        $pivot  = $this->db->prefix('cocktails_recipe_tag');
        $recipe = $this->db->prefix('cocktails_recipe');
        $sql    = 'SELECT p.recipe_id FROM `' . $pivot . '` p INNER JOIN `' . $recipe . '` r ON r.id = p.recipe_id '
                . 'WHERE p.tag_id = ' . $tagId . ' AND r.online = 1';
        $res    = $this->db->query($sql);
        $ids    = [];
        if ($res) {
            while ($row = $this->db->fetchArray($res)) {
                $ids[] = (int)$row['recipe_id'];
            }
        }

        return $ids;
    }

    /**
     * @return array{ok: bool, object: \XoopsObject, errors: string}
     */
    public function saveFromRequest(Helper $helper): array
    {
        $id     = Request::getInt('id', 0, 'POST');
        $object = $id > 0 ? $this->get($id) : $this->create();
        if (!\is_object($object)) {
            $object = $this->create();
        }
        $name = \trim(Request::getString('name', '', 'POST'));
        $object->setVar('name', $name);
        $object->setVar('slug', Utility::uniqueSlug($this, Utility::slugify($name), $id));

        $ok = (bool)$this->insert($object, true);

        return ['ok' => $ok, 'object' => $object, 'errors' => $ok ? '' : $object->getHtmlErrors()];
    }
}
