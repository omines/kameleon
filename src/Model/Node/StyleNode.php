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
}
