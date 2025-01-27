# Laravel Intelipost Package

Esta é uma package Laravel para integração com a API de cotação de fretes da Intelipost.

## Instalação

1. Adicione o repositório ao seu `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/brew-apps/intelipost"
        }
    ]
}
```

2. Instale via Composer:

```bash
composer require brew/intelipost
```

3. Publique os arquivos de configuração e migrations:

```bash
php artisan vendor:publish --provider="Brew\Intelipost\Providers\IntelipostServiceProvider"
```

4. Execute as migrations:

```bash
php artisan migrate
```

5. Configure suas credenciais no arquivo `.env`:

```env
INTELIPOST_API_KEY=sua-chave-api
INTELIPOST_DEFAULT_ORIGIN_ZIP_CODE=seu-cep-padrao
```

## Uso

### Exemplo Básico

```php
use Brew\Intelipost\Facades\Intelipost;
use Brew\Intelipost\DTO\{
    QuoteRequestData,
    VolumeData,
    AdditionalInformationData,
    IdentificationData
};

// Crie o volume
$volume = new VolumeData(
    weight: 0.5,
    volume_type: 'BOX',
    cost_of_goods: 100,
    width: 10,
    height: 10,
    length: 25
);

// Informações adicionais
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

// Informações de identificação
$identification = new IdentificationData(
    session: uniqid(),
    page_name: 'carrinho',
    url: 'http://seu-site.com.br/checkout/cart/'
);

// Crie a requisição de cotação
$quoteRequest = new QuoteRequestData(
    destination_zip_code: '04037-003',
    volumes: [$volume],
    additional_information: $additionalInfo,
    identification: $identification
);

// Faça a cotação
try {
    $quote = Intelipost::quote($quoteRequest);
    
    // Acesse os resultados
    foreach ($quote['content']['delivery_options'] as $option) {
        echo "Método: {$option['delivery_method_name']}\n";
        echo "Prazo: {$option['delivery_estimate_business_days']} dias úteis\n";
        echo "Custo: R$ {$option['final_shipping_cost']}\n";
    }
} catch (\Brew\Intelipost\Exceptions\IntelipostException $e) {
    echo "Erro: " . $e->getMessage();
}
```

### Logs

A package automaticamente registra todas as requisições e respostas na tabela `intelipost_logs`. Você pode acessá-las através do modelo `IntelipostLog`:

```php
use Brew\Intelipost\Models\IntelipostLog;

// Buscar todos os logs
$logs = IntelipostLog::all();

// Buscar logs específicos
$logs = IntelipostLog::where('endpoint', 'quote')
    ->where('status_code', 200)
    ->get();
```

### Personalização

Você pode substituir o CEP de origem padrão durante a requisição:

```php
$quoteRequest = new QuoteRequestData(
    destination_zip_code: '04037-003',
    origin_zip_code: '01153-000', // Sobrescreve o CEP padrão
    volumes: [$volume]
);
```

## Testes

A package inclui testes usando o framework Pest. Para executar os testes:

```bash
vendor/bin/pest
```

## Requisitos

- PHP 8.2 ou superior
- Laravel 11.0

## Contribuindo

Contribuições são bem-vindas! Por favor, sinta-se à vontade para submeter um Pull Request.

## Licença

Esta package é um software open-source licenciado sob a [licença MIT](LICENSE.md).
