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

require_once \dirname(__DIR__, 2) . '/include/common.php';

\xoops_load('XoopsFormLoader');

/**
 * Class CategoryForm.
 */
class CategoryForm extends \XoopsThemeForm
{
    public $targetObject;
    public $helper;

    public function __construct($target)
    {
        $this->helper       = $target->helper;
        $this->targetObject = $target;

        $title = $this->targetObject->isNew() ? \_AM_COCKTAILS_CATEGORY_ADD : \_AM_COCKTAILS_CATEGORY_EDIT;
        parent::__construct($title, 'categoryform', \xoops_getenv('SCRIPT_NAME'), 'post', true);
        $this->setExtra('enctype="multipart/form-data"');

        $this->addElement(new \XoopsFormHidden('id', $this->targetObject->getVar('id')));
        $this->addElement(new \XoopsFormText(\_AM_COCKTAILS_CATEGORY_TITLE, 'title', 50, 255, $this->targetObject->getVar('title', 'e')), true);

        /** @var \XoopsModules\Cocktails\CategoryHandler $categoryHandler */
        $categoryHandler = $this->helper->getHandler('Category');
        $parentSelect    = new \XoopsFormSelect(\_AM_COCKTAILS_CATEGORY_PID, 'pid', $this->targetObject->getVar('pid'));
        $parentSelect->addOption(0, \_AM_COCKTAILS_CATEGORY_NONE);
        foreach ($categoryHandler->getSelectList() as $cid => $ctitle) {
            if ($cid !== (int)$this->targetObject->getVar('id')) {
                $parentSelect->addOption($cid, $ctitle);
            }
        }
        $this->addElement($parentSelect);

        $this->addElement(new \XoopsFormTextArea(\_AM_COCKTAILS_CATEGORY_DESC, 'description', $this->targetObject->getVar('description', 'e'), 4, 50));
        $colorEl = new \XoopsFormText(\_AM_COCKTAILS_CATEGORY_COLOR, 'color', 10, 10, $this->targetObject->getVar('color', 'e'));
        $colorEl->setExtra('placeholder="#7b2ff7"');
        $this->addElement($colorEl);
        $this->addElement(new \XoopsFormText(\_AM_COCKTAILS_CATEGORY_WEIGHT, 'weight', 5, 5, (string)$this->targetObject->getVar('weight')));

        $online = $this->targetObject->isNew() ? 1 : (int)$this->targetObject->getVar('online');
        $this->addElement(new \XoopsFormRadioYN(\_AM_COCKTAILS_CATEGORY_ONLINE, 'online', $online));

        $this->addElement(new \XoopsFormHidden('op', 'save'));
        $this->addElement(new \XoopsFormButtonTray('submit', \_SUBMIT, 'submit', '', false));
    }
}
