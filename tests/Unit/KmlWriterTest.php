<?php

/*
 * KaMeLeon - KML and KMZ reader/writer
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Omines\Kameleon\Model\KmlDocument;
use Omines\Kameleon\Reader\KmlReader;
use Omines\Kameleon\Writer\KmlWriter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(KmlReader::class)]
#[CoversClass(KmlWriter::class)]
class KmlWriterTest extends TestCase
{
    public function testCreateKmlAfterReading(): void
    {
        $reader = new KmlReader();
        $writer = new KmlWriter();

        $document = $reader->readFromFile(__DIR__ . '/../Data/valid.kml');
        $this->assertInstanceOf(KmlDocument::class, $document);

        $output = $writer->writeKml($document);
        $this->assertStringContainsStringIgnoringCase('<kml xmlns="http://www.opengis.net/kml/2.1">', $output);
        $this->assertStringContainsStringIgnoringCase('<Document>', $output);
        $this->assertStringContainsStringIgnoringCase('<Style id="downArrowIcon">', $output);

        $output = $writer->writeKmz($document);
        $this->assertStringStartsWith('PK', $output);
    }

    public function testStreamKmlAfterReading(): void
    {
        $reader = new KmlReader();
        $writer = new KmlWriter();

        $document = $reader->readFromFile(__DIR__ . '/../Data/valid.kml');
        $this->assertInstanceOf(KmlDocument::class, $document);

        ob_start();
        $writer->streamKml($document);
        $output = (string) ob_get_clean();
        $this->assertStringContainsStringIgnoringCase('<kml xmlns="http://www.opengis.net/kml/2.1">', $output);

        ob_start();
        $writer->streamKmz($document);
        $output = (string) ob_get_clean();
        $this->assertStringStartsWith('PK', $output);
    }
}
