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

use Omines\Kameleon\Enum\AltitudeMode;

class Polygon
{
    private bool $extrude = false;

    private bool $tessellate = false;

    private AltitudeMode $altitudeMode = AltitudeMode::CLAMP_TO_GROUND;

    /** @var array<int, Coordinate> */
    private array $coordinates = [];

    public function isExtrude(): bool
    {
        return $this->extrude;
    }

    public function setExtrude(bool $extrude): static
    {
        $this->extrude = $extrude;

        return $this;
    }

    public function isTessellate(): bool
    {
        return $this->tessellate;
    }

    public function setTessellate(bool $tessellate): static
    {
        $this->tessellate = $tessellate;

        return $this;
    }

    public function getAltitudeMode(): AltitudeMode
    {
        return $this->altitudeMode;
    }

    public function setAltitudeMode(AltitudeMode $altitudeMode): static
    {
        $this->altitudeMode = $altitudeMode;

        return $this;
    }

    public function setAltitudeModeFromString(string $altitudeMode): static
    {
        $this->altitudeMode = AltitudeMode::fromString($altitudeMode);

        return $this;
    }

    /** @return array<int, Coordinate> */
    public function getCoordinates(): array
    {
        return $this->coordinates;
    }

    /** @param array<int, Coordinate> $coordinates */
    public function setCoordinates(array $coordinates): static
    {
        $this->coordinates = $coordinates;

        return $this;
    }

    public function addCoordinate(Coordinate $coordinate): static
    {
        $this->coordinates[] = $coordinate;

        return $this;
    }

    public function removeCoordinate(Coordinate $coordinate): static
    {
        $key = array_search($coordinate, $this->coordinates, true);

        if (false === $key) {
            throw new \InvalidArgumentException('Coordinate not found in polygon');
        }

        unset($this->coordinates[$key]);

        return $this;
    }

    public function setCoordinatesFromString(string $coordinates): static
    {
        $this->coordinates = [];
        $parts = preg_split('/\s+/', mb_trim($coordinates));
        assert(is_array($parts));

        foreach ($parts as $part) {
            $this->coordinates[] = Coordinate::fromString($part);
        }

        return $this;
    }

    public function getCoordinatesAsString(): string
    {
        return implode("\n", array_map(fn (Coordinate $coordinate) => $coordinate->toString(), $this->coordinates));
    }
}
