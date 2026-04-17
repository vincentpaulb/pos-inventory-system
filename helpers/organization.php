<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/OrganizationSetting.php';

function organization_info(bool $refresh = false): array
{
    static $cached = null;

    if ($refresh || $cached === null) {
        try {
            $model = new OrganizationSetting();
            $row = $model->get();
        } catch (Throwable $e) {
            $row = null;
        }

        $cached = [
            'has_record' => $row !== null,
            'company_name' => trim((string) ($row['company_name'] ?? APP_NAME)),
            'company_address' => trim((string) ($row['company_address'] ?? '')),
            'company_contact' => trim((string) ($row['company_contact'] ?? '')),
            'company_email' => trim((string) ($row['company_email'] ?? '')),
            'vat_rate' => max(0.0, (float) ($row['vat_rate'] ?? DEFAULT_VAT_RATE)),
            'low_stock_threshold' => max(0, (int) ($row['low_stock_threshold'] ?? DEFAULT_LOW_STOCK_THRESHOLD)),
            'logo_path' => trim((string) ($row['logo_path'] ?? '')),
            'header_path' => trim((string) ($row['header_path'] ?? '')),
            'owner_name' => trim((string) ($row['owner_name'] ?? '')),
            'owner_address' => trim((string) ($row['owner_address'] ?? '')),
            'owner_contact' => trim((string) ($row['owner_contact'] ?? '')),
            'owner_email' => trim((string) ($row['owner_email'] ?? '')),
            'is_setup_complete' => (int) ($row['is_setup_complete'] ?? 0),
        ];
    }

    return $cached;
}

function organization_setup_step(): string
{
    $organization = organization_info();

    if (!$organization['has_record'] || $organization['company_name'] === '' || $organization['company_address'] === '' || $organization['owner_name'] === '') {
        return 'organization';
    }

    if ((int) $organization['is_setup_complete'] !== 1) {
        return 'admin';
    }

    return 'complete';
}

function organization_is_setup_complete(): bool
{
    return organization_setup_step() === 'complete';
}

function organization_name(): string
{
    $organization = organization_info();
    return $organization['company_name'] !== '' ? $organization['company_name'] : APP_NAME;
}

function organization_initials(): string
{
    $source = organization_name();
    $letters = preg_replace('/[^A-Za-z0-9 ]/', ' ', $source);
    $parts = array_values(array_filter(preg_split('/\s+/', (string) $letters)));

    if (count($parts) >= 2) {
        return strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
    }

    return strtoupper(substr(str_replace(' ', '', (string) $letters), 0, 2) ?: 'PS');
}

function organization_logo_url(): ?string
{
    $path = organization_info()['logo_path'];
    return $path !== '' ? base_url($path) : null;
}

function organization_header_url(): ?string
{
    $path = organization_info()['header_path'];
    return $path !== '' ? base_url($path) : null;
}

function organization_secondary_line(): string
{
    $organization = organization_info();

    if ($organization['company_address'] !== '') {
        return $organization['company_address'];
    }

    if ($organization['owner_name'] !== '') {
        return 'Owner: ' . $organization['owner_name'];
    }

    return '';
}

function system_vat_rate(): float
{
    return organization_info()['vat_rate'];
}

function system_vat_multiplier(): float
{
    return 1 + system_vat_rate();
}

function system_vat_percent(): string
{
    $percent = system_vat_rate() * 100;
    $formatted = number_format($percent, 2, '.', '');
    $formatted = rtrim(rtrim($formatted, '0'), '.');
    return $formatted;
}

function system_vat_label(string $prefix = 'VAT'): string
{
    return trim($prefix . ' ' . system_vat_percent() . '%');
}

function sales_tax_breakdown(float $grossAmount): array
{
    $grossAmount = max(0, $grossAmount);
    $divisor = system_vat_multiplier();
    $netAmount = $divisor > 0 ? $grossAmount / $divisor : $grossAmount;
    $vatAmount = max(0, $grossAmount - $netAmount);

    return [
        'gross' => $grossAmount,
        'net' => $netAmount,
        'vat' => $vatAmount,
    ];
}

function system_low_stock_threshold(): int
{
    return organization_info()['low_stock_threshold'];
}
