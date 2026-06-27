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

use XoopsModules\Cocktails\Domain\Difficulty;
use XoopsModules\Cocktails\Domain\Unit;

require_once \dirname(__DIR__, 2) . '/include/common.php';

\xoops_load('XoopsFormLoader');

/**
 * Class RecipeForm.
 *
 * The headline feature is the dynamic measured-ingredient editor: a server-rendered table of
 * rows (each = one MeasuredIngredient) that the bundled JS lets the user add to / remove from.
 */
class RecipeForm extends \XoopsThemeForm
{
    public $targetObject;
    public $helper;

    public function __construct($target)
    {
        $this->helper       = $target->helper;
        $this->targetObject = $target;

        $title = $this->targetObject->isNew() ? \_AM_COCKTAILS_RECIPE_ADD : \_AM_COCKTAILS_RECIPE_EDIT;
        parent::__construct($title, 'recipeform', \xoops_getenv('SCRIPT_NAME'), 'post', true);
        $this->setExtra('enctype="multipart/form-data"');

        $this->addElement(new \XoopsFormHidden('id', $this->targetObject->getVar('id')));

        // Title
        $this->addElement(new \XoopsFormText(\_AM_COCKTAILS_RECIPE_TITLE, 'title', 50, 255, $this->targetObject->getVar('title', 'e')), true);

        // Summary
        $this->addElement(new \XoopsFormText(\_AM_COCKTAILS_RECIPE_SUMMARY, 'summary', 60, 500, $this->targetObject->getVar('summary', 'e')));

        // Category
        /** @var \XoopsModules\Cocktails\CategoryHandler $categoryHandler */
        $categoryHandler = $this->helper->getHandler('Category');
        $catSelect       = new \XoopsFormSelect(\_AM_COCKTAILS_RECIPE_CID, 'cid', $this->targetObject->getVar('cid'));
        $catSelect->addOption(0, '-------------');
        $catSelect->addOptionArray($categoryHandler->getSelectList());
        $this->addElement($catSelect);

        // Glass
        /** @var \XoopsModules\Cocktails\GlassHandler $glassHandler */
        $glassHandler = $this->helper->getHandler('Glass');
        $glassSelect  = new \XoopsFormSelect(\_AM_COCKTAILS_RECIPE_GLASS, 'glass_id', $this->targetObject->getVar('glass_id'));
        $glassSelect->addOption(0, '-------------');
        $glassSelect->addOptionArray($glassHandler->getSelectList());
        $this->addElement($glassSelect);

        // Difficulty
        $diffSelect = new \XoopsFormSelect(\_AM_COCKTAILS_RECIPE_DIFFICULTY, 'difficulty', $this->targetObject->getVar('difficulty') ?: 1);
        $diffSelect->addOptionArray(Difficulty::options());
        $this->addElement($diffSelect);

        // Prep time + servings
        $this->addElement(new \XoopsFormText(\_AM_COCKTAILS_RECIPE_PREPTIME, 'prep_time', 6, 6, (string)$this->targetObject->getVar('prep_time')));
        $this->addElement(new \XoopsFormText(\_AM_COCKTAILS_RECIPE_SERVINGS, 'servings', 4, 4, (string)($this->targetObject->getVar('servings') ?: 1)));

        // Alcoholic?
        $alc = $this->targetObject->isNew() ? 1 : (int)$this->targetObject->getVar('is_alcoholic');
        $this->addElement(new \XoopsFormRadioYN(\_AM_COCKTAILS_RECIPE_ALCOHOLIC, 'is_alcoholic', $alc));

        // Measured ingredients editor (custom widget)
        $editor = new \XoopsFormLabel(\_AM_COCKTAILS_INGLINES, $this->buildIngredientEditor());
        $editor->setDescription(\_AM_COCKTAILS_INGLINES_DESC);
        $this->addElement($editor);

        // Method (rich editor)
        $editorConfig = [
            'name'   => 'method',
            'value'  => $this->targetObject->getVar('method', 'e'),
            'rows'   => 8,
            'cols'   => 40,
            'width'  => '100%',
            'height' => '320px',
        ];
        if (\class_exists('XoopsFormEditor')) {
            $which  = $this->helper->isUserAdmin() ? 'cocktailsEditorAdmin' : 'cocktailsEditorUser';
            $method = new \XoopsFormEditor(\_AM_COCKTAILS_RECIPE_METHOD, $this->helper->getConfig($which), $editorConfig, false, 'textarea');
        } else {
            $method = new \XoopsFormDhtmlTextArea(\_AM_COCKTAILS_RECIPE_METHOD, 'method', $this->targetObject->getVar('method', 'e'), 8, 50);
        }
        $this->addElement($method);

        // Garnish
        $this->addElement(new \XoopsFormText(\_AM_COCKTAILS_RECIPE_GARNISH, 'garnish', 50, 255, $this->targetObject->getVar('garnish', 'e')));

        // Image
        $imageTray = new \XoopsFormElementTray(\_AM_COCKTAILS_RECIPE_IMAGE, '<br>');
        $currentImage = (string)$this->targetObject->getVar('image');
        if ('' !== $currentImage && \defined('COCKTAILS_RECIPE_IMAGES_URL')) {
            $imageTray->addElement(new \XoopsFormLabel('', "<img src='" . \COCKTAILS_RECIPE_IMAGES_URL . '/' . $currentImage . "' alt='' style='max-width:160px;border-radius:8px;'>"));
            $imageTray->addElement(new \XoopsFormHidden('image_current', $currentImage));
        }
        $imageTray->addElement(new \XoopsFormFile('', 'image', (int)$this->helper->getConfig('maxsize')));
        $this->addElement($imageTray);

        // Tags
        $tagHandler = $this->helper->getHandler('Tag');
        $tagNames   = [];
        foreach ($tagHandler->getForRecipe((int)$this->targetObject->getVar('id')) as $tag) {
            $tagNames[] = $tag->getVar('name');
        }
        $tagText = new \XoopsFormText(\_AM_COCKTAILS_RECIPE_TAGS, 'tags', 60, 255, \implode(', ', $tagNames));
        $tagText->setDescription(\_AM_COCKTAILS_RECIPE_TAGS_DESC);
        $this->addElement($tagText);

        // Admin-only: publish + featured.
        if ($this->helper->isUserAdmin()) {
            $online = $this->targetObject->isNew() ? 1 : (int)$this->targetObject->getVar('online');
            $this->addElement(new \XoopsFormRadioYN(\_AM_COCKTAILS_RECIPE_ONLINE, 'online', $online));
            $this->addElement(new \XoopsFormRadioYN(\_AM_COCKTAILS_RECIPE_FEATURED, 'featured', (int)$this->targetObject->getVar('featured')));
        }

        $this->addElement(new \XoopsFormHidden('op', 'save'));
        $this->addElement(new \XoopsFormButtonTray('submit', \_SUBMIT, 'submit', '', false));
    }

