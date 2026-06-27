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
 * IngredientType — value object classifying a master-list ingredient.
 */
final class IngredientType
{
    private const LABELS = [
        Constants::ING_SPIRIT  => 'Spirit',
        Constants::ING_LIQUEUR => 'Liqueur',
        Constants::ING_MIXER   => 'Mixer',
        Constants::ING_JUICE   => 'Juice',
        Constants::ING_GARNISH => 'Garnish',
        Constants::ING_OTHER   => 'Other',
    ];

    private function __construct(private readonly int $type) {}

    public static function fromInt(int $type): self
    {
        if (!isset(self::LABELS[$type])) {
            $type = Constants::ING_OTHER;
        }

        return new self($type);
    }

    public function value(): int
    {
        return $this->type;
    }

    public function label(): string
    {
        return self::LABELS[$this->type];
    }

    /**
     * @return array<int,string> type => label, ready for a XoopsFormSelect.
     */
    public static function options(): array
    {
        return self::LABELS;
    }
}
