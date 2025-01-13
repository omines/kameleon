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
use Omines\Kameleon\Model\Node\PlacemarkNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(KmlDocument::class)]
class KmlDocumentTest extends TestCase
{
    public function testProperties(): void
    {
        $document = new KmlDocument('test.kml');
        $this->assertInstanceOf(KmlDocument::class, $document);
        $this->assertEquals('test.kml', $document->getName());
        $this->assertEquals('http://www.opengis.net/kml/2.2', $document->getXmlns());

        $document->setName('test2.kml');
        $this->assertEquals('test2.kml', $document->getName());

        $document->setXmlns('http://www.opengis.net/kml/2.3');
        $this->assertEquals('http://www.opengis.net/kml/2.3', $document->getXmlns());
    }

    public function testNodes(): void
    {
        $document = new KmlDocument('test.kml');

        $this->assertCount(0, $document->getNodes());

        $node = new PlacemarkNode();
        $document->addNode($node);
        $this->assertCount(1, $document->getNodes());
        $this->assertSame($node, $document->getNodes()[0]);
        $this->assertTrue($document->hasNode($node));

        $document->removeNode($node);
        $this->assertCount(0, $document->getNodes());
        $this->assertFalse($document->hasNode($node));

        for ($i = 0; $i < 10; ++$i) {
            $document->addNode(new PlacemarkNode());
        }
        $this->assertCount(10, $document->getNodes());

        $document->clearNodes();
        $this->assertCount(0, $document->getNodes());

        $this->expectException(InvalidArgumentException::class);
        $document->removeNode(new PlacemarkNode());
    }
}
