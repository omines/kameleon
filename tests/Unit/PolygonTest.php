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
use Omines\Kameleon\Model\Coordinate;
use Omines\Kameleon\Model\Polygon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Polygon::class)]
class PolygonTest extends TestCase
{
    public function testDefaults(): void
    {
        $polygon = new Polygon();
        $this->assertFalse($polygon->isExtrude());
        $this->assertFalse($polygon->isTessellate());
        $this->assertEquals(AltitudeMode::CLAMP_TO_GROUND, $polygon->getAltitudeMode());
    }

    public function testAccessors(): void
    {
        $polygon = new Polygon();

        $polygon->setExtrude(true);
        $this->assertTrue($polygon->isExtrude());

        $polygon->setTessellate(true);
        $this->assertTrue($polygon->isTessellate());

        $polygon->setAltitudeMode(AltitudeMode::RELATIVE_TO_GROUND);
        $this->assertEquals(AltitudeMode::RELATIVE_TO_GROUND, $polygon->getAltitudeMode());

        $polygon->setCoordinates([new Coordinate(1, 2, 3)]);
        $this->assertEquals([new Coordinate(1, 2, 3)], $polygon->getCoordinates());

        $coordinate = new Coordinate(4, 5, 6);
        $polygon->addCoordinate($coordinate);
        $this->assertCount(2, $polygon->getCoordinates());

        $polygon->removeCoordinate($coordinate);
        $this->assertCount(1, $polygon->getCoordinates());

        $this->expectException(InvalidArgumentException::class);
        $polygon->removeCoordinate(new Coordinate(1, 2, 3));

        $polygon->setCoordinatesFromString('1,2,3 4,5,6');
        $this->assertCount(2, $polygon->getCoordinates());

        $this->expectException(InvalidArgumentException::class);
        $polygon->setCoordinatesFromString('1,2,3 4,5');
    }
}
