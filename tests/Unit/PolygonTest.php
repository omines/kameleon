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

        $polygon->setAltitudeModeFromString('clampToGround');
        $this->assertEquals(AltitudeMode::CLAMP_TO_GROUND, $polygon->getAltitudeMode());

        $polygon->setCoordinates([new Coordinate(1, 2, 3)]);
        $this->assertEquals([new Coordinate(1, 2, 3)], $polygon->getCoordinates());

        $coordinate = new Coordinate(4, 5, 6);
        $polygon->addCoordinate($coordinate);
        $this->assertCount(2, $polygon->getCoordinates());

        $polygon->removeCoordinate($coordinate);
        $this->assertCount(1, $polygon->getCoordinates());

        $polygon->setCoordinatesFromString('1,2,3 4,5,6');
        $this->assertCount(2, $polygon->getCoordinates());

        $polygon->setCoordinatesFromString('          1,2,3           ');
        $this->assertCount(1, $polygon->getCoordinates());
    }

    public function testRemovingNonExistentCoordinate(): void
    {
        $polygon = new Polygon();
        $this->expectException(InvalidArgumentException::class);
        $polygon->removeCoordinate(new Coordinate(1, 2, 3));
    }

    public function testSettingInvalidCoordinates(): void
    {
        $polygon = new Polygon();
        $this->expectException(InvalidArgumentException::class);
        $polygon->setCoordinatesFromString('1,2,3 4,5,6,7');
    }

    public function testCreateFromFloatArray(): void
    {
        $polygon = new Polygon();
        $polygon->setCoordinatesFromFloatArray([[1, 2, 3], [4, 5, 6], [7, 8, 9]]);
        $this->assertEquals([
            new Coordinate(1, 2, 3),
            new Coordinate(4, 5, 6),
            new Coordinate(7, 8, 9),
        ], $polygon->getCoordinates());
        $this->assertEquals([[1, 2, 3], [4, 5, 6], [7, 8, 9]], $polygon->getCoordinatesAsFloatArray());
    }

    public function testCreateFromLinearRing(): void
    {
        $polygon = Polygon::buildFromLinearRing(new SimpleXMLElement(<<<EOT
<LinearRing>
    <extrude>1</extrude>
    <tessellate>1</tessellate>
    <coordinates>1,2,3 4,5,6 7,8,9</coordinates>
</LinearRing>
EOT));

        $this->assertTrue($polygon->isExtrude());
        $this->assertTrue($polygon->isTessellate());
        $this->assertEquals(AltitudeMode::CLAMP_TO_GROUND, $polygon->getAltitudeMode());
        $this->assertEquals([new Coordinate(1, 2, 3), new Coordinate(4, 5, 6), new Coordinate(7, 8, 9)], $polygon->getCoordinates());

        $polygon = Polygon::buildFromLinearRing(new SimpleXMLElement(<<<EOT
<LinearRing>
    <extrude>0</extrude>
    <tessellate>0</tessellate>
    <altitudeMode>absolute</altitudeMode>
    <coordinates>1,2,3 4,5,6 7,8,9</coordinates>
</LinearRing>
EOT));

        $this->assertFalse($polygon->isExtrude());
        $this->assertFalse($polygon->isTessellate());
        $this->assertEquals(AltitudeMode::ABSOLUTE, $polygon->getAltitudeMode());
        $this->assertEquals([new Coordinate(1, 2, 3), new Coordinate(4, 5, 6), new Coordinate(7, 8, 9)], $polygon->getCoordinates());
    }
}
