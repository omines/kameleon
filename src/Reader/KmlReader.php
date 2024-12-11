<?php

/*
 * KaMeLeon - KML and KMZ reader/writer
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Omines\Kameleon\Reader;

use Omines\Kameleon\Exception\InvalidArchiveException;
use Omines\Kameleon\Exception\InvalidFileException;
use Omines\Kameleon\Model\KmlDocument;
use Omines\Kameleon\Model\Node\KmlNode;

class KmlReader
{
    /**
     * @throws InvalidFileException     If the file does not exist or is not a valid KML file
     * @throws InvalidArchiveException  If an archive is provided, but it does not contain a KML file named "doc.kml"
     */
    public function read(string $fileName): ?KmlDocument
    {
        if (!file_exists($fileName) || !is_file($fileName)) {
            throw new InvalidFileException(sprintf('Invalid file "%s" provided', $fileName));
        }

        $zip = new \ZipArchive();
        $openResult = $zip->open($fileName);

        try {
            if (\ZipArchive::ER_NOZIP === $openResult) {
                $document = file_get_contents($fileName);
            } elseif (true === $openResult) {
                $document = $zip->getFromName('doc.kml');

                if (false === $document) {
                    throw new InvalidArchiveException(sprintf('No KML file found in KMZ archive "%s"', $fileName));
                }
            } else {
                throw new InvalidFileException(sprintf('Invalid file "%s" provided', $fileName));
            }
        } finally {
            if (true === $openResult) {
                $zip->close();
            }
        }

        if (empty($document)) {
            throw new InvalidFileException('Empty KML/KMZ file provided');
        }

        return $this->parseDocument(
            $document,
            (string) preg_replace('/\.[^.]*$/', '', pathinfo($fileName, PATHINFO_FILENAME)),
        );
    }

    private function parseDocument(string $document, string $fileName): KmlDocument
    {
        try {
            $xml = new \SimpleXMLElement($document);
            $document = new KmlDocument($fileName);

            if (null !== $xml->attributes()->xmlns) {
                $document->setXmlns((string) $xml->attributes()->xmlns);
            }

            foreach ($xml->Document->children() as $node) {
                if ('name' === $node->getName()) {
                    $document->setName((string) $node);
                } elseif ($node = KmlNode::fromSimpleXmlElement($node)) {
                    $document->addNode($node);
                }
            }
        } catch (\Throwable $e) {
            throw new InvalidFileException('KML format is invalid');
        }

        return $document;
    }
}
