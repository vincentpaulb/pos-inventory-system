<?php
declare(strict_types=1);

function validate_required(array $fields, array $labels): array
{
    $errors = [];

    foreach ($fields as $key => $value) {
        if ($value === null || trim((string) $value) === '') {
            $errors[$key] = ($labels[$key] ?? ucfirst($key)) . ' is required.';
        }
    }

    return $errors;
}

function validate_positive_number(string $field, mixed $value, string $label): ?string
{
    if (!is_numeric($value) || (float) $value < 0) {
        return $label . ' must be a valid non-negative number.';
    }
    return null;
}

function validate_integer(string $field, mixed $value, string $label): ?string
{
    if (filter_var($value, FILTER_VALIDATE_INT) === false || (int) $value < 0) {
        return $label . ' must be a valid non-negative integer.';
    }
    return null;
}
