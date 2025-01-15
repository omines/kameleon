<?php

/*
 * KaMeLeon - KML and KMZ reader/writer
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Omines\Kameleon\Model\Node\KmlNode;
use Omines\Kameleon\Model\Node\StyleMapNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(KmlNode::class)]
#[CoversClass(StyleMapNode::class)]
class StyleMapNodeTest extends TestCase
{
    public function testManual(): void
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

    public function testFactory(): void
    {
        $node = KmlNode::fromSimpleXmlElement(new SimpleXMLElement(<<<EOF
<StyleMap id="test">
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

        $this->assertEquals('test', $node->getId());

        $this->assertCount(2, $node->getPairs());
        $this->assertEquals('#test1', $node->getPair('normal'));
        $this->assertEquals('#test2', $node->getPair('highlight'));

        $dom = new DOMDocument();
        $documentElement = $dom->createElement('Document');
        $node->appendTo($dom, $documentElement);
        $dom->appendChild($documentElement);
        $xml = $dom->saveXML();

        $this->assertNotFalse($xml);
        $this->assertStringContainsStringIgnoringCase('<StyleMap id="test">', $xml);
        $this->assertEquals(2, mb_substr_count($xml, '<Pair>'));
        $this->assertStringContainsString('<key>normal</key>', $xml);
        $this->assertStringContainsString('<styleUrl>#test1</styleUrl>', $xml);
        $this->assertStringContainsString('<key>highlight</key>', $xml);
        $this->assertStringContainsString('<styleUrl>#test2</styleUrl>', $xml);
    }
}
