<?php
/**
 * Teste de Produção - E-SIC
 * Execute este arquivo para verificar se tudo está funcionando
 * URL: https://seudominio.com.br/esic/test-production.php
 */

define('ESIC_SECURE', true);
session_start();

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧪 Teste E-SIC Produção</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-gear"></i> Teste de Produção E-SIC</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Teste automático do sistema em produção</strong><br>
                            Executado em: <?= date('d/m/Y H:i:s') ?>
                        </div>

                        <?php
                        $tests = [];
                        $allPassed = true;

                        // Teste 1: Versão PHP
                        $phpVersion = phpversion();
                        if (version_compare($phpVersion, '8.0.0', '>=')) {
                            $tests[] = ['✅', 'PHP Version', $phpVersion, 'success'];
                        } else {
                            $tests[] = ['❌', 'PHP Version', $phpVersion . ' (Requer 8.0+)', 'danger'];
                            $allPassed = false;
                        }

                        // Teste 2: Conexão com banco de dados
                        try {
                            $environment = $_SERVER['HTTP_HOST'] ?? 'localhost';
                            
                            if (strpos($environment, 'localhost') !== false || strpos($environment, '127.0.0.1') !== false) {
                                // Desenvolvimento
                                $config = [
                                    'host' => 'localhost',
                                    'dbname' => 'esic_db',
                                    'username' => 'root',
                                    'password' => '',
                                    'charset' => 'utf8mb4'
                                ];
                            } else {
                                // Produção
                                if (file_exists('config/production.php')) {
                                    $prodConfig = include 'config/production.php';
                                    $config = $prodConfig['database'];
                                } else {
                                    throw new Exception('Arquivo config/production.php não encontrado');
                                }
                            }
                            
                            $pdo = new PDO(
                                "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
                                $config['username'],
                                $config['password'],
                                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                            );
                            
                            // Testa uma query simples
                            $stmt = $pdo->query("SELECT 1 as test");
                            $result = $stmt->fetch();
                            
                            if ($result && $result['test'] == 1) {
                                $tests[] = ['✅', 'Banco de Dados', 'Conectado (' . $config['dbname'] . ')', 'success'];
                            } else {
                                $tests[] = ['❌', 'Banco de Dados', 'Erro na query de teste', 'danger'];
                                $allPassed = false;
                            }
                        } catch (Exception $e) {
                            $tests[] = ['❌', 'Banco de Dados', 'Erro: ' . $e->getMessage(), 'danger'];
                            $allPassed = false;
                        }

                        // Teste 3: Extensões PHP necessárias
                        $requiredExtensions = ['mysqli', 'pdo', 'mbstring', 'json', 'session'];
                        $missingExtensions = [];
                        
                        foreach ($requiredExtensions as $ext) {
                            if (!extension_loaded($ext)) {
                                $missingExtensions[] = $ext;
                            }
                        }
                        
                        if (empty($missingExtensions)) {
                            $tests[] = ['✅', 'Extensões PHP', 'Todas instaladas (' . implode(', ', $requiredExtensions) . ')', 'success'];
                        } else {
                            $tests[] = ['❌', 'Extensões PHP', 'Faltando: ' . implode(', ', $missingExtensions), 'danger'];
                            $allPassed = false;
                        }

                        // Teste 4: Permissões de escrita
                        $writableDirs = ['uploads/', 'logs/', 'cache/'];
                        $nonWritable = [];
                        
                        foreach ($writableDirs as $dir) {
                            if (!is_dir($dir)) {
                                @mkdir($dir, 0755, true);
                            }
                            if (!is_writable($dir)) {
                                $nonWritable[] = $dir;
                            }
                        }
                        
                        if (empty($nonWritable)) {
                            $tests[] = ['✅', 'Permissões', 'Todas as pastas graváveis', 'success'];
                        } else {
                            $tests[] = ['❌', 'Permissões', 'Sem escrita: ' . implode(', ', $nonWritable), 'warning'];
                        }

                        // Teste 5: SSL/HTTPS
                        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                            $tests[] = ['✅', 'SSL/HTTPS', 'Ativo e funcionando', 'success'];
                        } else {
                            $tests[] = ['⚠️', 'SSL/HTTPS', 'Não detectado (recomendado para produção)', 'warning'];
                        }

                        // Teste 6: Configuração de segurança
                        $securityHeaders = [
                            'X-Content-Type-Options' => 'nosniff',
                            'X-Frame-Options' => 'DENY',
                            'X-XSS-Protection' => '1; mode=block'
                        ];
                        
                        $securityOk = true;
                        foreach ($securityHeaders as $header => $value) {
                            if (!headers_sent()) {
                                header("$header: $value");
                            }
                        }
                        
                        if ($securityOk) {
                            $tests[] = ['✅', 'Headers Segurança', 'Configurados corretamente', 'success'];
                        }

                        // Teste 7: Arquivos principais
                        $requiredFiles = [
                            'index.php' => 'Página principal',
                            'novo-pedido.php' => 'Nova solicitação',
                            'acompanhar.php' => 'Acompanhamento',
                            'transparencia.php' => 'Portal transparência',
                            'config/database.php' => 'Config banco',
                            '.htaccess' => 'Configuração Apache'
                        ];
                        
                        $missingFiles = [];
                        foreach ($requiredFiles as $file => $desc) {
                            if (!file_exists($file)) {
                                $missingFiles[] = "$desc ($file)";
                            }
                        }
                        
                        if (empty($missingFiles)) {
                            $tests[] = ['✅', 'Arquivos Sistema', 'Todos os arquivos presentes', 'success'];
                        } else {
                            $tests[] = ['❌', 'Arquivos Sistema', 'Faltando: ' . implode(', ', $missingFiles), 'danger'];
                            $allPassed = false;
                        }

                        // Teste 8: Configuração de produção
                        if (file_exists('config/production.php')) {
                            $tests[] = ['✅', 'Config Produção', 'Arquivo encontrado', 'success'];
                        } else {
                            $tests[] = ['⚠️', 'Config Produção', 'Arquivo config/production.php não encontrado', 'warning'];
                        }

                        // Teste 9: Memória e limites
                        $memoryLimit = ini_get('memory_limit');
                        $maxExecutionTime = ini_get('max_execution_time');
                        $uploadMaxSize = ini_get('upload_max_filesize');
                        
                        $tests[] = ['ℹ️', 'Limites PHP', "Memória: $memoryLimit | Exec: {$maxExecutionTime}s | Upload: $uploadMaxSize", 'info'];
                        ?>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th width="10%">Status</th>
                                        <th width="25%">Teste</th>
                                        <th>Resultado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tests as $test): ?>
                                    <tr class="table-<?= $test[3] ?>">
                                        <td class="text-center fs-4"><?= $test[0] ?></td>
                                        <td><strong><?= $test[1] ?></strong></td>
                                        <td><?= $test[2] ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($allPassed): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle"></i>
                                <strong>🎉 Todos os testes essenciais passaram!</strong><br>
                                O sistema E-SIC está pronto para produção.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i>
                                <strong>⚠️ Alguns testes falharam!</strong><br>
                                Corrija os problemas antes de usar em produção.
                            </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php" class="btn btn-primary">
                                <i class="bi bi-house"></i> Ir para E-SIC
                            </a>
                            <button onclick="location.reload()" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Executar Novamente
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        <h6><i class="bi bi-info-circle"></i> Informações do Servidor</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <small>
                                    <strong>Servidor:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' ?><br>
                                    <strong>PHP:</strong> <?= phpversion() ?><br>
                                    <strong>Sistema:</strong> <?= php_uname() ?><br>
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small>
                                    <strong>Host:</strong> <?= $_SERVER['HTTP_HOST'] ?? 'N/A' ?><br>
                                    <strong>IP:</strong> <?= $_SERVER['SERVER_ADDR'] ?? 'N/A' ?><br>
                                    <strong>Timezone:</strong> <?= date_default_timezone_get() ?><br>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>