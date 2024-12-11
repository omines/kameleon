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

use Omines\Kameleon\Model\Style\BalloonStyle;
use Omines\Kameleon\Model\Style\LineStyle;
use Omines\Kameleon\Model\Style\PolyStyle;

class StyleNode extends KmlNode
{
    private ?LineStyle $lineStyle = null;
    private ?PolyStyle $polyStyle = null;
    private ?BalloonStyle $balloonStyle = null;

    public function getLineStyle(): ?LineStyle
    {
        return $this->lineStyle;
    }

    public function setLineStyle(?LineStyle $lineStyle): static
    {
        $this->lineStyle = $lineStyle;

        return $this;
    }

    public function getPolyStyle(): ?PolyStyle
    {
        return $this->polyStyle;
    }

    public function setPolyStyle(?PolyStyle $polyStyle): static
    {
        $this->polyStyle = $polyStyle;

        return $this;
    }

    public function getBalloonStyle(): ?BalloonStyle
    {
        return $this->balloonStyle;
    }

    public function setBalloonStyle(?BalloonStyle $balloonStyle): static
    {
        $this->balloonStyle = $balloonStyle;

        return $this;
    }

    public static function fromSimpleXmlElement(\SimpleXMLElement $node): ?self
    {
        $style = new self($node->attributes()->id ? (string) $node->attributes()->id : null);

        foreach ($node->children() as $child) {
            switch (mb_strtolower($child->getName())) {
                case 'linestyle':
                    $style->setLineStyle(LineStyle::fromSimpleXmlElement($child));
                    break;
                case 'polystyle':
                    $style->setPolyStyle(PolyStyle::fromSimpleXmlElement($child));
                    break;
                case 'balloonstyle':
                    $style->setBalloonStyle(BalloonStyle::fromSimpleXmlElement($child));
                    break;
            }
        }

        return $style;
    }

    public function appendTo(\DOMDocument $document, \DOMElement $parent): void
    {
        $styleElement = $document->createElement('Style');
        if (null !== $this->getId()) {
            $styleElement->setAttribute('id', $this->getId());
        }
        $parent->appendChild($styleElement);

        if (null !== $this->getLineStyle()) {
            $lineStyle = $document->createElement('LineStyle');
            $styleElement->appendChild($lineStyle);

            if (null !== $this->getLineStyle()->getColor()) {
                $lineStyle->appendChild($document->createElement('Color', $this->getLineStyle()->getColor()));
            }
            if (null !== $this->getLineStyle()->getWidth()) {
                $lineStyle->appendChild($document->createElement('Width', (string) $this->getLineStyle()->getWidth()));
            }
        }

        if (null !== $this->getPolyStyle()) {
            $polyStyle = $document->createElement('PolyStyle');
            $styleElement->appendChild($polyStyle);

            if (null !== $this->getPolyStyle()->getColor()) {
                $polyStyle->appendChild($document->createElement('Color', $this->getPolyStyle()->getColor()));
            }
            if (null !== $this->getPolyStyle()->getFill()) {
                $polyStyle->appendChild($document->createElement('fill', (string) $this->getPolyStyle()->getFill()));
            }
            if (null !== $this->getPolyStyle()->getOutline()) {
                $polyStyle->appendChild($document->createElement('outline', (string) $this->getPolyStyle()->getOutline()));
            }
        }

        if (null !== $this->getBalloonStyle()) {
            $balloonStyle = $document->createElement('BalloonStyle');
            $styleElement->appendChild($balloonStyle);

            if (null !== $this->getBalloonStyle()->getText()) {
                $balloonStyle->appendChild($document->createElement('text', $this->getBalloonStyle()->getText()));
            }
        }
    }
}
