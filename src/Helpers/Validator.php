<?php

namespace EventCore\Helpers;

/**
 * Clase Validator
 * Métodos estáticos para validación de datos.
 */
class Validator {
    public static function email(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function required(array $data, array $fields): array {
        $errors = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[$field] = "El campo " . ucfirst($field) . " es obligatorio.";
            }
        }
        return $errors;
    }

    public static function sanitize(string $data): string {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}
