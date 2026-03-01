<?php
/**
 * EventCore — Setup / Instalación
 * 
 * Ejecutar UNA VEZ: http://localhost/drop/tecnoloweb2/setup.php
 * Después de ejecutar, BORRAR este archivo por seguridad.
 */

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'drop';

echo "<!DOCTYPE html><html><head><title>EventCore Setup</title></head><body>";
echo "<pre style='font-family:monospace;background:#080e1a;color:#00d4ff;padding:30px;border-radius:12px;min-height:100vh;margin:0'>\n";
echo "⚡ EventCore — Setup\n";
echo "═══════════════════════════════════════\n\n";

try {
    // Conectar usando mysqli para ejecutar múltiples statements
    $mysqli = new mysqli($host, $user, $pass, $dbname);
    if ($mysqli->connect_error) {
        die("✕ Error de conexión: " . $mysqli->connect_error . "\n");
    }
    echo "✓ Conexión a MySQL (BD: {$dbname}) exitosa\n";
    
    // Leer schema SQL
    $schemaFile = __DIR__ . '/database/schema.sql';
    if (!file_exists($schemaFile)) {
        die("✕ No se encontró database/schema.sql\n");
    }
    
    $sql = file_get_contents($schemaFile);
    echo "✓ Archivo schema.sql leído\n";
    
    // Ejecutar schema completo
    if ($mysqli->multi_query($sql)) {
        do {
            if ($result = $mysqli->store_result()) {
                $result->free();
            }
        } while ($mysqli->next_result());
        
        if ($mysqli->error) {
            echo "⚠ Advertencia: " . $mysqli->error . "\n";
        } else {
            echo "✓ Schema importado exitosamente\n";
        }
    } else {
        echo "⚠ Error en schema: " . $mysqli->error . "\n";
    }
    
    $mysqli->close();
    
    // Ahora actualizar las contraseñas con hashes reales usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    echo "\n► Actualizando contraseñas con hashes seguros:\n";
    
    $passwords = [
        'ladmin@gmail.com'      => '12345',
        'maria@eventcore.com'   => 'org123',
        'roberto@eventcore.com' => 'org123',
        'ana@eventcore.com'     => 'rep123',
    ];
    
    $stmt = $pdo->prepare("UPDATE usuarios_admin SET password_hash = ? WHERE email = ?");
    foreach ($passwords as $email => $plainPass) {
        $hash = password_hash($plainPass, PASSWORD_DEFAULT);
        $stmt->execute([$hash, $email]);
        echo "  ✓ {$email} → contraseña: {$plainPass}\n";
    }

    // Verificación
    echo "\n═══════════════════════════════════════\n";
    echo "✓ SETUP COMPLETADO\n\n";
    echo "► Credenciales de acceso:\n";
    echo "  ┌──────────────────────────────────────────────┐\n";
    echo "  │ Admin:       ladmin@gmail.com / 12345         │\n";
    echo "  │ Organizador: maria@eventcore.com / org123     │\n";
    echo "  │ Reportes:    ana@eventcore.com   / rep123     │\n";
    echo "  └──────────────────────────────────────────────┘\n\n";
    echo "► Acceder al sistema:\n";
    echo "  <a href='/drop/tecnoloweb2/login.php' style='color:#a3e635'>http://localhost/drop/tecnoloweb2/login.php</a>\n\n";
    echo "⚠ IMPORTANTE: Eliminar setup.php después de usar.\n";
    
    // Resumen de tablas
    echo "\n► Tablas creadas:\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $t) {
        try {
            $count = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
            echo "  ✓ $t ($count registros)\n";
        } catch (Exception $e) {
            echo "  ✓ $t (vista)\n";
        }
    }
    
} catch (Exception $e) {
    echo "✕ Error: " . $e->getMessage() . "\n";
    echo "\nAsegúrate de que:\n";
    echo "  1. XAMPP (Apache + MySQL) está ejecutándose\n";
    echo "  2. La base de datos '{$dbname}' existe\n";
    echo "  3. El archivo database/schema.sql existe\n";
}

echo "</pre></body></html>";
