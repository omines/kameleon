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
use Omines\Kameleon\Model\Node\StyleMapNode;
use Omines\Kameleon\Model\Node\StyleNode;
use Omines\Kameleon\Model\Polygon;
use Omines\Kameleon\Model\Style\BalloonStyle;
use Omines\Kameleon\Model\Style\IconStyle;
use Omines\Kameleon\Model\Style\LabelStyle;
use Omines\Kameleon\Model\Style\LineStyle;
use Omines\Kameleon\Model\Style\PolyStyle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(KmlNode::class)]
#[CoversClass(PlacemarkNode::class)]
#[CoversClass(StyleMapNode::class)]
#[CoversClass(StyleNode::class)]
class KmlNodeTest extends TestCase
{
    public function testGenericNode(): void
    {
        $node = new PlacemarkNode('test');
        $this->assertInstanceOf(KmlNode::class, $node);
        $this->assertEquals('test', $node->getId());

        $node->setId('test2');
        $this->assertEquals('test2', $node->getId());
    }

    public function testPlacemarkNode(): void
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

    public function testStyleNode(): void
    {
        $node = new StyleNode();
        $this->assertInstanceOf(StyleNode::class, $node);
        $this->assertNull($node->getIconStyle());
        $this->assertNull($node->getLabelStyle());
        $this->assertNull($node->getLineStyle());
        $this->assertNull($node->getPolyStyle());
        $this->assertNull($node->getBalloonStyle());

        $iconStyle = new IconStyle('test', 0, 0, 32, 32);
        $node->setIconStyle($iconStyle);
        $this->assertNotNull($node->getIconStyle());
        $this->assertEquals('test', $node->getIconStyle()->getHref());
        $this->assertEquals(0, $node->getIconStyle()->getX());
        $this->assertEquals(0, $node->getIconStyle()->getY());
        $this->assertEquals(32, $node->getIconStyle()->getWidth());
        $this->assertEquals(32, $node->getIconStyle()->getHeight());

        $labelStyle = new LabelStyle('ff0000ff', 1);
        $node->setLabelStyle($labelStyle);
        $this->assertNotNull($node->getLabelStyle());
        $this->assertEquals('ff0000ff', $node->getLabelStyle()->getColor());
        $this->assertEquals(1, $node->getLabelStyle()->getScale());

        $lineStyle = new LineStyle('ff0000ff', 1);
        $node->setLineStyle($lineStyle);
        $this->assertNotNull($node->getLineStyle());
        $this->assertEquals('ff0000ff', $node->getLineStyle()->getColor());
        $this->assertEquals(1, $node->getLineStyle()->getWidth());

        $polyStyle = new PolyStyle('ff0000ff', 1, 1);
        $node->setPolyStyle($polyStyle);
        $this->assertNotNull($node->getPolyStyle());
        $this->assertEquals('ff0000ff', $node->getPolyStyle()->getColor());
        $this->assertEquals(1, $node->getPolyStyle()->getFill());
        $this->assertEquals(1, $node->getPolyStyle()->getOutline());

        $balloonStyle = new BalloonStyle('test');
        $node->setBalloonStyle($balloonStyle);
        $this->assertNotNull($node->getBalloonStyle());
        $this->assertEquals('test', $node->getBalloonStyle()->getText());
    }

    public function testStyleMapNode(): void
    {
        $node = new StyleMapNode();
        $this->assertInstanceOf(StyleMapNode::class, $node);
        $this->assertEmpty($node->getPairs());

        $node->setPair('test', 'test');
        $this->assertCount(1, $node->getPairs());
        $this->assertTrue($node->hasPair('test'));
        $this->assertEquals('test', $node->getPair('test'));

        $node->removePair('test');
        $this->assertEmpty($node->getPairs());

        $node->setPair('test', 'test');
        $node->setPair('test2', 'test2');
        $this->assertCount(2, $node->getPairs());
        $node->clearPairs();
        $this->assertEmpty($node->getPairs());
    }

