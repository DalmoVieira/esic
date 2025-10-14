<?php
echo "Iniciando teste...\n";

require_once __DIR__ . '/bootstrap.php';
echo "Bootstrap carregado.\n";

use App\Controllers\PublicController;
echo "Namespace importado.\n";

try {
    $controller = new PublicController();
    echo "Controller criado com sucesso!\n";
    
    echo "Testando método index...\n";
    $controller->index();
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>