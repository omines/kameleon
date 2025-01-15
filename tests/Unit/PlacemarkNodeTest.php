<?php

/*
 * KaMeLeon - KML and KMZ reader/writer
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Omines\Kameleon\Model\Coordinate;
use Omines\Kameleon\Model\Node\KmlNode;
use Omines\Kameleon\Model\Node\PlacemarkNode;
use Omines\Kameleon\Model\Polygon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(KmlNode::class)]
#[CoversClass(PlacemarkNode::class)]
#[CoversClass(Coordinate::class)]
#[CoversClass(Polygon::class)]
class PlacemarkNodeTest extends TestCase
{
    public function testManual(): void
    {
        $node = new PlacemarkNode();
        $this->assertInstanceOf(PlacemarkNode::class, $node);
        $this->assertNull($node->getName());
        $this->assertNull($node->getDescription());
        $this->assertNull($node->getStyleUrl());
        $this->assertNull($node->getOuterBoundary());
        $this->assertNull($node->getInnerBoundary());
        $this->assertNull($node->getLineString());
        $this->assertNull($node->getPoint());
        $this->assertEmpty($node->getExtendedData());

        $node->setName('test');
        $this->assertEquals('test', $node->getName());

        $node->setDescription('test');
        $this->assertEquals('test', $node->getDescription());

        $node->setStyleUrl('test');
        $this->assertEquals('test', $node->getStyleUrl());

        $node->setOuterBoundary(new Polygon());
        $this->assertNotNull($node->getOuterBoundary());

        $node->setInnerBoundary(new Polygon());
        $this->assertNotNull($node->getInnerBoundary());

        $node->setLineString(new Polygon());
        $this->assertNotNull($node->getLineString());

        $node->setPoint(new Coordinate(0, 0));
        $this->assertNotNull($node->getPoint());

        $node->setExtendedData(['test' => 'test']);
        $this->assertEquals(['test' => 'test'], $node->getExtendedData());
        $this->assertEquals('test', $node->getExtendedDataValue('test'));
        $this->assertCount(1, $node->getExtendedData());

        $node->addExtendedData('test2', 'test2');
        $this->assertCount(2, $node->getExtendedData());

        $node->removeExtendedData('test');
        $this->assertCount(1, $node->getExtendedData());
    }

    public function testFactory(): void
    {
        $node = KmlNode::fromSimpleXmlElement(new SimpleXMLElement(<<<EOF
<Placemark id="test">
    <Name>test-name</Name>
    <Description>test-description</Description>
    <StyleUrl>test-style-url</StyleUrl>
    <Polygon>
        <outerBoundaryIs>
            <LinearRing>
                <extrude>1</extrude>
                <tessellate>1</tessellate>
                <coordinates>0,0 0,1 1,1 1,0 0,0</coordinates>
            </LinearRing>
        </outerBoundaryIs>
    </Polygon>
    <Polygon>
        <innerBoundaryIs>
            <LinearRing>
                <extrude>0</extrude>
                <tessellate>0</tessellate>
                <coordinates>0,0 0,1 1,1 1,0 0,0</coordinates>
            </LinearRing>
        </innerBoundaryIs>
    </Polygon>
    <Polygon>
        <LineString>
            <altitudeMode>clampToGround</altitudeMode>
            <coordinates>0,0 0,1 1,1 1,0 0,0</coordinates>
        </LineString>
    </Polygon>
    <Point>
        <coordinates>1,1,12</coordinates>
    </Point>
    <ExtendedData>
        <WrongTag name="wrong">value</WrongTag>
        <Data name="test">value</Data>
    </ExtendedData>
</Placemark>
EOF
        ));
        $this->assertInstanceOf(PlacemarkNode::class, $node);

        $this->assertEquals('test-name', $node->getName());
        $this->assertEquals('test-description', $node->getDescription());
        $this->assertEquals('test-style-url', $node->getStyleUrl());
        $this->assertNotNull($node->getOuterBoundary());
        $this->assertNotNull($node->getInnerBoundary());
        $this->assertNotNull($node->getLineString());
        $this->assertNotNull($node->getPoint());
        $this->assertEquals(1, $node->getPoint()->getLatitude());
        $this->assertEquals(1, $node->getPoint()->getLongitude());
        $this->assertEquals(['test' => 'value'], $node->getExtendedData());
        $this->assertEquals('value', $node->getExtendedDataValue('test'));
        $this->assertCount(1, $node->getExtendedData());

        $dom = new DOMDocument();
        $documentElement = $dom->createElement('Document');
        $node->appendTo($dom, $documentElement);
        $dom->appendChild($documentElement);
        $xml = $dom->saveXML();

        $this->assertNotFalse($xml);
        $this->assertStringContainsStringIgnoringCase('<Placemark id="test">', $xml);
        $this->assertStringContainsStringIgnoringCase('<name>test-name</name>', $xml);
        $this->assertStringContainsStringIgnoringCase('<description>test-description</description>', $xml);
        $this->assertStringContainsStringIgnoringCase('<styleUrl>test-style-url</styleUrl>', $xml);
        $this->assertStringContainsStringIgnoringCase('<Polygon>', $xml);
        $this->assertStringContainsStringIgnoringCase('<outerBoundaryIs>', $xml);
        $this->assertStringContainsStringIgnoringCase('<innerBoundaryIs>', $xml);
        $this->assertStringContainsStringIgnoringCase('<LinearRing>', $xml);
        $this->assertStringContainsStringIgnoringCase('<coordinates>', $xml);
        $this->assertStringContainsStringIgnoringCase('<Point>', $xml);
        $this->assertStringContainsStringIgnoringCase('<ExtendedData>', $xml);
        $this->assertStringContainsStringIgnoringCase('<Data name="test">value</Data>', $xml);
        $this->assertEquals(3, mb_substr_count($xml, '<altitudeMode>'));
        $this->assertEquals(4, mb_substr_count($xml, '<coordinates>'));
        $this->assertEquals(2, mb_substr_count($xml, '<extrude>0</extrude>'));
        $this->assertEquals(1, mb_substr_count($xml, '<extrude>1</extrude>'));
        $this->assertEquals(2, mb_substr_count($xml, '<tesselate>0</tesselate>'));
        $this->assertEquals(1, mb_substr_count($xml, '<tesselate>1</tesselate>'));
    }

    public function testPointWithShortNotation(): void
    {
        $node = KmlNode::fromSimpleXmlElement(new SimpleXMLElement(<<<EOF
<Placemark>
    <Point>
        <coordinates>1,1</coordinates>
    </Point>
</Placemark>
EOF
        ));
        $this->assertInstanceOf(PlacemarkNode::class, $node);
        $this->assertNotNull($node->getPoint());
        $this->assertEquals(1, $node->getPoint()->getLatitude());
        $this->assertEquals(1, $node->getPoint()->getLongitude());
        $this->assertEquals(0, $node->getPoint()->getAltitude());
    }

    public function testPointWithFullNotation(): void
    {
        $node = KmlNode::fromSimpleXmlElement(new SimpleXMLElement(<<<EOF
<Placemark>
    <Point>
        <coordinates>1,1,2</coordinates>
    </Point>
</Placemark>
EOF
        ));
        $this->assertInstanceOf(PlacemarkNode::class, $node);
        $this->assertNotNull($node->getPoint());
        $this->assertEquals(1, $node->getPoint()->getLatitude());
        $this->assertEquals(1, $node->getPoint()->getLongitude());
        $this->assertEquals(2, $node->getPoint()->getAltitude());
    }

    public function testBrokenExtendedData(): void
    {
        $node = KmlNode::fromSimpleXmlElement(new SimpleXMLElement(<<<EOF
<Placemark>
    <ExtendedData>
        <Data key="test">value</Data>
    </ExtendedData>
</Placemark>
EOF
        ));
        $this->assertInstanceOf(PlacemarkNode::class, $node);
        $this->assertEmpty($node->getExtendedData());
    }

    public function testBrokenFactory(): void
    {
        $this->expectException(InvalidArgumentException::class);
        KmlNode::fromSimpleXmlElement(new SimpleXMLElement(<<<EOF
<Placemark>
    <Name>test-name</Name>
    <Description>test-description</Description>
    <StyleUrl>test-style-url</StyleUrl>
    <Polygon>
        <outerBoundaryIs>
            <LinearRing>
                <coordinates>0,0 0,1 1,1 1,0 0,0</coordinates>
            </LinearRing>
        </outerBoundaryIs>
    </Polygon>
    <Polygon>
        <innerBoundaryIs>
            <LinearRing>
                <coordinates>0,0 0,1 1,1 1,0 0,0</coordinates>
            </LinearRing>
        </innerBoundaryIs>
    </Polygon>
    <Polygon>
        <LineString>
            <altitudeMode>clampToGround</altitudeMode>
            <coordinates>0,0 0,1 1,1 1,0 0,0</coordinates>
        </LineString>
    </Polygon>
    <Point>
        <coordinates>1,1,2,2</coordinates>
    </Point>
    <ExtendedData>
        <Data name="test">value</Data>
    </ExtendedData>
</Placemark>
EOF
        ));
    }

    public function testEmptyValuesAreEmpty(): void
    {
        $node = KmlNode::fromSimpleXmlElement(new SimpleXMLElement('<Placemark></Placemark>'));
        $this->assertInstanceOf(PlacemarkNode::class, $node);
        $this->assertNull($node->getName());
        $this->assertNull($node->getDescription());
        $this->assertNull($node->getStyleUrl());
        $this->assertNull($node->getOuterBoundary());
        $this->assertNull($node->getInnerBoundary());
        $this->assertNull($node->getLineString());
        $this->assertNull($node->getPoint());
        $this->assertEmpty($node->getExtendedData());

        $dom = new DOMDocument();
        $documentElement = $dom->createElement('Document');
        $node->appendTo($dom, $documentElement);
        $dom->appendChild($documentElement);
        $xmlString = $dom->saveXML();

        $this->assertNotFalse($xmlString);
        $this->assertStringContainsStringIgnoringCase('<Placemark', $xmlString);
        $this->assertStringNotContainsStringIgnoringCase('<Placemark id="', $xmlString);
        $this->assertStringNotContainsStringIgnoringCase('<name>', $xmlString);
        $this->assertStringNotContainsStringIgnoringCase('<description>', $xmlString);
        $this->assertStringNotContainsStringIgnoringCase('<styleUrl>', $xmlString);
        $this->assertStringNotContainsStringIgnoringCase('<Polygon>', $xmlString);
        $this->assertStringNotContainsStringIgnoringCase('<LineString>', $xmlString);
        $this->assertStringNotContainsStringIgnoringCase('<Point>', $xmlString);
        $this->assertStringNotContainsStringIgnoringCase('<ExtendedData>', $xmlString);
    }

    public function testOnlyOuterBoundary(): void
    {
        $node = KmlNode::fromSimpleXmlElement(new SimpleXMLElement(<<<EOF
<Placemark>
    <Polygon>
        <outerBoundaryIs>
            <LinearRing>
                <coordinates>0,0 0,1 1,1 1,0 0,0</coordinates>
            </LinearRing>
        </outerBoundaryIs>
    </Polygon>
</Placemark>
EOF
        ));
        $this->assertInstanceOf(PlacemarkNode::class, $node);
        $this->assertNotNull($node->getOuterBoundary());
        $this->assertNull($node->getInnerBoundary());

        $dom = new DOMDocument();
        $documentElement = $dom->createElement('Document');
        $node->appendTo($dom, $documentElement);
        $dom->appendChild($documentElement);
        $xml = $dom->saveXML();

        $this->assertNotFalse($xml);
        $this->assertStringContainsStringIgnoringCase('<Polygon>', $xml);
    }

    public function testOnlyInnerBoundary(): void
    {
        $node = KmlNode::fromSimpleXmlElement(new SimpleXMLElement(<<<EOF
<Placemark>
    <Polygon>
        <innerBoundaryIs>
            <LinearRing>
                <coordinates>0,0 0,1 1,1 1,0 0,0</coordinates>
            </LinearRing>
        </innerBoundaryIs>
    </Polygon>
</Placemark>
EOF
        ));
        $this->assertInstanceOf(PlacemarkNode::class, $node);
        $this->assertNull($node->getOuterBoundary());
        $this->assertNotNull($node->getInnerBoundary());

        $dom = new DOMDocument();
        $documentElement = $dom->createElement('Document');
        $node->appendTo($dom, $documentElement);
        $dom->appendChild($documentElement);
        $xml = $dom->saveXML();

        $this->assertNotFalse($xml);
        $this->assertStringContainsStringIgnoringCase('<Polygon>', $xml);
    }
}
