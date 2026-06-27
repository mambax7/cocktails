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

/**
 * Unit — an immutable value object for a measurement unit.
 *
 * This is the "make it better" version of the book's free-text quantity. The book allowed
 * ml / fl oz / tsp / a plain number; here every unit is a first-class concept that knows its
 * own label and, where it makes sense, its conversion factor to millilitres so a recipe can be
 * normalised, scaled by servings, or re-displayed in the drinker's preferred system.
 *
 * Units with mlFactor === null (dash, piece, slice, leaf, "to top", ...) are intentionally
 * non-convertible: a dash is a gesture, not a volume.
 */
final class Unit
{
    /**
     * Canonical catalogue: code => [label, mlPerUnit|null].
     */
    private const CATALOG = [
        'ml'     => ['ml', 1.0],
        'cl'     => ['cl', 10.0],
        'oz'     => ['fl oz', 29.5735],
        'tsp'    => ['tsp', 4.92892],
        'tbsp'   => ['tbsp', 14.7868],
        'part'   => ['part', null],
        'dash'   => ['dash', 0.92],
        'drop'   => ['drop', 0.05],
        'splash' => ['splash', 3.7],
        'barspoon' => ['bar spoon', 5.0],
        'cup'    => ['cup', 236.588],
        'piece'  => ['piece', null],
        'slice'  => ['slice', null],
        'wedge'  => ['wedge', null],
        'leaf'   => ['leaf/leaves', null],
        'sprig'  => ['sprig', null],
        'cube'   => ['cube', null],
        'scoop'  => ['scoop', null],
        'pinch'  => ['pinch', null],
        'top'    => ['to top', null],
    ];

    private function __construct(private readonly string $code) {}

    /**
     * @throws \InvalidArgumentException for an unknown unit code
     */
    public static function fromCode(string $code): self
    {
        $code = \strtolower(\trim($code));
        if (!isset(self::CATALOG[$code])) {
            throw new \InvalidArgumentException("Unknown cocktail unit: '$code'");
        }

        return new self($code);
    }

    /**
     * Lenient factory for persisted/legacy data: unknown codes degrade to a label-only unit.
     */
    public static function tryFromCode(string $code): self
    {
        $code = \strtolower(\trim($code));

        return new self(isset(self::CATALOG[$code]) ? $code : 'piece');
    }

    public function code(): string
    {
        return $this->code;
    }

    public function label(): string
    {
        return self::CATALOG[$this->code][0];
    }

    public function isConvertible(): bool
    {
        return null !== self::CATALOG[$this->code][1];
    }

    /**
     * Convert an amount expressed in this unit to millilitres, or null if non-convertible.
     */
    public function toMillilitres(float $amount): ?float
    {
        $factor = self::CATALOG[$this->code][1];

        return null === $factor ? null : $amount * $factor;
    }

    /**
     * @return array<string,string> code => label, ready for a XoopsFormSelect.
     */
    public static function options(): array
    {
        $out = [];
        foreach (self::CATALOG as $code => [$label]) {
            $out[$code] = $label;
        }

        return $out;
    }

    public static function isValidCode(string $code): bool
    {
        return isset(self::CATALOG[\strtolower(\trim($code))]);
    }

    /**
     * Format an amount + unit for human display (e.g. 1.5 -> "1½ fl oz", 0.5 -> "½ tsp").
     */
    public function format(float $amount): string
    {
        return self::prettyAmount($amount) . ' ' . $this->label();
    }

    /**
     * Turn a decimal into a friendly bartender fraction.
     */
    public static function prettyAmount(float $amount): string
    {
        if ($amount <= 0.0) {
            return '';
        }
        $whole = (int)\floor($amount);
        $frac  = $amount - $whole;
        // Use value/glyph pairs — float array keys are illegal in PHP and get cast to int.
        $map     = [[0.25, '¼'], [0.33, '⅓'], [0.5, '½'], [0.66, '⅔'], [0.75, '¾']];
        $fracStr = '';
        foreach ($map as [$value, $glyph]) {
            if (\abs($frac - $value) < 0.04) {
                $fracStr = $glyph;
                break;
            }
        }
        if ('' === $fracStr && $frac > 0.0) {
            return \rtrim(\rtrim(\number_format($amount, 2, '.', ''), '0'), '.');
        }
        if (0 === $whole) {
            return $fracStr;
        }

        return '' === $fracStr ? (string)$whole : $whole . $fracStr;
    }
}
