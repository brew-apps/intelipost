<?php

namespace Brew\Intelipost\Services;

use Brew\Intelipost\Contracts\IntelipostQuoteInterface;
use Brew\Intelipost\DTO\QuoteRequestData;
use Brew\Intelipost\Exceptions\IntelipostException;
use Brew\Intelipost\Models\IntelipostLog;
use Illuminate\Support\Facades\Http;

class IntelipostService implements IntelipostQuoteInterface
{
    protected string $apiUrl;

    protected string $apiKey;

    protected string $defaultOriginZipCode;

    public function __construct()
    {
        $this->apiUrl = config('intelipost.api_url');
        $this->apiKey = config('intelipost.api_key');
        $this->defaultOriginZipCode = config('intelipost.default_origin_zip_code');
    }

    /**
     * @throws IntelipostException
     */
    public function quote(QuoteRequestData $quoteData): array
    {
        $requestData = $quoteData->toArray();

        if (empty($requestData['origin_zip_code'])) {
            $requestData['origin_zip_code'] = $this->defaultOriginZipCode;
        }

        $startTime = microtime(true);

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'api-key' => $this->apiKey,
            ])->post("{$this->apiUrl}/quote", $requestData);

            $executionTime = microtime(true) - $startTime;

            // Tenta decodificar a resposta, se falhar usa um array vazio
            try {
                $responseData = $response->json();
            } catch (\Throwable $e) {
                $responseData = ['messages' => ['Unable to process server response']];
            }

            // Log da requisição (mesmo em caso de erro)
            $this->logRequest(
                'quote',
                $requestData,
                $responseData,
                $response->status(),
                $executionTime
            );

            if (! $response->successful()) {
                $errorMessage = $responseData['messages'][0] ?? 'HTTP Error: '.$response->status();
                $errorMessage = implode('. ', $errorMessage);
                throw new IntelipostException($errorMessage, $response->status());
            }

            if (isset($responseData['status']) && $responseData['status'] === 'ERROR') {
                $errorMessage = $responseData['messages'][0] ?? 'Error response from Intelipost';
                throw new IntelipostException($errorMessage);
            }

            return $responseData;

        } catch (\Throwable $e) {
            if ($e instanceof IntelipostException) {
                throw $e;
            }

            throw new IntelipostException(
                'Failed to communicate with Intelipost API: '.$e->getMessage(),
                $e->getCode() ?: 500,
                $e
            );
        }
    }

    protected function logRequest(
        string $endpoint,
        array $requestData,
        array $responseData,
        int $statusCode,
        float $executionTime
    ): void {
        try {
            IntelipostLog::create([
                'endpoint' => $endpoint,
                'request_data' => $requestData,
                'response_data' => $responseData,
                'status_code' => $statusCode,
                'execution_time' => $executionTime,
            ]);
        } catch (\Throwable $e) {
            // Falha no log não deve interromper o fluxo
            report($e);
        }
    }
}
