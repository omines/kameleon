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
use Omines\Kameleon\Model\Node\PlacemarkNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(KmlNode::class)]
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

    public function testInvalidFactory(): void
    {
        $node = KmlNode::fromSimpleXmlElement(new SimpleXMLElement('<invalid></invalid>'));
        $this->assertNull($node);
    }
}
