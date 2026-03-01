<?php

// 1. Cargar el autoloader manual (mientras no se ejecute composer install)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

require_once __DIR__ . '/config.php';

use App\Repositories\ReservationRepository;
use App\Services\ReservationService;
use App\Observers\EmailObserver;
use App\Observers\LogObserver;
use App\Observers\QuotaObserver;
use App\Controllers\ReservationController;

// --- INICIO DE LA PRUEBA ---
echo "Iniciando prueba de implementación de Patrones de Diseño...\n";

try {
    $db = getDB();

    // Limpiar log anterior para la prueba
    if (file_exists(__DIR__ . '/app.log')) unlink(__DIR__ . '/app.log');

    // 1. Instanciar el repositorio (Data Access)
    $repository = new ReservationRepository($db);

    // 2. Instanciar el servicio (Subject)
    $service = new ReservationService($repository);

    // 3. Adjuntar observadores (Observer Pattern)
    $service->attach(new EmailObserver());
    $service->attach(new LogObserver());
    $service->attach(new QuotaObserver($db));

    // 4. Instanciar el controlador (MVC)
    $controller = new ReservationController($service);

    // 5. Simular una petición para crear una reserva
    $testData = [
        'id_participante' => 1,
        'id_evento' => 1, // Asegúrate de que exista un evento con ID 1 en tu DB
        'id_estado_inscripcion' => 1
    ];

    echo "Ejecutando Controller->store()...\n";
    $result = $controller->store($testData);

    echo "Resultado: " . $result['status'] . " - " . $result['message'] . "\n";

    if ($result['status'] === 'success') {
        echo "Reserva ID: " . $result['data']->id . "\n";
        
        echo "\n--- Verificando Logs del Sistema ---\n";
        if (file_exists(__DIR__ . '/app.log')) {
            echo file_get_contents(__DIR__ . '/app.log');
        } else {
            echo "ERROR: El archivo de log no se generó.\n";
        }
    }

} catch (Exception $e) {
    echo "ERROR CRÍTICO: " . $e->getMessage() . "\n";
}
