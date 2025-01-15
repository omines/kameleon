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

use Omines\Kameleon\Exception\FileIsDirectoryException;
use Omines\Kameleon\Exception\FileNotFoundException;
use Omines\Kameleon\Exception\InvalidArchiveException;
use Omines\Kameleon\Exception\InvalidFileException;
use Omines\Kameleon\Model\KmlDocument;
use Omines\Kameleon\Reader\KmlReader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(KmlReader::class)]
class KmlReaderTest extends TestCase
{
    public function testReadKmlFileExistsFailure(): void
    {
        $reader = new KmlReader();

        $this->expectException(FileNotFoundException::class);
        $reader->readFromFile(__DIR__ . '/does_not_exist.kml');
    }

    public function testReadKmlFileIsFolderFailure(): void
    {
        $reader = new KmlReader();

        $this->expectException(FileIsDirectoryException::class);
        $reader->readFromFile(__DIR__);
    }

    public function testReadKmlStringFailure(): void
    {
        $reader = new KmlReader();

        $this->expectException(InvalidFileException::class);
        $reader->readFromString('invalid', 'invalid.kml');
    }

    public function testReadKmlFileEmptyFailure(): void
    {
        $reader = new KmlReader();

        $this->expectException(InvalidFileException::class);
        $reader->readFromFile(__DIR__ . '/../Data/empty.kmz');
    }

    public function testReadKmlFileInvalidFailure(): void
    {
        $reader = new KmlReader();

        $this->expectException(InvalidFileException::class);
        $reader->readFromFile(__DIR__ . '/../Data/invalid.kml');
    }

    public function testReadKmzFileInvalidFailure(): void
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
        $this->assertEquals('http://www.opengis.net/kml/2.1', $document->getXmlns());
        $this->assertEquals('KML Samples', $document->getName());
        $this->assertEquals('Unleash your creativity with the help of these examples!', $document->getDescription());
        $this->assertFalse($document->isOpen());
        $this->assertNotEmpty($document->getNodes());
    }

    public function testReadKmzFileSuccess(): void
    {
        $reader = new KmlReader();
        $document = $reader->readFromFile(__DIR__ . '/../Data/valid.kmz');

        $this->assertInstanceOf(KmlDocument::class, $document);
    }

    public function testKmlWithoutXmlns(): void
    {
        $reader = new KmlReader();
        $document = $reader->readFromFile(__DIR__ . '/../Data/no_xmlns.kml');

        $this->assertInstanceOf(KmlDocument::class, $document);
        $this->assertEquals('http://www.opengis.net/kml/2.2', $document->getXmlns());
        $this->assertTrue($document->isOpen());
    }
}
