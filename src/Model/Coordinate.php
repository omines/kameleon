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

class Coordinate
{
    public function __construct(private float $latitude, private float $longitude, private float $altitude = 0)
    {
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getAltitude(): float
    {
        return $this->altitude;
    }

    public function setAltitude(float $altitude): static
    {
        $this->altitude = $altitude;

        return $this;
    }

    public static function fromString(string $string): self
    {
        $parts = explode(',', $string);
        if (count($parts) < 2) {
            throw new \InvalidArgumentException('Invalid coordinate string');
        }

        return new self((float) $parts[1], (float) $parts[0], isset($parts[2]) ? (float) $parts[2] : 0);
    }

    public function toString(): string
    {
        return sprintf('%f,%f,%f', $this->longitude, $this->latitude, $this->altitude);
    }
}
