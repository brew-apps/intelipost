<?php

namespace Brew\Intelipost\DTO;

class AdditionalInformationData
{
    protected const LEAD_TIME_DAYS = 2;

    public function __construct(
        public bool $free_shipping,
        public float $extra_cost_absolute,
        public float $extra_cost_percentage,
        public int $lead_time_business_days,
        public string $sales_channel,
        public ?string $tax_id,
        public string $client_type,
        public string $payment_type,
        public bool $is_state_tax_payer,
    ) {
        $this->lead_time_business_days = $lead_time_business_days === 0 ? self::LEAD_TIME_DAYS : $lead_time_business_days;
    }

    public function toArray(): array
    {
        return [
            'free_shipping' => $this->free_shipping,
            'extra_cost_absolute' => $this->extra_cost_absolute,
            'extra_cost_percentage' => $this->extra_cost_percentage,
            'lead_time_business_days' => $this->lead_time_business_days,
            'sales_channel' => $this->sales_channel,
            'tax_id' => $this->tax_id,
            'client_type' => $this->client_type,
            'payment_type' => $this->payment_type,
            'is_state_tax_payer' => $this->is_state_tax_payer,
        ];
    }
}
