<?php
declare(strict_types=1);

require_once BASE_PATH . '/models/BaseModel.php';

class OrganizationSetting extends BaseModel
{
    public function get(): ?array
    {
        $stmt = $this->db->query('SELECT * FROM organization_settings ORDER BY id ASC LIMIT 1');
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function save(array $data): bool
    {
        $existing = $this->get();

        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE organization_settings
                SET
                    company_name = :company_name,
                    company_address = :company_address,
                    company_contact = :company_contact,
                    company_email = :company_email,
                    vat_rate = :vat_rate,
                    low_stock_threshold = :low_stock_threshold,
                    logo_path = :logo_path,
                    header_path = :header_path,
                    owner_name = :owner_name,
                    owner_address = :owner_address,
                    owner_contact = :owner_contact,
                    owner_email = :owner_email,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ");

            return $stmt->execute([
                'company_name' => $data['company_name'],
                'company_address' => $data['company_address'],
                'company_contact' => $data['company_contact'],
                'company_email' => $data['company_email'],
                'vat_rate' => $data['vat_rate'],
                'low_stock_threshold' => $data['low_stock_threshold'],
                'logo_path' => $data['logo_path'],
                'header_path' => $data['header_path'],
                'owner_name' => $data['owner_name'],
                'owner_address' => $data['owner_address'],
                'owner_contact' => $data['owner_contact'],
                'owner_email' => $data['owner_email'],
                'id' => $existing['id'],
            ]);
        }

        $stmt = $this->db->prepare("
            INSERT INTO organization_settings (
                company_name,
                company_address,
                company_contact,
                company_email,
                vat_rate,
                low_stock_threshold,
                logo_path,
                header_path,
                owner_name,
                owner_address,
                owner_contact,
                owner_email
            ) VALUES (
                :company_name,
                :company_address,
                :company_contact,
                :company_email,
                :vat_rate,
                :low_stock_threshold,
                :logo_path,
                :header_path,
                :owner_name,
                :owner_address,
                :owner_contact,
                :owner_email
            )
        ");

        return $stmt->execute([
            'company_name' => $data['company_name'],
            'company_address' => $data['company_address'],
            'company_contact' => $data['company_contact'],
            'company_email' => $data['company_email'],
            'vat_rate' => $data['vat_rate'],
            'low_stock_threshold' => $data['low_stock_threshold'],
            'logo_path' => $data['logo_path'],
            'header_path' => $data['header_path'],
            'owner_name' => $data['owner_name'],
            'owner_address' => $data['owner_address'],
            'owner_contact' => $data['owner_contact'],
            'owner_email' => $data['owner_email'],
        ]);
    }

    public function markSetupComplete(): bool
    {
        $existing = $this->get();
        if (!$existing) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE organization_settings
            SET is_setup_complete = 1, setup_completed_at = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");

        return $stmt->execute(['id' => $existing['id']]);
    }
}
