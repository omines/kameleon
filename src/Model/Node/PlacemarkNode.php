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

use Omines\Kameleon\Model\Polygon;

class PlacemarkNode extends KmlNode
{
    private ?string $name = null;

    private ?string $description = null;

    private ?string $styleUrl = null;

    private ?Polygon $outerBoundary = null;

    private ?Polygon $innerBoundary = null;

    private ?Polygon $lineString = null;

    /** @var array<string, string> */
    private array $extendedData = [];

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStyleUrl(): ?string
    {
        return $this->styleUrl;
    }

    public function setStyleUrl(?string $styleUrl): static
    {
        $this->styleUrl = $styleUrl;

        return $this;
    }

    public function getOuterBoundary(): ?Polygon
    {
        return $this->outerBoundary;
    }

    public function setOuterBoundary(?Polygon $outerBoundary): static
    {
        $this->outerBoundary = $outerBoundary;

        return $this;
    }

    public function getInnerBoundary(): ?Polygon
    {
        return $this->innerBoundary;
    }

    public function setInnerBoundary(?Polygon $innerBoundary): static
    {
        $this->innerBoundary = $innerBoundary;

        return $this;
    }

    public function getLineString(): ?Polygon
    {
        return $this->lineString;
    }

    public function setLineString(?Polygon $lineString): static
    {
        $this->lineString = $lineString;

        return $this;
    }

    /** @return array<string, string> */
    public function getExtendedData(): array
    {
        return $this->extendedData;
    }

    /** @param array<string, string> $extendedData */
    public function setExtendedData(array $extendedData): static
    {
        $this->extendedData = $extendedData;

        return $this;
    }

    public function addExtendedData(string $key, string $value): static
    {
        $this->extendedData[$key] = $value;

        return $this;
    }

    public function removeExtendedData(string $key): static
    {
        unset($this->extendedData[$key]);

        return $this;
    }

    public static function fromSimpleXmlElement(\SimpleXMLElement $node): ?self
    {
        $placemarkNode = new self($node->attributes()->id ? (string) $node->attributes()->id : null);

        foreach ($node->children() as $child) {
            switch (mb_strtolower($child->getName())) {
                case 'name':
                    $placemarkNode->setName((string) $child);
                    break;
                case 'description':
                    $placemarkNode->setDescription((string) $child);
                    break;
                case 'styleurl':
                    $placemarkNode->setStyleUrl((string) $child);
                    break;
                case 'polygon':
                    if (isset($child->outerBoundaryIs->LinearRing)) {
                        $placemarkNode->setOuterBoundary(self::buildPolygonFromLinearRing($child->outerBoundaryIs->LinearRing));
                    }
                    if (isset($child->innerBoundaryIs->LinearRing)) {
                        $placemarkNode->setInnerBoundary(self::buildPolygonFromLinearRing($child->innerBoundaryIs->LinearRing));
                    }
                    if (isset($child->LineString)) {
                        $placemarkNode->setLineString(self::buildPolygonFromLinearRing($child->LineString));
                    }
                    break;
            }
        }

        return $placemarkNode;
    }

    private static function buildPolygonFromLinearRing(\SimpleXMLElement $ring): Polygon
    {
        $polygon = (new Polygon())
            ->setExtrude(isset($ring->extrude) && $ring->extrude)
            ->setTessellate(isset($ring->tessellate) && $ring->tessellate)
        ;
        if (isset($ring->altitudeMode)) {
            $polygon->setAltitudeModeFromString((string) $ring->altitudeMode);
        }
        if (isset($ring->coordinates)) {
            $polygon->setCoordinatesFromString((string) $ring->coordinates);
        }

        return $polygon;
    }
}
