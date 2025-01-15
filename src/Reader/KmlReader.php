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

use Omines\Kameleon\Exception\FileIsDirectoryException;
use Omines\Kameleon\Exception\FileNotFoundException;
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
     */
    public function readFromFile(string $fileName): ?KmlDocument
    {
        if (!file_exists($fileName)) {
            throw new FileNotFoundException(sprintf('File "%s" does not exist', $fileName));
        }
        if (is_dir($fileName)) {
            throw new FileIsDirectoryException(sprintf('File "%s" is a directory', $fileName));
        }

        $document = ErrorToExceptionTransformer::run(function () use ($fileName): ?string {
            $document = null;
            $zip = new \ZipArchive();
            $openResult = $zip->open($fileName);

            if (\ZipArchive::ER_NOZIP === $openResult) {
                $document = file_get_contents($fileName);
            } elseif (true === $openResult) {
                $document = $zip->getFromName('doc.kml');
                $zip->close();

                if (false === $document) {
                    throw new InvalidArchiveException(sprintf('No KML file found in KMZ archive "%s"', $fileName));
                }
            }

            return $document ?: null;
        });

        if (empty($document)) {
            throw new InvalidFileException('Empty KML/KMZ file provided');
        }
        assert(is_string($document));

        $cleanFileName = preg_replace('/\.[^.]*$/', '', pathinfo($fileName, PATHINFO_FILENAME));
        assert(is_string($cleanFileName));

        return $this->parseDocument($document, $cleanFileName);
    }

    /**
     * @throws InvalidFileException If the provided document is not a valid KML file
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
                    assert(is_string($namespace));
                    $doc->setXmlns($namespace);
                }

                foreach ($xml->Document->children() as $node) {
                    switch ($node->getName()) {
                        case 'name':
                            $doc->setName((string) $node);
                            break;
                        case 'description':
                            $doc->setDescription((string) $node);
                            break;
                        case 'open':
                            $doc->setOpen('1' === (string) $node);
                            break;
                        default:
                            if ($node = KmlNode::fromSimpleXmlElement($node)) {
                                $doc->addNode($node);
                            }
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
