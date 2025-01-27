<?php

namespace Brew\Intelipost\DTO;

readonly class QuoteRequestData
{
    public function __construct(
        public string $destination_zip_code,
        public ?string $origin_zip_code = null,
        public array $volumes = [],
        public ?AdditionalInformationData $additional_information = null,
        public ?IdentificationData $identification = null,
    ) {}

    public function toArray(): array
    {
        $data = [
            'destination_zip_code' => $this->destination_zip_code,
            'volumes' => array_map(fn ($volume) => $volume->toArray(), $this->volumes),
        ];

        if ($this->origin_zip_code) {
            $data['origin_zip_code'] = $this->origin_zip_code;
        }

        if ($this->additional_information) {
            $data['additional_information'] = $this->additional_information->toArray();
        }

        if ($this->identification) {
            $data['identification'] = $this->identification->toArray();
        }

        return $data;
    }
}
