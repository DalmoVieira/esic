<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 - Muitas requisições | E-SIC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
        <div class="text-center">
            <div class="mb-4">
                <i class="bi bi-speedometer text-danger" style="font-size: 6rem;"></i>
            </div>
            
            <h1 class="display-1 fw-bold text-danger">429</h1>
            <h2 class="mb-4">Muitas Requisições</h2>
            <p class="lead text-muted mb-4">
                Você fez muitas requisições em pouco tempo. Aguarde um momento.
            </p>
            
            <div class="card bg-info bg-opacity-10 border-info mx-auto" style="max-width: 400px;">
                <div class="card-body">
                    <h6 class="card-title text-info">
                        <i class="bi bi-clock me-2"></i>
                        Limite de Taxa
                    </h6>
                    <p class="text-start mb-0 small">
                        Para manter o sistema estável e seguro, limitamos o número de 
                        requisições por minuto. Aguarde alguns minutos e tente novamente.
                    </p>
                </div>
            </div>
            
            <div class="mt-4">
                <div class="spinner-border text-primary me-2" role="status" id="countdown-spinner">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <span id="countdown-text">Aguarde 60 segundos...</span>
            </div>
            
            <div class="d-flex gap-3 justify-content-center flex-wrap mt-4">
                <button onclick="window.location.reload()" class="btn btn-primary" id="retry-btn" disabled>
                    <i class="bi bi-arrow-clockwise me-2"></i>
                    Tentar Novamente
                </button>
                <a href="/" class="btn btn-outline-primary">
                    <i class="bi bi-house me-2"></i>
                    Página Inicial
                </a>
            </div>
        </div>
    </div>
    
    <script>
        let seconds = 60;
        const countdown = setInterval(() => {
            seconds--;
            document.getElementById('countdown-text').textContent = `Aguarde ${seconds} segundos...`;
            
            if (seconds <= 0) {
                clearInterval(countdown);
                document.getElementById('countdown-spinner').style.display = 'none';
                document.getElementById('countdown-text').textContent = 'Você pode tentar novamente agora.';
                document.getElementById('retry-btn').disabled = false;
                document.getElementById('retry-btn').classList.remove('btn-primary');
                document.getElementById('retry-btn').classList.add('btn-success');
            }
        }, 1000);
    </script>
</body>
</html>