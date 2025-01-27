<?php

use Brew\Intelipost\DTO\AdditionalInformationData;
use Brew\Intelipost\DTO\IdentificationData;
use Brew\Intelipost\DTO\QuoteRequestData;
use Brew\Intelipost\DTO\VolumeData;

test('can create quote request with minimal data', function () {
    // Arrange & Act
    $quoteRequest = new QuoteRequestData(
        destination_zip_code: '04037-003'
    );

    // Assert
    expect($quoteRequest)
        ->toBeInstanceOf(QuoteRequestData::class)
        ->and($quoteRequest->destination_zip_code)->toBe('04037-003')
        ->and($quoteRequest->origin_zip_code)->toBeNull()
        ->and($quoteRequest->volumes)->toBeArray()
        ->and($quoteRequest->volumes)->toBeEmpty()
        ->and($quoteRequest->additional_information)->toBeNull()
        ->and($quoteRequest->identification)->toBeNull();
});

test('can create quote request with all data', function () {
    // Arrange
    $volume = new VolumeData(
        weight: 0.5,
        volume_type: 'BOX',
        cost_of_goods: 100,
        width: 10,
        height: 10,
        length: 25
    );

    $additionalInfo = new AdditionalInformationData(
        free_shipping: false,
        extra_cost_absolute: 0,
        extra_cost_percentage: 0,
        lead_time_business_days: 0,
        sales_channel: 'hotsite',
        tax_id: '22337462000127',
        client_type: 'gold',
        payment_type: 'CIF',
        is_state_tax_payer: false
    );

    $identification = new IdentificationData(
        session: '04e5bdf7ed15e571c0265c18333b6fdf1434658753',
        page_name: 'carrinho',
        url: 'http://www.intelipost.com.br/checkout/cart/'
    );

    // Act
    $quoteRequest = new QuoteRequestData(
        destination_zip_code: '04037-003',
        origin_zip_code: '04012-090',
        volumes: [$volume],
        additional_information: $additionalInfo,
        identification: $identification
    );

    // Assert
    expect($quoteRequest)
        ->toBeInstanceOf(QuoteRequestData::class)
        ->and($quoteRequest->destination_zip_code)->toBe('04037-003')
        ->and($quoteRequest->origin_zip_code)->toBe('04012-090')
        ->and($quoteRequest->volumes)->toHaveCount(1)
        ->and($quoteRequest->volumes[0])->toBeInstanceOf(VolumeData::class)
        ->and($quoteRequest->additional_information)->toBeInstanceOf(AdditionalInformationData::class)
        ->and($quoteRequest->identification)->toBeInstanceOf(IdentificationData::class);
});

test('toArray method returns correct structure with minimal data', function () {
    // Arrange
    $quoteRequest = new QuoteRequestData(
        destination_zip_code: '04037-003'
    );

    // Act
    $array = $quoteRequest->toArray();

    // Assert
    expect($array)
        ->toBeArray()
        ->toHaveKeys(['destination_zip_code', 'volumes'])
        ->and($array['destination_zip_code'])->toBe('04037-003')
        ->and($array['volumes'])->toBeArray()
        ->and($array['volumes'])->toBeEmpty()
        ->and($array)->not->toHaveKey('origin_zip_code')
        ->and($array)->not->toHaveKey('additional_information')
        ->and($array)->not->toHaveKey('identification');
});

test('toArray method returns correct structure with all data', function () {
    // Arrange
    $volume = new VolumeData(
        weight: 0.5,
        volume_type: 'BOX',
        cost_of_goods: 100,
        width: 10,
        height: 10,
        length: 25
    );

    $additionalInfo = new AdditionalInformationData(
        free_shipping: false,
        extra_cost_absolute: 0,
        extra_cost_percentage: 0,
        lead_time_business_days: 0,
        sales_channel: 'hotsite',
        tax_id: '22337462000127',
        client_type: 'gold',
        payment_type: 'CIF',
        is_state_tax_payer: false
    );

    $identification = new IdentificationData(
        session: '04e5bdf7ed15e571c0265c18333b6fdf1434658753',
        page_name: 'carrinho',
        url: 'http://www.intelipost.com.br/checkout/cart/'
    );

    $quoteRequest = new QuoteRequestData(
        destination_zip_code: '04037-003',
        origin_zip_code: '04012-090',
        volumes: [$volume],
        additional_information: $additionalInfo,
        identification: $identification
    );

    // Act
    $array = $quoteRequest->toArray();

    // Assert
    expect($array)
        ->toBeArray()
        ->toHaveKeys([
            'destination_zip_code',
            'origin_zip_code',
            'volumes',
            'additional_information',
            'identification',
        ]);

    expect($array['volumes'][0])
        ->toHaveKeys([
            'weight',
            'volume_type',
            'cost_of_goods',
            'width',
            'height',
            'length',
        ]);

    // Verificando valores específicos
    expect($array['destination_zip_code'])->toBe('04037-003')
        ->and($array['origin_zip_code'])->toBe('04012-090')
        ->and($array['volumes'])->toHaveCount(1);

    // Verificando volume
    expect($array['volumes'][0]['weight'])->toBe(0.5)
        ->and($array['volumes'][0]['volume_type'])->toBe('BOX')
        ->and($array['volumes'][0]['cost_of_goods'])->toBeInt()->toBe(100)
        ->and($array['volumes'][0]['width'])->toBe(10)
        ->and($array['volumes'][0]['height'])->toBe(10)
        ->and($array['volumes'][0]['length'])->toBe(25);
});

test('null values are not included in array output', function () {
    // Arrange
    $quoteRequest = new QuoteRequestData(
        destination_zip_code: '04037-003',
        origin_zip_code: null,
        volumes: [],
        additional_information: null,
        identification: null
    );

    // Act
    $array = $quoteRequest->toArray();

    // Assert
    expect($array)
        ->toBeArray()
        ->toHaveKeys(['destination_zip_code', 'volumes'])
        ->and($array)->not->toHaveKey('origin_zip_code')
        ->and($array)->not->toHaveKey('additional_information')
        ->and($array)->not->toHaveKey('identification')
        ->and($array['volumes'])->toBeEmpty();
});
