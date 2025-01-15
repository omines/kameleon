<?php

/*
 * KaMeLeon - KML and KMZ reader/writer
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Omines\Kameleon\Enum\DisplayMode;
use Omines\Kameleon\Model\Node\KmlNode;
use Omines\Kameleon\Model\Node\StyleNode;
use Omines\Kameleon\Model\Style\BalloonStyle;
use Omines\Kameleon\Model\Style\IconStyle;
use Omines\Kameleon\Model\Style\LabelStyle;
use Omines\Kameleon\Model\Style\LineStyle;
use Omines\Kameleon\Model\Style\PolyStyle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(KmlNode::class)]
#[CoversClass(StyleNode::class)]
#[CoversClass(BalloonStyle::class)]
#[CoversClass(IconStyle::class)]
#[CoversClass(LabelStyle::class)]
#[CoversClass(LineStyle::class)]
#[CoversClass(PolyStyle::class)]
class StyleNodeTest extends TestCase
{
    public function testManual(): void
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

        $iconStyle
            ->setHref('test2')
            ->setX(1)
            ->setY(1)
            ->setWidth(33)
            ->setHeight(33)
        ;
        $this->assertEquals('test2', $iconStyle->getHref());
        $this->assertEquals(1, $iconStyle->getX());
        $this->assertEquals(1, $iconStyle->getY());
        $this->assertEquals(33, $iconStyle->getWidth());
        $this->assertEquals(33, $iconStyle->getHeight());

        $labelStyle = new LabelStyle('ff0000ff', 1);
        $node->setLabelStyle($labelStyle);
        $this->assertNotNull($node->getLabelStyle());
        $this->assertEquals('ff0000ff', $node->getLabelStyle()->getColor());
        $this->assertEquals(1, $node->getLabelStyle()->getScale());

        $labelStyle
            ->setColor('00ff00ff')
            ->setScale(2)
        ;
        $this->assertEquals('00ff00ff', $labelStyle->getColor());
        $this->assertEquals(2, $labelStyle->getScale());

        $lineStyle = new LineStyle('ff0000ff', 1);
        $node->setLineStyle($lineStyle);
        $this->assertNotNull($node->getLineStyle());
        $this->assertEquals('ff0000ff', $node->getLineStyle()->getColor());
        $this->assertEquals(1, $node->getLineStyle()->getWidth());

        $lineStyle
            ->setColor('00ff00ff')
            ->setWidth(2)
        ;
        $this->assertEquals('00ff00ff', $lineStyle->getColor());
        $this->assertEquals(2, $lineStyle->getWidth());

        $polyStyle = new PolyStyle('ff0000ff', 1, 1);
        $node->setPolyStyle($polyStyle);
        $this->assertNotNull($node->getPolyStyle());
        $this->assertEquals('ff0000ff', $node->getPolyStyle()->getColor());
        $this->assertEquals(1, $node->getPolyStyle()->getFill());
        $this->assertEquals(1, $node->getPolyStyle()->getOutline());

        $polyStyle
            ->setColor('00ff00ff')
            ->setFill(0)
            ->setOutline(0)
        ;
        $this->assertEquals('00ff00ff', $polyStyle->getColor());
        $this->assertEquals(0, $polyStyle->getFill());
        $this->assertEquals(0, $polyStyle->getOutline());

        $balloonStyle = new BalloonStyle('test');
        $node->setBalloonStyle($balloonStyle);
        $this->assertNotNull($node->getBalloonStyle());
        $this->assertEquals('test', $node->getBalloonStyle()->getText());
        $this->assertEquals(DisplayMode::DEFAULT, $node->getBalloonStyle()->getDisplayMode());

        $balloonStyle
            ->setText('test2')
            ->setDisplayMode(DisplayMode::HIDE)
        ;
        $this->assertEquals('test2', $balloonStyle->getText());
        $this->assertEquals(DisplayMode::HIDE, $balloonStyle->getDisplayMode());

        $balloonStyle->setDisplayModeFromString('default');
        $this->assertEquals(DisplayMode::DEFAULT, $balloonStyle->getDisplayMode());
    }

    public function testFactory(): void
    {
        $node = KmlNode::fromSimpleXmlElement(new SimpleXMLElement(<<<EOF
<Style id="test">
    <IconStyle>
        <Icon>
            <href>test</href>
            <x>10</x>
            <y>10</y>
            <w>32</w>
            <h>32</h>
        </Icon>
    </IconStyle>
    <LabelStyle>
        <Color>ff0000ff</Color>
        <scale>2</scale>
    </LabelStyle>
    <LineStyle>
        <Color>00ff00ff</Color>
        <width>1</width>
    </LineStyle>
    <PolyStyle>
        <color>0000ffff</color>
        <fill>1</fill>
        <outline>1</outline>
    </PolyStyle>
    <BalloonStyle>
        <text>test</text>
        <displayMode>hide</displayMode>
    </BalloonStyle>
</Style>
EOF
        ));
        $this->assertInstanceOf(StyleNode::class, $node);

        $this->assertInstanceOf(IconStyle::class, $node->getIconStyle());
        $this->assertEquals('test', $node->getIconStyle()->getHref());
        $this->assertEquals(10, $node->getIconStyle()->getX());
        $this->assertEquals(10, $node->getIconStyle()->getY());
        $this->assertEquals(32, $node->getIconStyle()->getWidth());
        $this->assertEquals(32, $node->getIconStyle()->getHeight());

        $this->assertInstanceOf(LabelStyle::class, $node->getLabelStyle());
        $this->assertEquals('ff0000ff', $node->getLabelStyle()->getColor());
        $this->assertEquals(2, $node->getLabelStyle()->getScale());

        $this->assertInstanceOf(LineStyle::class, $node->getLineStyle());
        $this->assertEquals('00ff00ff', $node->getLineStyle()->getColor());
        $this->assertEquals(1, $node->getLineStyle()->getWidth());

        $this->assertInstanceOf(PolyStyle::class, $node->getPolyStyle());
        $this->assertEquals('0000ffff', $node->getPolyStyle()->getColor());
        $this->assertEquals(1, $node->getPolyStyle()->getFill());
        $this->assertEquals(1, $node->getPolyStyle()->getOutline());

        $this->assertInstanceOf(BalloonStyle::class, $node->getBalloonStyle());
        $this->assertEquals('test', $node->getBalloonStyle()->getText());
        $this->assertEquals(DisplayMode::HIDE, $node->getBalloonStyle()->getDisplayMode());

        $dom = new DOMDocument();
        $documentElement = $dom->createElement('Document');
        $node->appendTo($dom, $documentElement);
        $dom->appendChild($documentElement);
        $xml = $dom->saveXML();

        $this->assertNotFalse($xml);
        $this->assertStringContainsStringIgnoringCase('<Style id="test">', $xml);

        $this->assertStringContainsStringIgnoringCase('<BalloonStyle>', $xml);
        $this->assertStringContainsStringIgnoringCase('<text>test</text>', $xml);
        $this->assertStringContainsStringIgnoringCase('<displayMode>hide</displayMode>', $xml);

        $this->assertStringContainsStringIgnoringCase('<IconStyle>', $xml);
        $this->assertStringContainsStringIgnoringCase('<Icon>', $xml);
        $this->assertStringContainsStringIgnoringCase('<href>test</href>', $xml);
        $this->assertStringContainsStringIgnoringCase('<x>10</x>', $xml);
        $this->assertStringContainsStringIgnoringCase('<y>10</y>', $xml);
        $this->assertStringContainsStringIgnoringCase('<w>32</w>', $xml);
        $this->assertStringContainsStringIgnoringCase('<h>32</h>', $xml);

        $this->assertStringContainsStringIgnoringCase('<LabelStyle>', $xml);
        $this->assertStringContainsStringIgnoringCase('<Color>ff0000ff</Color>', $xml);
        $this->assertStringContainsStringIgnoringCase('<scale>2</scale>', $xml);

        $this->assertStringContainsStringIgnoringCase('<LineStyle>', $xml);
        $this->assertStringContainsStringIgnoringCase('<Color>00ff00ff</Color>', $xml);
        $this->assertStringContainsStringIgnoringCase('<width>1</width>', $xml);

        $this->assertStringContainsStringIgnoringCase('<PolyStyle>', $xml);
        $this->assertStringContainsStringIgnoringCase('<color>0000ffff</color>', $xml);
        $this->assertStringContainsStringIgnoringCase('<fill>1</fill>', $xml);
        $this->assertStringContainsStringIgnoringCase('<outline>1</outline>', $xml);
    }

    public function testBrokenIconStyle(): void
    {
        $node = KmlNode::fromSimpleXmlElement(new SimpleXMLElement(<<<EOF
<Style id="test">
    <IconStyle>
        <Invalid>0</Invalid>
    </IconStyle>
</Style>
EOF
        ));
        $this->assertInstanceOf(StyleNode::class, $node);
        $this->assertNull($node->getIconStyle());
    }

    public function testIconStyleWithMissingHref(): void
    {
        $node = KmlNode::fromSimpleXmlElement(new SimpleXMLElement(<<<EOF
<Style id="test">
    <IconStyle>
        <Icon>
            <x>0</x>
            <y>0</y>
            <w>32</w>
            <h>32</h>
        </Icon>
    </IconStyle>
</Style>

EOF
        ));
        $this->assertInstanceOf(StyleNode::class, $node);
        $this->assertNotNull($node->getIconStyle());
        $this->assertNull($node->getIconStyle()->getHref());

        $dom = new DOMDocument();
        $documentElement = $dom->createElement('Document');
        $node->appendTo($dom, $documentElement);
        $dom->appendChild($documentElement);
        $xml = $dom->saveXML();

        $this->assertNotFalse($xml);
        $this->assertStringContainsStringIgnoringCase('<Style id="test">', $xml);
        $this->assertStringContainsStringIgnoringCase('<IconStyle', $xml);
        $this->assertStringNotContainsStringIgnoringCase('<href>', $xml);
    }

    public function testLabelStyleWithMissingColor(): void
    {
        $node = KmlNode::fromSimpleXmlElement(new SimpleXMLElement(<<<EOF
<Style id="test">
    <LabelStyle>
        <scale>1</scale>
    </LabelStyle>
</Style>

EOF
        ));
        $this->assertInstanceOf(StyleNode::class, $node);
        $this->assertNotNull($node->getLabelStyle());
        $this->assertNull($node->getLabelStyle()->getColor());
        $this->assertEquals(1, $node->getLabelStyle()->getScale());

        $dom = new DOMDocument();
        $documentElement = $dom->createElement('Document');
        $node->appendTo($dom, $documentElement);
        $dom->appendChild($documentElement);
        $xml = $dom->saveXML();

        $this->assertNotFalse($xml);
        $this->assertStringContainsStringIgnoringCase('<Style id="test">', $xml);
        $this->assertStringContainsStringIgnoringCase('<LabelStyle', $xml);
        $this->assertStringNotContainsStringIgnoringCase('<Color>', $xml);
        $this->assertStringContainsStringIgnoringCase('<scale>', $xml);
    }

    public function testEmpty(): void
    {
        $node = KmlNode::fromSimpleXmlElement(new SimpleXMLElement(<<<EOF
<Style id="test"/>
EOF
        ));

        $this->assertInstanceOf(StyleNode::class, $node);
        $this->assertNull($node->getBalloonStyle());
        $this->assertNull($node->getIconStyle());
        $this->assertNull($node->getLabelStyle());
        $this->assertNull($node->getLineStyle());
        $this->assertNull($node->getPolyStyle());

        $dom = new DOMDocument();
        $documentElement = $dom->createElement('Document');
        $node->appendTo($dom, $documentElement);
        $dom->appendChild($documentElement);
        $xml = $dom->saveXML();

        $this->assertNotFalse($xml);
        $this->assertStringContainsStringIgnoringCase('<Style id="test"/>', $xml);
        $this->assertStringNotContainsStringIgnoringCase('<IconStyle>', $xml);
        $this->assertStringNotContainsStringIgnoringCase('<LabelStyle>', $xml);
        $this->assertStringNotContainsStringIgnoringCase('<LineStyle>', $xml);
        $this->assertStringNotContainsStringIgnoringCase('<PolyStyle>', $xml);
        $this->assertStringNotContainsStringIgnoringCase('<BalloonStyle>', $xml);
    }
}
