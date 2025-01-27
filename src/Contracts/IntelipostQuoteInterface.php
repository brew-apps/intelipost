<?php

namespace Brew\Intelipost\Contracts;

use Brew\Intelipost\DTO\QuoteRequestData;

interface IntelipostQuoteInterface
{
    public function quote(QuoteRequestData $quoteData): array;
}
