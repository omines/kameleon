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

use Omines\Kameleon\Enum\DisplayMode;

class BalloonStyle
{
    public function __construct(private ?string $text = null, private DisplayMode $displayMode = DisplayMode::DEFAULT)
    {
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getDisplayMode(): DisplayMode
    {
        return $this->displayMode;
    }

    public function setDisplayMode(DisplayMode $displayMode): static
    {
        $this->displayMode = $displayMode;

        return $this;
    }

    public function setDisplayModeFromString(string $displayMode): static
    {
        $this->setDisplayMode(DisplayMode::fromString($displayMode));

        return $this;
    }

    public static function fromSimpleXmlElement(\SimpleXMLElement $node): ?self
    {
        $style = new self();

        if (isset($node->text)) {
            $style->setText((string) $node->text);
        }
        if (isset($node->displayMode)) {
            $style->setDisplayModeFromString((string) $node->displayMode);
        }

        return $style;
    }
}
