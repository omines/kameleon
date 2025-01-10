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

use Omines\Kameleon\Exception\InvalidArchiveException;
use Omines\Kameleon\Exception\InvalidFileException;
use Omines\Kameleon\Model\KmlDocument;
use Omines\Kameleon\Reader\KmlReader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(KmlReader::class)]
class KmlReaderTest extends TestCase
{
    public function testReadKmlStringFailure(): void
    {
        $reader = new KmlReader();

        $this->expectException(InvalidFileException::class);
        $reader->readFromString('invalid', 'invalid.kml');
    }

    public function testReadKmlFileFailure(): void
    {
        $reader = new KmlReader();

        $this->expectException(InvalidFileException::class);
        $reader->readFromFile(__DIR__ . '/../Data/invalid.kml');
    }

    public function testReadKmzFileFailure(): void
    {
        $reader = new KmlReader();

        $this->expectException(InvalidArchiveException::class);
        $reader->readFromFile(__DIR__ . '/../Data/invalid.kmz');
    }

    public function testReadKmlStringSuccess(): void
    {
        $reader = new KmlReader();
        $document = $reader->readFromString((string) file_get_contents(__DIR__ . '/../Data/valid.kml'), 'valid.kml');

        $this->assertInstanceOf(KmlDocument::class, $document);
    }

    public function testReadKmlFileSuccess(): void
    {
        $reader = new KmlReader();
        $document = $reader->readFromFile(__DIR__ . '/../Data/valid.kml');

        $this->assertInstanceOf(KmlDocument::class, $document);
    }

    public function testReadKmzFileSuccess(): void
    {
        $reader = new KmlReader();
        $document = $reader->readFromFile(__DIR__ . '/../Data/valid.kmz');

        $this->assertInstanceOf(KmlDocument::class, $document);
    }
}
