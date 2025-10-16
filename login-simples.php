<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Simples - Teste</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4>Login - E-SIC</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="api/login.php">
                            <div class="mb-3">
                                <label class="form-label">E-mail ou CPF</label>
                                <input type="text" class="form-control" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Senha</label>
                                <input type="password" class="form-control" name="senha" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Entrar</button>
                        </form>
                        <hr>
                        <p class="text-center mb-0">
                            <a href="novo-pedido.php">Fazer um pedido sem login</a> |
                            <a href="acompanhar.php">Acompanhar pedido</a>
                        </p>
                    </div>
                </div>
                
                <div class="alert alert-info mt-3">
                    <strong>Teste de Funcionalidade</strong><br>
                    Este é um login simplificado para testar se o redirecionamento está funcionando.
                    <hr>
                    <a href="login.php" class="btn btn-sm btn-primary">Ver Login Completo</a>
                    <a href="phpinfo-test.php" class="btn btn-sm btn-secondary">Ver Info PHP</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
