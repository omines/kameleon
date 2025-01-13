<?php

/*
 * KaMeLeon - KML and KMZ reader/writer
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Omines\Kameleon\Writer;

use Omines\Kameleon\Exception\InvalidArchiveException;
use Omines\Kameleon\Model\KmlDocument;

class KmlWriter
{
    public function writeKml(KmlDocument $document): string
    {
        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $kml = $xml->createElement('kml');
        $kml->setAttribute('xmlns', $document->getXmlns());
        $xml->appendChild($kml);

        $documentElement = $xml->createElement('Document');
        $kml->appendChild($documentElement);

        foreach ($document->getNodes() as $node) {
            $node->appendTo($xml, $documentElement);
        }

        return $xml->saveXML() ?: throw new \RuntimeException('Failed to write KML document');
    }

    public function writeKmz(KmlDocument $document): string
    {
        $doc = $this->writeKml($document);

        $tempFile = tempnam(sys_get_temp_dir(), 'zip');

        $zip = new \ZipArchive();
        $zip->open($tempFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFromString('doc.kml', $doc);
        $zip->close();

        $zipContent = file_get_contents($tempFile);

        unlink($tempFile);

        return $zipContent ?: throw new InvalidArchiveException('Failed to write KMZ document');
    }

    public function streamKml(KmlDocument $document): void
    {
        $output = fopen('php://output', 'w');
        assert(false !== $output);

        $doc = $this->writeKml($document);

        header('Content-Type: application/vnd.google-earth.kml+xml');
        header('Content-Disposition: attachment; filename="doc.kml"');

        fprintf($output, '%s', $doc);
        fclose($output);
    }

    public function streamKmz(KmlDocument $document): void
    {
        $output = fopen('php://output', 'w');
        assert(false !== $output);

        $doc = $this->writeKmz($document);

        header('Content-Type: application/vnd.google-earth.kmz');
        header('Content-Disposition: attachment; filename="doc.kmz"');

        fprintf($output, '%s', $doc);
        fclose($output);
    }
}
