<?php declare(strict_types=1);

namespace XoopsModules\Cocktails\Domain;

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

use XoopsModules\Cocktails\Constants;

/**
 * Difficulty — value object describing how hard a recipe is to make.
 */
final class Difficulty
{
    private const LABELS = [
        Constants::DIFFICULTY_EASY   => ['Easy', 'cocktails-badge-easy'],
        Constants::DIFFICULTY_MEDIUM => ['Medium', 'cocktails-badge-medium'],
        Constants::DIFFICULTY_HARD   => ['Hard', 'cocktails-badge-hard'],
    ];

    private function __construct(private readonly int $level) {}

    public static function fromLevel(int $level): self
    {
        if (!isset(self::LABELS[$level])) {
            $level = Constants::DIFFICULTY_EASY;
        }

        return new self($level);
    }

    public function level(): int
    {
        return $this->level;
    }

    public function label(): string
    {
        return self::LABELS[$this->level][0];
    }

    public function cssClass(): string
    {
        return self::LABELS[$this->level][1];
    }

    /**
     * @return array<int,string> level => label, ready for a XoopsFormSelect.
     */
    public static function options(): array
    {
        $out = [];
        foreach (self::LABELS as $level => [$label]) {
            $out[$level] = $label;
        }

        return $out;
    }
}
