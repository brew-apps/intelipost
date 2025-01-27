<?php

namespace Brew\Intelipost\Facades;

use Brew\Intelipost\DTO\QuoteRequestData;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array quote(QuoteRequestData $data)
 *
 * @see \Brew\Intelipost\Services\IntelipostService
 */
class Intelipost extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'intelipost';
    }
}
