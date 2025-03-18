<?php

/*
 * KaMeLeon - KML and KMZ reader/writer
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Omines\Kameleon\Model\Coordinate;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Coordinate::class)]
class CoordinateTest extends TestCase
{
    public function testDefaults(): void
    {
        $coordinate = new Coordinate(1, 2, 3);
        $this->assertEquals(1, $coordinate->getLongitude());
        $this->assertEquals(2, $coordinate->getLatitude());
        $this->assertEquals(3, $coordinate->getAltitude());
    }

    public function testAccessors(): void
    {
        $coordinate = new Coordinate(1, 2, 3);

        $coordinate->setLatitude(4);
        $this->assertEquals(4, $coordinate->getLatitude());

        $coordinate->setLongitude(5);
        $this->assertEquals(5, $coordinate->getLongitude());

        $coordinate->setAltitude(6);
        $this->assertEquals(6, $coordinate->getAltitude());
    }

    public function testCreateCoordinateFromStringWithTwoValues(): void
    {
        $coordinate = Coordinate::fromString('1,2');
        $this->assertEquals(1, $coordinate->getLongitude());
        $this->assertEquals(2, $coordinate->getLatitude());
        $this->assertEquals(0, $coordinate->getAltitude());
    }

    public function testCreateCoordinateFromStringWithThreeValues(): void
    {
        $coordinate = Coordinate::fromString('1,2,3');
        $this->assertEquals(1, $coordinate->getLongitude());
        $this->assertEquals(2, $coordinate->getLatitude());
        $this->assertEquals(3, $coordinate->getAltitude());
    }

    public function testCreateCoordinateFromStringWithOneValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Coordinate::fromString('1');
    }

    public function testCreateCoordinateFromStringWithFourValues(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Coordinate::fromString('1,2,3,4');
    }
}
