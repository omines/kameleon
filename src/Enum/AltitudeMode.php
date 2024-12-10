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

enum AltitudeMode
{
    case ABSOLUTE;
    case CLAMP_TO_GROUND;
    case RELATIVE_TO_GROUND;

    public static function fromString(string $string): self
    {
        return match (mb_strtolower($string)) {
            'absolute' => self::ABSOLUTE,
            'clamptoground' => self::CLAMP_TO_GROUND,
            'relativetoground' => self::RELATIVE_TO_GROUND,
            default => throw new \InvalidArgumentException("Invalid altitude mode: $string"),
        };
    }
}
