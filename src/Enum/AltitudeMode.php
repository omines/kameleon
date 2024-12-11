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

enum AltitudeMode: string
{
    case ABSOLUTE = 'absolute';
    case CLAMP_TO_GROUND = 'clampToGround';
    case RELATIVE_TO_GROUND = 'relativeToGround';

    public static function fromString(string $string): self
    {
        return match (mb_strtolower($string)) {
            mb_strtolower(self::ABSOLUTE->value) => self::ABSOLUTE,
            mb_strtolower(self::CLAMP_TO_GROUND->value) => self::CLAMP_TO_GROUND,
            mb_strtolower(self::RELATIVE_TO_GROUND->value) => self::RELATIVE_TO_GROUND,
            default => throw new \InvalidArgumentException("Invalid altitude mode: $string"),
        };
    }
}
