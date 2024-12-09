<?php

/*
 * KaMeLeon - KML and KMZ reader/writer
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Omines\Kameleon\Model\Style;

class PolyStyle
{
    public function __construct(private ?string $color = null, private ?int $fill = null, private ?int $outline = null)
    {
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getFill(): ?int
    {
        return $this->fill;
    }

    public function setFill(?int $fill): static
    {
        $this->fill = $fill;

        return $this;
    }

    public function getOutline(): ?int
    {
        return $this->outline;
    }

    public function setOutline(?int $outline): static
    {
        $this->outline = $outline;

        return $this;
    }

    public static function fromSimpleXmlElement(\SimpleXMLElement $node): ?self
    {
        $style = new self();
        if (isset($node->color)) {
            $style->setColor((string) $node->color);
        }

        if (isset($node->fill)) {
            $style->setFill((int) $node->fill);
        }

        if (isset($node->outline)) {
            $style->setOutline((int) $node->outline);
        }

        return $style;
    }
}
