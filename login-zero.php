<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login E-SIC - Teste Zero</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            max-width: 400px;
            width: 100%;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            opacity: 0.9;
        }
        .links {
            margin-top: 20px;
            text-align: center;
        }
        .links a {
            color: #667eea;
            text-decoration: none;
            display: block;
            margin: 10px 0;
        }
        .links a:hover {
            text-decoration: underline;
        }
        .alert {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="alert">
            <strong>✅ PÁGINA CARREGOU COM SUCESSO!</strong><br>
            Se você está vendo isso, o sistema está funcionando.
        </div>
        
        <h1>🔐 Login E-SIC</h1>
        
        <form method="POST" action="api/login.php">
            <div class="form-group">
                <label for="email">E-mail ou CPF</label>
                <input 
                    type="text" 
                    id="email" 
                    name="email" 
                    placeholder="Digite seu e-mail ou CPF"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="senha">Senha</label>
                <input 
                    type="password" 
                    id="senha" 
                    name="senha" 
                    placeholder="Digite sua senha"
                    required
                >
            </div>
            
            <button type="submit">Entrar no Sistema</button>
        </form>
        
        <div class="links">
            <a href="novo-pedido.php">📝 Fazer um pedido sem login</a>
            <a href="acompanhar.php">🔍 Acompanhar meu pedido</a>
            <a href="transparencia.php">📊 Portal da Transparência</a>
        </div>
        
        <div class="alert" style="margin-top: 20px; background: #fff3cd; border-color: #ffeaa7; color: #856404;">
            <small><strong>Teste Zero:</strong> Esta é uma versão sem CDNs externos (sem Bootstrap, sem jQuery). Todo CSS é inline.</small>
        </div>
    </div>
</body>
</html>
