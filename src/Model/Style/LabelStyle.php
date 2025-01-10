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

class LabelStyle
{
    public function __construct(private ?string $color = null, private int $scale = 1)
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

    public function getScale(): int
    {
        return $this->scale;
    }

    public function setScale(int $scale): static
    {
        $this->scale = $scale;

        return $this;
    }

    public static function fromSimpleXmlElement(\SimpleXMLElement $node): ?self
    {
        $style = new self();

        if (isset($node->Color)) {
            $style->setColor((string) $node->Color);
        }
        if (isset($node->scale)) {
            $style->setScale((int) $node->scale);
        }

        return $style;
    }
}
