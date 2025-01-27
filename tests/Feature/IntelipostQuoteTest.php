<?php

namespace Brew\Intelipost\Tests\Feature;

use Brew\Intelipost\DTO\AdditionalInformationData;
use Brew\Intelipost\DTO\IdentificationData;
use Brew\Intelipost\DTO\QuoteRequestData;
use Brew\Intelipost\DTO\VolumeData;
use Brew\Intelipost\Exceptions\IntelipostException;
use Brew\Intelipost\Facades\Intelipost;
use Illuminate\Support\Facades\Http;

test('can get quote successfully', function () {
    // Arrange
    Http::fake([
        '*/quote' => Http::response([
            'status' => 'OK',
            'content' => [
                'delivery_options' => [
                    [
                        'delivery_method_id' => 1,
                        'delivery_method_name' => 'Correios PAC',
                        'delivery_estimate_business_days' => 5,
                        'final_shipping_cost' => 8.43,
                    ],
                ],
            ],
        ], 200),
    ]);

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

    $quoteData = new QuoteRequestData(
        destination_zip_code: '04037-003',
        volumes: [$volume],
        additional_information: $additionalInfo,
        identification: $identification
    );

    $response = Intelipost::quote($quoteData);

    expect($response['status'])->toBe('OK')
        ->and($response['content']['delivery_options'])->toHaveCount(1);
});

test('throws exception for invalid api key', function () {
    // Arrange
    Http::fake([
        '*' => Http::response([
            'status' => 'ERROR',
            'messages' => ['Invalid API key'],
        ], 401),
    ]);

    $volume = new VolumeData(
        weight: 0.5,
        volume_type: 'BOX',
        cost_of_goods: 100,
        width: 10,
        height: 10,
        length: 25
    );

    $quoteData = new QuoteRequestData(
        destination_zip_code: '04037-003',
        volumes: [$volume]
    );

    // Act & Assert
    expect(fn () => Intelipost::quote($quoteData))
        ->toThrow(IntelipostException::class, 'Invalid API key');

    Http::assertSent(function ($request) {
        return $request->url() === config('intelipost.api_url').'/quote';
    });
});

test('uses default origin zip code when not provided', function () {
    // Arrange
    Http::fake([
        '*/quote' => Http::response([
            'status' => 'OK',
            'content' => ['delivery_options' => []],
        ], 200),
    ]);

    config(['intelipost.default_origin_zip_code' => '04012-090']);

    $volume = new VolumeData(
        weight: 0.5,
        volume_type: 'BOX',
        cost_of_goods: 100,
        width: 10,
        height: 10,
        length: 25
    );

    $quoteData = new QuoteRequestData(
        destination_zip_code: '04037-003',
        volumes: [$volume]
    );

    // Act
    Intelipost::quote($quoteData);

    // Assert
    Http::assertSent(function ($request) {
        $data = json_decode($request->body(), true);

        return $data['origin_zip_code'] === '04012-090';
    });
});

test('allows overriding default origin zip code', function () {
    // Arrange
    Http::fake([
        '*/quote' => Http::response([
            'status' => 'OK',
            'content' => ['delivery_options' => []],
        ], 200),
    ]);

    config(['intelipost.default_origin_zip_code' => '04012-090']);

    $volume = new VolumeData(
        weight: 0.5,
        volume_type: 'BOX',
        cost_of_goods: 100,
        width: 10,
        height: 10,
        length: 25
    );

    $quoteData = new QuoteRequestData(
        destination_zip_code: '04037-003',
        origin_zip_code: '01153-000',
        volumes: [$volume]
    );

    // Act
    Intelipost::quote($quoteData);

    // Assert
    Http::assertSent(function ($request) {
        $data = json_decode($request->body(), true);

        return $data['origin_zip_code'] === '01153-000';
    });
});

test('handles network errors', function () {
    // Arrange
    Http::fake([
        '*' => Http::response(status: 500),
    ]);

    $volume = new VolumeData(
        weight: 0.5,
        volume_type: 'BOX',
        cost_of_goods: 100,
        width: 10,
        height: 10,
        length: 25
    );

    $quoteData = new QuoteRequestData(
        destination_zip_code: '04037-003',
        volumes: [$volume]
    );

    // Act & Assert
    expect(fn () => Intelipost::quote($quoteData))
        ->toThrow(IntelipostException::class);
});
