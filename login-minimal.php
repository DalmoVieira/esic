<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-SIC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">🔐 Login - E-SIC Rio Claro</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>✅ Página carregada com sucesso!</strong><br>
                            Se você está vendo esta mensagem, o PHP está funcionando.
                        </div>
                        
                        <form method="POST" action="api/login.php">
                            <div class="mb-3">
                                <label class="form-label">E-mail</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Senha</label>
                                <input type="password" class="form-control" name="senha" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Entrar</button>
                        </form>
                        
                        <hr>
                        
                        <div class="text-center">
                            <p class="mb-2"><a href="novo-pedido.php">Fazer um pedido sem login</a></p>
                            <p class="mb-0"><a href="acompanhar.php">Acompanhar pedido</a></p>
                        </div>
                        
                        <hr>
                        
                        <div class="alert alert-warning">
                            <strong>⚠️ Atenção:</strong><br>
                            Este é um login SIMPLIFICADO para testes.<br>
                            O login completo tem mais recursos.
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="login.php" class="btn btn-secondary">Ver Login Completo Original</a>
                            <a href="test-error.php" class="btn btn-info">Diagnóstico de Erros</a>
                            <a href="phpinfo-test.php" class="btn btn-warning">Info do PHP</a>
                        </div>
                    </div>
                    <div class="card-footer text-center text-muted">
                        <small>Prefeitura Municipal de Rio Claro - SP</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
