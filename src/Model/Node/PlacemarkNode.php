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

use Omines\Kameleon\Model\Coordinate;
use Omines\Kameleon\Model\Polygon;

class PlacemarkNode extends KmlNode
{
    private ?string $name = null;

    private ?string $description = null;

    private ?string $styleUrl = null;

    private ?Polygon $outerBoundary = null;

    private ?Polygon $innerBoundary = null;

    private ?Polygon $lineString = null;

    private ?Coordinate $point = null;

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

    public function getPoint(): ?Coordinate
    {
        return $this->point;
    }

    public function setPoint(?Coordinate $point): static
    {
        $this->point = $point;

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
                case 'point':
                    $coordinates = explode(',', (string) $child->Point->coordinates);
                    if (count($coordinates) < 2) {
                        throw new \InvalidArgumentException('Invalid coordinate string');
                    }
                    $point = new Coordinate(
                        (float) $coordinates[0],
                        (float) $coordinates[1],
                        isset($coordinates[2]) ? (float) $coordinates[2] : 0
                    );
                    $placemarkNode->setPoint($point);
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

    public function appendTo(\DOMDocument $document, \DOMElement $parent): void
    {
        $placemark = $document->createElement('Placemark');
        if (null !== $this->getId()) {
            $placemark->setAttribute('id', $this->getId());
        }
        $parent->appendChild($placemark);

        if (null !== $this->name) {
            $name = $document->createElement('name', $this->name);
            $placemark->appendChild($name);
        }

        if (null !== $this->description) {
            $description = $document->createElement('description', $this->description);
            $placemark->appendChild($description);
        }

        if (null !== $this->styleUrl) {
            $styleUrl = $document->createElement('styleUrl', $this->styleUrl);
            $placemark->appendChild($styleUrl);
        }

        if (null !== $this->outerBoundary || null !== $this->innerBoundary) {
            $polygon = $document->createElement('Polygon');
            $placemark->appendChild($polygon);

            if (null !== $this->outerBoundary) {
                $outerBoundary = $document->createElement('outerBoundaryIs');
                $polygon->appendChild($outerBoundary);

                $linearRing = $document->createElement('LinearRing');
                $outerBoundary->appendChild($linearRing);

                $linearRing->appendChild($document->createElement('extrude', $this->outerBoundary->isExtrude() ? '1' : '0'));
                $linearRing->appendChild($document->createElement('tesselate', $this->outerBoundary->isTessellate() ? '1' : '0'));
                $linearRing->appendChild($document->createElement('altitudeMode', $this->outerBoundary->getAltitudeMode()->value));
                $linearRing->appendChild($document->createElement('coordinates', $this->outerBoundary->getCoordinatesAsString()));
            }

            if (null !== $this->innerBoundary) {
                $innerBoundary = $document->createElement('innerBoundaryIs');
                $polygon->appendChild($innerBoundary);

                $linearRing = $document->createElement('LinearRing');
                $innerBoundary->appendChild($linearRing);

                $linearRing->appendChild($document->createElement('extrude', $this->innerBoundary->isExtrude() ? '1' : '0'));
                $linearRing->appendChild($document->createElement('tesselate', $this->innerBoundary->isTessellate() ? '1' : '0'));
                $linearRing->appendChild($document->createElement('altitudeMode', $this->innerBoundary->getAltitudeMode()->value));
                $linearRing->appendChild($document->createElement('coordinates', $this->innerBoundary->getCoordinatesAsString()));
            }
        }

        if (null !== $this->lineString) {
            $lineString = $document->createElement('LineString');
            $placemark->appendChild($lineString);

            $lineString->appendChild($document->createElement('extrude', $this->lineString->isExtrude() ? '1' : '0'));
            $lineString->appendChild($document->createElement('tesselate', $this->lineString->isTessellate() ? '1' : '0'));
            $lineString->appendChild($document->createElement('altitudeMode', $this->lineString->getAltitudeMode()->value));
            $lineString->appendChild($document->createElement('coordinates', $this->lineString->getCoordinatesAsString()));
        }

        if (null !== $this->point) {
            $point = $document->createElement('Point');
            $placemark->appendChild($point);

            $point->appendChild($document->createElement('coordinates', $this->point->toString()));
        }

        if (count($this->extendedData) > 0) {
            $extendedData = $document->createElement('ExtendedData');
            $placemark->appendChild($extendedData);

            foreach ($this->extendedData as $key => $value) {
                $data = $document->createElement('Data');
                $data->setAttribute('name', $key);
                $extendedData->appendChild($data);

                $data->appendChild($document->createElement('value', $value));
            }
        }
    }
}
