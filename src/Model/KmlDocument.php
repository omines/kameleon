<?php

/*
 * KaMeLeon - KML and KMZ reader/writer
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Omines\Kameleon\Model;

use Omines\Kameleon\Model\Node\KmlNode;

class KmlDocument
{
    /** @var string */
    private const KML_XMLNS = 'http://www.opengis.net/kml/2.2';

    /** @var array<int, KmlNode> */
    private array $nodes = [];

    public function __construct(private string $name, private string $xmlns = self::KML_XMLNS)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getXmlns(): string
    {
        return $this->xmlns;
    }

    public function setXmlns(string $xmlns): static
    {
        $this->xmlns = $xmlns;

        return $this;
    }

    /** @return array<int, KmlNode> */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    public function addNode(KmlNode $node): static
    {
        $this->nodes[] = $node;

        return $this;
    }

    public function removeNode(KmlNode $node): static
    {
        $key = array_search($node, $this->nodes, true);

        if (false !== $key) {
            unset($this->nodes[$key]);
        }

        return $this;
    }

    public function clearNodes(): static
    {
        $this->nodes = [];

        return $this;
    }

    public function hasNode(KmlNode $node): bool
    {
        return in_array($node, $this->nodes, true);
    }
}
