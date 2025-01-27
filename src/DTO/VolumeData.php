<?php

namespace Brew\Intelipost\DTO;

readonly class VolumeData
{
    public function __construct(
        public float $weight,
        public string $volume_type,
        public float $cost_of_goods,
        public float $width,
        public float $height,
        public float $length,
    ) {}

    public function toArray(): array
    {
        return [
            'weight' => $this->weight / 1000,
            'volume_type' => $this->volume_type,
            'cost_of_goods' => $this->cost_of_goods,
            'width' => $this->width,
            'height' => $this->height,
            'length' => $this->length,
        ];
    }
}
