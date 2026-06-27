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

use XoopsModules\Mtools;

/**
 * Class Utility
 *
 * Inherits the full shared toolbox from mtools (truncateHtml, selectSorting,
 * getServerStats, checkVerXoops, checkVerPhp, cloneRecord, prepareFolder, ...).
 * Only cocktails-specific helpers live here.
 */
class Utility extends Mtools\Common\SysUtility
{
    //--------------- Custom module methods -----------------------------

    /**
     * Build a URL/SEO friendly slug from an arbitrary string.
     *
     * Transliterates, lowercases, strips anything that is not a-z/0-9 and collapses
     * separators to single hyphens. Falls back to 'item' so we never emit an empty slug.
     */
    public static function slugify(string $text): string
    {
        $text = \trim($text);
        if (\function_exists('transliterator_transliterate')) {
            $text = (string)\transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $text);
        } else {
            $text = \mb_strtolower($text);
        }
        $text = \preg_replace('~[^\pL\d]+~u', '-', $text) ?? '';
        $text = \preg_replace('~[^-\w]+~', '', $text) ?? '';
        $text = \trim($text, '-');
        $text = \preg_replace('~-+~', '-', $text) ?? '';

        return '' === $text ? 'item' : $text;
    }

    /**
     * Guarantee a slug is unique within a table by appending -2, -3, ... when needed.
     * $ignoreId lets an edited row keep its own slug.
     */
    public static function uniqueSlug(\XoopsPersistableObjectHandler $handler, string $base, int $ignoreId = 0): string
    {
        $slug   = $base;
        $suffix = 1;
        while (true) {
            $criteria = new \CriteriaCompo();
            $criteria->add(new \Criteria('slug', $slug));
            if ($ignoreId > 0) {
                $criteria->add(new \Criteria('id', (string)$ignoreId, '<>'));
            }
            if (0 === $handler->getCount($criteria)) {
                return $slug;
            }
            ++$suffix;
            $slug = $base . '-' . $suffix;
        }
    }

    /**
     * Render a 0-5 (decimal) average as accessible star markup for templates.
     */
    public static function renderStars(float $avg): string
    {
        $full  = (int)\floor($avg);
        $half  = ($avg - $full) >= 0.5 ? 1 : 0;
        $empty = 5 - $full - $half;
        $out   = '<span class="cocktails-stars" aria-label="' . \round($avg, 1) . ' / 5">';
        $out  .= \str_repeat('<i class="cocktails-star is-full">&#9733;</i>', $full);
        $out  .= \str_repeat('<i class="cocktails-star is-half">&#9733;</i>', $half);
        $out  .= \str_repeat('<i class="cocktails-star is-empty">&#9734;</i>', \max(0, $empty));
        $out  .= '</span>';

        return $out;
    }
}
