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

use Omines\Kameleon\Model\KmlDocument;

class KmlWriter
{
    public function writeKml(KmlDocument $document): string
    {
        $xml = new \DOMDocument('1.0', 'UTF-8');

        $kml = $xml->createElement('kml');
        $kml->setAttribute('xmlns', $document->getXmlns());
        $xml->appendChild($kml);

        $documentElement = $xml->createElement('Document');
        $kml->appendChild($documentElement);

        foreach ($document->getNodes() as $node) {
            $node->appendTo($xml, $documentElement);
        }

        $output = $xml->saveXML();
        assert(is_string($output));

        return $output;
    }

    public function writeKmz(KmlDocument $document): string
    {
        $doc = $this->writeKml($document);

        $tempFile = tempnam(sys_get_temp_dir(), 'zip');

        $zip = new \ZipArchive();
        $zip->open($tempFile, \ZipArchive::CREATE);
        $zip->addFromString('doc.kml', $doc);
        $zip->close();

        $zipContent = file_get_contents($tempFile);
        assert(is_string($zipContent));

        /* @infection-ignore-all */
        unlink($tempFile);

        return $zipContent;
    }

    public function streamKml(KmlDocument $document): void
    {
        $this->stream($this->writeKml($document), 'application/vnd.google-earth.kml+xml', 'doc.kml');
    }

    public function streamKmz(KmlDocument $document): void
    {
        $this->stream($this->writeKmz($document), 'application/vnd.google-earth.kmz', 'doc.kmz');
    }

    private function stream(string $content, string $contentType, string $filename): void
    {
        /** @var resource $output */
        $output = fopen('php://output', 'w');

        /* @infection-ignore-all */
        header(sprintf('Content-Type: %s', $contentType));
        /* @infection-ignore-all */
        header(sprintf('Content-Disposition: attachment; filename="%s"', $filename));

        fprintf($output, '%s', $content);
        fclose($output);
    }
}
