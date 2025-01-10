<?php

/*
 * KaMeLeon - KML and KMZ reader/writer
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Omines\Kameleon\Enum\AltitudeMode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AltitudeMode::class)]
class EnumTest extends TestCase
{
    public function testAltitudeModes(): void
    {
        $this->assertEquals(AltitudeMode::CLAMP_TO_GROUND, AltitudeMode::fromString('clampToGround'));
        $this->assertEquals(AltitudeMode::RELATIVE_TO_GROUND, AltitudeMode::fromString('relativeToGround'));
        $this->assertEquals(AltitudeMode::ABSOLUTE, AltitudeMode::fromString('absolute'));
        $this->expectException(InvalidArgumentException::class);
        AltitudeMode::fromString('invalid');
    }
}
