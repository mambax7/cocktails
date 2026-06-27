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
 * Class IngredientHandler.
 */
class IngredientHandler extends \XoopsPersistableObjectHandler
{
    public function __construct(?\XoopsDatabase $db = null, public $helper = null)
    {
        parent::__construct($db, 'cocktails_ingredient', Ingredient::class, 'id', 'name');
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
     * id => name list for select boxes (online ingredients, sorted by weight then name).
     *
     * @return array<int,string>
     */
    public function getSelectList(bool $onlineOnly = true): array
    {
        $criteria = new \CriteriaCompo();
        if ($onlineOnly) {
            $criteria->add(new \Criteria('online', 1));
        }
        $criteria->setSort('weight ASC, name');
        $criteria->setOrder('ASC');

        $out = [];
        foreach ($this->getAll($criteria) as $ing) {
            $out[(int)$ing->getVar('id')] = $ing->getVar('name');
        }

        return $out;
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
        $object->setVar('type', Request::getInt('type', 6, 'POST'));
        $object->setVar('abv', (float)Request::getString('abv', '0', 'POST'));
        $object->setVar('description', Request::getText('description', '', 'POST'));
        $object->setVar('weight', Request::getInt('weight', 0, 'POST'));
        $object->setVar('online', 1 === Request::getInt('online', 1, 'POST') ? 1 : 0);

        $ok = (bool)$this->insert($object, true);

        return ['ok' => $ok, 'object' => $object, 'errors' => $ok ? '' : $object->getHtmlErrors()];
    }
}
