<?php declare(strict_types=1);

namespace XoopsModules\Cocktails\Form;

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

require_once \dirname(__DIR__, 2) . '/include/common.php';

\xoops_load('XoopsFormLoader');

/**
 * Class IngredientForm.
 */
class IngredientForm extends \XoopsThemeForm
{
    public $targetObject;
    public $helper;

    public function __construct($target)
    {
        $this->helper       = $target->helper;
        $this->targetObject = $target;

        $title = $this->targetObject->isNew() ? \_AM_COCKTAILS_INGREDIENT_ADD : \_AM_COCKTAILS_INGREDIENT_EDIT;
        parent::__construct($title, 'ingredientform', \xoops_getenv('SCRIPT_NAME'), 'post', true);
        $this->setExtra('enctype="multipart/form-data"');

        $this->addElement(new \XoopsFormHidden('id', $this->targetObject->getVar('id')));
        $this->addElement(new \XoopsFormText(\_AM_COCKTAILS_INGREDIENT_NAME, 'name', 50, 100, $this->targetObject->getVar('name', 'e')), true);

        $typeSelect = new \XoopsFormSelect(\_AM_COCKTAILS_INGREDIENT_TYPE, 'type', $this->targetObject->getVar('type') ?: 6);
        $typeSelect->addOptionArray(IngredientType::options());
        $this->addElement($typeSelect);

        $this->addElement(new \XoopsFormText(\_AM_COCKTAILS_INGREDIENT_ABV, 'abv', 6, 6, (string)$this->targetObject->getVar('abv')));
        $this->addElement(new \XoopsFormTextArea(\_AM_COCKTAILS_INGREDIENT_DESC, 'description', $this->targetObject->getVar('description', 'e'), 4, 50));
        $this->addElement(new \XoopsFormText(\_AM_COCKTAILS_INGREDIENT_WEIGHT, 'weight', 5, 5, (string)$this->targetObject->getVar('weight')));

        $online = $this->targetObject->isNew() ? 1 : (int)$this->targetObject->getVar('online');
        $this->addElement(new \XoopsFormRadioYN(\_AM_COCKTAILS_INGREDIENT_ONLINE, 'online', $online));

        $this->addElement(new \XoopsFormHidden('op', 'save'));
        $this->addElement(new \XoopsFormButtonTray('submit', \_SUBMIT, 'submit', '', false));
    }
}
