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
use Omines\Kameleon\Util\ErrorToExceptionTransformer;

class KmlReader
{
    /**
     * @throws InvalidFileException     If the file does not exist or is not a valid KML file
     * @throws InvalidArchiveException  If an archive is provided, but it does not contain a KML file named "doc.kml"
     * @throws \ErrorException      If the reader itself fails catastrophically
     */
    public function readFromFile(string $fileName): ?KmlDocument
    {
        //        if (!file_exists($fileName) || !is_file($fileName)) {
        //            throw new InvalidFileException(sprintf('Invalid file "%s" provided', $fileName));
        //        }
        //
        $document = ErrorToExceptionTransformer::run(function () use ($fileName): ?string {
            $document = null;
            $zip = new \ZipArchive();
            $openResult = null;
            try {
                $openResult = $zip->open($fileName);

                if (\ZipArchive::ER_NOZIP === $openResult) {
                    $document = file_get_contents($fileName);
                } elseif (true === $openResult) {
                    $document = $zip->getFromName('doc.kml');

                    if (false === $document) {
                        throw new InvalidArchiveException(sprintf('No KML file found in KMZ archive "%s"', $fileName));
                    }
                }
            } finally {
                if (true === $openResult) {
                    $zip->close();
                }
            }

            return $document ?: null;
        });

        if (empty($document) || !is_string($document)) {
            throw new InvalidFileException('Empty KML/KMZ file provided');
        }

        return $this->parseDocument(
            $document,
            (string) preg_replace('/\.[^.]*$/', '', pathinfo($fileName, PATHINFO_FILENAME)),
        );
    }

    /**
     * @throws InvalidFileException If the provided document is not a valid KML file
     * @throws \ErrorException      If the reader itself fails catastrophically
     */
    public function readFromString(string $document, string $fileName): ?KmlDocument
    {
        return $this->parseDocument($document, $fileName);
    }

    private function parseDocument(string $document, string $fileName): KmlDocument
    {
        try {
            $doc = ErrorToExceptionTransformer::run(function () use ($document, $fileName): KmlDocument {
                $xml = new \SimpleXMLElement($document);
                $doc = new KmlDocument($fileName);

                if (null !== ($namespace = $xml->getNamespaces()[''] ?? null)) {
                    $doc->setXmlns((string) $namespace);
                }

                foreach ($xml->Document->children() as $node) {
                    if ('name' === $node->getName()) {
                        $doc->setName((string) $node);
                    } elseif ($node = KmlNode::fromSimpleXmlElement($node)) {
                        $doc->addNode($node);
                    }
                }

                return $doc;
            });
        } catch (\Throwable $e) {
            throw new InvalidFileException('KML format is invalid');
        }
        assert($doc instanceof KmlDocument);

        return $doc;
    }
}
