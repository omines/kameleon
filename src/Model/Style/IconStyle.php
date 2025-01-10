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

class IconStyle
{
    public function __construct(private ?string $href = null, private int $x = 0, private int $y = 0, private ?int $width = null, private ?int $height = null)
    {
    }

    public function getHref(): ?string
    {
        return $this->href;
    }

    public function setHref(?string $href): static
    {
        $this->href = $href;

        return $this;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function setX(int $x): static
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function setY(int $y): static
    {
        $this->y = $y;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public static function fromSimpleXmlElement(\SimpleXMLElement $node): ?self
    {
        if (!isset($node->Icon)) {
            return null;
        }

        $style = new self();
        if (isset($node->Icon->href)) {
            $style->setHref((string) $node->Icon->href);
        }
        if (isset($node->Icon->x)) {
            $style->setX((int) $node->Icon->x);
        }
        if (isset($node->Icon->y)) {
            $style->setY((int) $node->Icon->y);
        }
        if (isset($node->Icon->w)) {
            $style->setWidth((int) $node->Icon->w);
        }
        if (isset($node->Icon->h)) {
            $style->setHeight((int) $node->Icon->h);
        }

        return $style;
    }
}
