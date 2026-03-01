<?php

namespace EventCore\Helpers;

/**
 * Clase FileHelper
 * Utilidades para manejo de archivos y subidas.
 */
class FileHelper {
    public static function upload(array $file, string $destination, array $allowedTypes = ['image/jpeg', 'image/png']): ?string {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('img_', true) . '.' . $extension;
        $target = $destination . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            return $filename;
        }

        return null;
    }
}
