<?php

namespace Brew\Intelipost\DTO;

readonly class IdentificationData
{
    public function __construct(
        public string $session,
        public string $page_name,
        public string $url,
    ) {}

    public function toArray(): array
    {
        return [
            'session' => $this->session,
            'page_name' => $this->page_name,
            'url' => $this->url,
        ];
    }
}