    /**
     * Build the HTML for the measured-ingredient editor. Existing lines are rendered as rows;
     * a hidden <template> row is used by assets/js/cocktails.js to add new rows on the fly.
     */
    private function buildIngredientEditor(): string
    {
        /** @var \XoopsModules\Cocktails\IngredientHandler $ingredientHandler */
        $ingredientHandler = $this->helper->getHandler('Ingredient');
        $ingredients       = $ingredientHandler->getSelectList(true);
        $units             = Unit::options();

        /** @var \XoopsModules\Cocktails\RecipeIngredientHandler $lineHandler */
        $lineHandler = $this->helper->getHandler('RecipeIngredient');
        $lines       = $lineHandler->getByRecipe((int)$this->targetObject->getVar('id'));

        $ingOptions = static function (int $selected) use ($ingredients): string {
            $html = '<option value="0">' . \htmlspecialchars(\_AM_COCKTAILS_ING_PICK, \ENT_QUOTES) . '</option>';
            foreach ($ingredients as $iid => $iname) {
                $sel  = $iid === $selected ? ' selected' : '';
                $html .= '<option value="' . $iid . '"' . $sel . '>' . \htmlspecialchars((string)$iname, \ENT_QUOTES) . '</option>';
            }

            return $html;
        };
        $unitOptions = static function (string $selected) use ($units): string {
            $html = '';
            foreach ($units as $code => $label) {
                $sel  = $code === $selected ? ' selected' : '';
                $html .= '<option value="' . \htmlspecialchars($code, \ENT_QUOTES) . '"' . $sel . '>' . \htmlspecialchars($label, \ENT_QUOTES) . '</option>';
            }

            return $html;
        };

        $row = static function (string $idSel, string $amount, string $unitSel, string $note, bool $optional) use ($ingOptions, $unitOptions): string {
            return '<tr class="cocktails-ing-row">'
                 . '<td><select name="ing_id[]" class="cocktails-ing-select">' . $idSel . '</select></td>'
                 . '<td><input type="text" name="ing_amount[]" size="5" value="' . \htmlspecialchars($amount, \ENT_QUOTES) . '" placeholder="' . \htmlspecialchars(\_AM_COCKTAILS_ING_AMOUNT, \ENT_QUOTES) . '"></td>'
                 . '<td><select name="ing_unit[]">' . $unitSel . '</select></td>'
                 . '<td><input type="text" name="ing_note[]" size="18" value="' . \htmlspecialchars($note, \ENT_QUOTES) . '" placeholder="' . \htmlspecialchars(\_AM_COCKTAILS_ING_NOTE, \ENT_QUOTES) . '"></td>'
                 . '<td class="center"><input type="checkbox" name="ing_optional[]" value="1"' . ($optional ? ' checked' : '') . '></td>'
                 . '<td class="center"><button type="button" class="cocktails-ing-remove btn btn-sm">&times;</button></td>'
                 . '</tr>';
        };

        $body = '';
        foreach ($lines as $line) {
            $body .= $row(
                $ingOptions((int)$line->getVar('ingredient_id')),
                \rtrim(\rtrim(\number_format((float)$line->getVar('amount'), 2, '.', ''), '0'), '.'),
                $unitOptions((string)$line->getVar('unit')),
                (string)$line->getVar('note'),
                (bool)$line->getVar('is_optional')
            );
        }
        // Ensure at least one editable row when adding.
        if ('' === $body) {
            $body = $row($ingOptions(0), '', $unitOptions('ml'), '', false);
        }

        $templateRow = $row($ingOptions(0), '', $unitOptions('ml'), '', false);

        $html  = '<div id="cocktails-ing-editor" class="cocktails-ing-editor">';
        $html .= '<table class="cocktails-ing-table" style="width:100%">';
        $html .= '<thead><tr>'
               . '<th>' . \htmlspecialchars(\_AM_COCKTAILS_ING_PICK, \ENT_QUOTES) . '</th>'
               . '<th>' . \htmlspecialchars(\_AM_COCKTAILS_ING_AMOUNT, \ENT_QUOTES) . '</th>'
               . '<th>' . \htmlspecialchars(\_AM_COCKTAILS_ING_UNIT, \ENT_QUOTES) . '</th>'
               . '<th>' . \htmlspecialchars(\_AM_COCKTAILS_ING_NOTE, \ENT_QUOTES) . '</th>'
               . '<th>' . \htmlspecialchars(\_AM_COCKTAILS_ING_OPTIONAL, \ENT_QUOTES) . '</th>'
               . '<th></th></tr></thead>';
        $html .= '<tbody class="cocktails-ing-body">' . $body . '</tbody>';
        $html .= '</table>';
        $html .= '<button type="button" id="cocktails-ing-add" class="btn btn-secondary">+ ' . \htmlspecialchars(\_AM_COCKTAILS_ING_ADDROW, \ENT_QUOTES) . '</button>';
        $html .= '<template id="cocktails-ing-template">' . $templateRow . '</template>';
        $html .= '</div>';
        $html .= '<script>(function(){var s=document.createElement("script");s.src="' . \XOOPS_URL . '/modules/cocktails/assets/js/cocktails.js";document.body.appendChild(s);})();</script>';

        return $html;
    }
}
