<?php
/**
 * Genera hashes de contraseñas para los usuarios seed.
 * Ejecutar una vez: http://localhost/drop/generate_hash.php
 * Luego copiar los hashes al schema.sql y borrar este archivo.
 */
$passwords = ['admin123', 'org123', 'rep123'];
echo "<pre>\n";
foreach ($passwords as $p) {
    echo "$p => " . password_hash($p, PASSWORD_DEFAULT) . "\n";
}
echo "</pre>";
