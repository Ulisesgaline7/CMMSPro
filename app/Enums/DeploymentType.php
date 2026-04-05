<?php

namespace App\Enums;

enum DeploymentType: string
{
    case CloudSaas = 'cloud_saas';
    case Subdomain = 'subdomain';
    case OnPremise = 'on_premise';
    case WhiteLabel = 'white_label';

    public function label(): string
    {
        return match ($this) {
            self::CloudSaas => 'Cloud SaaS',
            self::Subdomain => 'Subdominio Dedicado',
            self::OnPremise => 'On-Premise',
            self::WhiteLabel => 'White Label',
        };
    }

    public function basePrice(): float
    {
        return match ($this) {
            self::CloudSaas => 49.0,
            self::Subdomain => 89.0,
            self::OnPremise => 199.0,
            self::WhiteLabel => 299.0,
        };
    }
}