    public function testPlacemarkFactory(): void
    {
        $node = KmlNode::fromSimpleXmlElement(new SimpleXMLElement(<<<EOF
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
            <coordinates>0,0 0,1 1,1 1,0 0,0</coordinates>
        </LineString>
    </Polygon>
    <Point>
        <coordinates>1,1</coordinates>
    </Point>
    <ExtendedData>
        <Data name="test">value</Data>
    </ExtendedData>
</Placemark>
EOF));
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

        $dom = new DOMDocument();
        $documentElement = $dom->createElement('Document');
        $node->appendTo($dom, $documentElement);
        $dom->appendChild($documentElement);
        $xml = $dom->saveXML();

        $this->assertNotFalse($xml);
        $this->assertStringContainsString('<Placemark>', $xml);
        $this->assertStringContainsString('<name>test-name</name>', $xml);
    }

    public function testStyleFactory(): void
    {
        $node = KmlNode::fromSimpleXmlElement(new SimpleXMLElement(<<<EOF
<Style>
    <IconStyle>
        <Icon>
            <href>test</href>
            <x>0</x>
            <y>0</y>
            <w>32</w>
            <h>32</h>
        </Icon>
    </IconStyle>
    <LabelStyle>
        <Color>ff0000ff</Color>
        <scale>1</scale>
    </LabelStyle>
    <LineStyle>
        <Color>ff0000ff</Color>
        <width>1</width>
    </LineStyle>
    <PolyStyle>
        <color>ff0000ff</color>
        <fill>1</fill>
        <outline>1</outline>
    </PolyStyle>
    <BalloonStyle>
        <text>test</text>
        <displayMode>default</displayMode>
    </BalloonStyle>
</Style>
EOF));
        $this->assertInstanceOf(StyleNode::class, $node);

        $this->assertInstanceOf(IconStyle::class, $node->getIconStyle());
        $this->assertEquals('test', $node->getIconStyle()->getHref());
        $this->assertEquals(0, $node->getIconStyle()->getX());
        $this->assertEquals(0, $node->getIconStyle()->getY());
        $this->assertEquals(32, $node->getIconStyle()->getWidth());
        $this->assertEquals(32, $node->getIconStyle()->getHeight());

        $this->assertInstanceOf(LabelStyle::class, $node->getLabelStyle());
        $this->assertEquals('ff0000ff', $node->getLabelStyle()->getColor());
        $this->assertEquals(1, $node->getLabelStyle()->getScale());

        $this->assertInstanceOf(LineStyle::class, $node->getLineStyle());
        $this->assertEquals('ff0000ff', $node->getLineStyle()->getColor());
        $this->assertEquals(1, $node->getLineStyle()->getWidth());

        $this->assertInstanceOf(PolyStyle::class, $node->getPolyStyle());
        $this->assertEquals('ff0000ff', $node->getPolyStyle()->getColor());
        $this->assertEquals(1, $node->getPolyStyle()->getFill());
        $this->assertEquals(1, $node->getPolyStyle()->getOutline());

        $this->assertInstanceOf(BalloonStyle::class, $node->getBalloonStyle());
        $this->assertEquals('test', $node->getBalloonStyle()->getText());

        $dom = new DOMDocument();
        $documentElement = $dom->createElement('Document');
        $node->appendTo($dom, $documentElement);
        $dom->appendChild($documentElement);
        $xml = $dom->saveXML();

        $this->assertNotFalse($xml);
        $this->assertStringContainsString('<Style>', $xml);
    }

    public function testStyleMapFactory(): void
    {
        $node = KmlNode::fromSimpleXmlElement(new SimpleXMLElement(<<<EOF
<StyleMap>
    <Pair>
        <key>normal</key>
        <styleUrl>#test1</styleUrl>
    </Pair>
    <Pair>
        <key>highlight</key>
        <styleUrl>#test2</styleUrl>
    </Pair>
</StyleMap>
EOF));
        $this->assertInstanceOf(StyleMapNode::class, $node);

        $this->assertCount(2, $node->getPairs());
        $this->assertEquals('#test1', $node->getPair('normal'));
        $this->assertEquals('#test2', $node->getPair('highlight'));

        $dom = new DOMDocument();
        $documentElement = $dom->createElement('Document');
        $node->appendTo($dom, $documentElement);
        $dom->appendChild($documentElement);
        $xml = $dom->saveXML();

        $this->assertNotFalse($xml);
        $this->assertStringContainsString('<StyleMap>', $xml);
    }

    public function testInvalidFactory(): void
    {
        $node = KmlNode::fromSimpleXmlElement(new SimpleXMLElement('<invalid></invalid>'));
        $this->assertNull($node);
    }
}
