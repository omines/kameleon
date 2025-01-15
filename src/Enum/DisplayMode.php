<?php

/*
 * KaMeLeon - KML and KMZ reader/writer
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Omines\Kameleon\Enum;

enum DisplayMode: string
{
    case DEFAULT = 'default';
    case HIDE = 'hide';

    public static function fromString(string $string): self
    {
        return match (mb_strtolower($string)) {
            mb_strtolower(self::DEFAULT->value) => self::DEFAULT,
            mb_strtolower(self::HIDE->value) => self::HIDE,
            default => throw new \InvalidArgumentException("Invalid display mode: $string"),
        };
    }
}
