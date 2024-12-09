<?php

/*
 * KaMeLeon - KML and KMZ reader/writer
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Omines\Kameleon\Model\Node;

abstract class KmlNode
{
    public function __construct(private ?string $id = null)
    {
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public static function fromSimpleXmlElement(\SimpleXMLElement $node): ?self
    {
        return match (mb_strtolower($node->getName())) {
            'style' => StyleNode::fromSimpleXmlElement($node),
            'stylemap' => StyleMapNode::fromSimpleXmlElement($node),
            default => null,
        };
    }
}
