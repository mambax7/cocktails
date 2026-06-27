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
 * Class TagForm.
 */
class TagForm extends \XoopsThemeForm
{
    public $targetObject;
    public $helper;

    public function __construct($target)
    {
        $this->helper       = $target->helper;
        $this->targetObject = $target;

        $title = $this->targetObject->isNew() ? \_AM_COCKTAILS_TAG_ADD : \_AM_COCKTAILS_TAG_EDIT;
        parent::__construct($title, 'tagform', \xoops_getenv('SCRIPT_NAME'), 'post', true);

        $this->addElement(new \XoopsFormHidden('id', $this->targetObject->getVar('id')));
        $this->addElement(new \XoopsFormText(\_AM_COCKTAILS_TAG_NAME, 'name', 50, 50, $this->targetObject->getVar('name', 'e')), true);

        $this->addElement(new \XoopsFormHidden('op', 'save'));
        $this->addElement(new \XoopsFormButtonTray('submit', \_SUBMIT, 'submit', '', false));
    }
}
