<?php

/*
 * KaMeLeon - KML and KMZ reader/writer
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit;

use Omines\Kameleon\Exception\InvalidFileException;
use Omines\Kameleon\Reader\KmlReader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(KmlReader::class)]
class ReaderTest extends TestCase
{
    public function testReadFailure(): void
    {
        $reader = new KmlReader();

        $this->expectException(InvalidFileException::class);
        $reader->readFromString('invalid', 'invalid.kml');
    }

    public function testReadFileSuccess(): void
    {
        $reader = new KmlReader();
        $document = $reader->readFromFile(__DIR__ . '/../Data/sample.kml');

        $this->assertNotNull($document);
    }

    public function testReadStringSuccess(): void
    {
        $reader = new KmlReader();
        $document = $reader->readFromString((string) file_get_contents(__DIR__ . '/../Data/sample.kml'), 'sample.kml');

        $this->assertNotNull($document);
    }
}
