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

class StyleMapNode extends KmlNode
{
    /** @param array<string, string> $pairs */
    public function __construct(?string $id = null, private array $pairs = [])
    {
        parent::__construct($id);
    }

    /** @return array<string, string> */
    public function getPairs(): array
    {
        return $this->pairs;
    }

    public function setPair(string $key, string $value): static
    {
        $this->pairs[$key] = $value;

        return $this;
    }

    public function removePair(string $key): static
    {
        unset($this->pairs[$key]);

        return $this;
    }

    public function getPair(string $key): ?string
    {
        return $this->pairs[$key] ?? null;
    }

    public function hasPair(string $key): bool
    {
        return isset($this->pairs[$key]);
    }

    public function clearPairs(): static
    {
        $this->pairs = [];

        return $this;
    }

    public static function fromSimpleXmlElement(\SimpleXMLElement $node): ?self
    {
        $pairs = [];
        foreach ($node->Pair as $pair) {
            $pairs[(string) $pair->key] = (string) $pair->styleUrl;
        }

        return new self($node->id ? (string) $node->id : null, $pairs);
    }
}
