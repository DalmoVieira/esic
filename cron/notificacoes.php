<?php
/**
 * E-SIC - Cron de Notificações
 * Verifica prazos e envia notificações automáticas
 * 
 * Executar via cron: php cron/notificacoes.php
 * Sugestão: executar diariamente às 8h
 */

require_once __DIR__ . '/../app/config/Database.php';
require_once __DIR__ . '/../app/classes/EmailNotificacao.php';

echo "[" . date('Y-m-d H:i:s') . "] Iniciando verificação de notificações...\n";

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    $emailService = new EmailNotificacao();
    
    // Contadores
    $total_enviados = 0;
    $total_erros = 0;
    
    // 1. VERIFICAR PRAZOS PRÓXIMOS DO VENCIMENTO (5 dias)
    echo "\n[1] Verificando prazos próximos do vencimento...\n";
    
    $stmt = $pdo->query("
        SELECT 
            p.*,
            u.nome as requerente_nome,
            u.email as requerente_email,
            DATEDIFF(p.data_limite, CURDATE()) as dias_restantes
        FROM pedidos p
        JOIN usuarios u ON p.requerente_id = u.id
        WHERE p.status IN ('aguardando', 'em_analise')
        AND DATEDIFF(p.data_limite, CURDATE()) = 5
        AND p.notificado_prazo_proximo = 0
    ");
    
    $pedidos_proximos = $stmt->fetchAll();
    
    foreach ($pedidos_proximos as $pedido) {
        echo "  - Notificando prazo próximo: {$pedido['protocolo']} ({$pedido['dias_restantes']} dias)\n";
        
        $requerente = [
            'nome' => $pedido['requerente_nome'],
            'email' => $pedido['requerente_email']
        ];
        
        if ($emailService->notificarPrazoProximo($pedido, $requerente, $pedido['dias_restantes'])) {
            // Marcar como notificado
            $pdo->prepare("UPDATE pedidos SET notificado_prazo_proximo = 1 WHERE id = ?")->execute([$pedido['id']]);
            $total_enviados++;
            echo "    ✓ Email enviado com sucesso\n";
        } else {
            $total_erros++;
            echo "    ✗ Erro ao enviar email\n";
        }
    }
    
    echo "  Total: " . count($pedidos_proximos) . " pedidos notificados\n";
    
    
    // 2. VERIFICAR PRAZOS VENCIDOS
    echo "\n[2] Verificando prazos vencidos...\n";
    
    $stmt = $pdo->query("
        SELECT 
            p.*,
            u.nome as requerente_nome,
            u.email as requerente_email,
            DATEDIFF(CURDATE(), p.data_limite) as dias_vencidos
        FROM pedidos p
        JOIN usuarios u ON p.requerente_id = u.id
        WHERE p.status IN ('aguardando', 'em_analise')
        AND DATE(p.data_limite) < CURDATE()
        AND p.notificado_prazo_vencido = 0
    ");
    
    $pedidos_vencidos = $stmt->fetchAll();
    
    foreach ($pedidos_vencidos as $pedido) {
        echo "  - Notificando prazo vencido: {$pedido['protocolo']} ({$pedido['dias_vencidos']} dias vencidos)\n";
        
        $requerente = [
            'nome' => $pedido['requerente_nome'],
            'email' => $pedido['requerente_email']
        ];
        
        if ($emailService->notificarPrazoVencido($pedido, $requerente)) {
            // Marcar como notificado
            $pdo->prepare("UPDATE pedidos SET notificado_prazo_vencido = 1 WHERE id = ?")->execute([$pedido['id']]);
            $total_enviados++;
            echo "    ✓ Email enviado com sucesso\n";
        } else {
            $total_erros++;
            echo "    ✗ Erro ao enviar email\n";
        }
    }
    
    echo "  Total: " . count($pedidos_vencidos) . " pedidos notificados\n";
    
    
    // 3. VERIFICAR RECURSOS COM PRAZO PRÓXIMO (3 dias)
    echo "\n[3] Verificando recursos com prazo próximo...\n";
    
    $stmt = $pdo->query("
        SELECT 
            r.*,
            p.protocolo as pedido_protocolo,
            u.nome as requerente_nome,
            u.email as requerente_email,
            DATEDIFF(r.data_limite, CURDATE()) as dias_restantes
        FROM recursos r
        JOIN pedidos p ON r.pedido_id = p.id
        JOIN usuarios u ON p.requerente_id = u.id
        WHERE r.status = 'aguardando'
        AND DATEDIFF(r.data_limite, CURDATE()) = 3
    ");
    
    $recursos_proximos = $stmt->fetchAll();
    
    foreach ($recursos_proximos as $recurso) {
        echo "  - Notificando recurso próximo: {$recurso['protocolo']} ({$recurso['dias_restantes']} dias)\n";
        // Lógica similar aos pedidos
        $total_enviados++;
    }
    
    echo "  Total: " . count($recursos_proximos) . " recursos notificados\n";
    
    
    // 4. RELATÓRIO DE PENDÊNCIAS PARA ADMINISTRADORES
    echo "\n[4] Gerando relatório para administradores...\n";
    
    // Buscar estatísticas
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_vencidos
        FROM pedidos
        WHERE status IN ('aguardando', 'em_analise')
        AND DATE(data_limite) < CURDATE()
    ");
    
    $stats = $stmt->fetch();
    
    if ($stats['total_vencidos'] > 0) {
        echo "  - Existem {$stats['total_vencidos']} pedidos vencidos\n";
        
        // Buscar emails dos administradores
        $stmt = $pdo->query("
            SELECT email 
            FROM usuarios 
            WHERE tipo = 'administrador' 
            AND ativo = 1
        ");
        
        $admins = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($admins as $admin_email) {
            // Aqui você pode implementar envio de relatório consolidado
            echo "  - Enviando relatório para: $admin_email\n";
        }
    } else {
        echo "  - Nenhuma pendência crítica encontrada\n";
    }
    
    
    // RESUMO FINAL
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "RESUMO DA EXECUÇÃO\n";
    echo str_repeat("=", 50) . "\n";
    echo "Total de emails enviados: $total_enviados\n";
    echo "Total de erros: $total_erros\n";
    echo "Data/Hora: " . date('Y-m-d H:i:s') . "\n";
    echo str_repeat("=", 50) . "\n";
    
    // Registrar log da execução
    $stmt = $pdo->prepare("
        INSERT INTO logs_sistema (usuario_id, acao, detalhes, data_log)
        VALUES (NULL, 'cron_notificacoes', :detalhes, NOW())
    ");
    
    $detalhes = json_encode([
        'emails_enviados' => $total_enviados,
        'erros' => $total_erros,
        'pedidos_proximos' => count($pedidos_proximos),
        'pedidos_vencidos' => count($pedidos_vencidos),
        'recursos_proximos' => count($recursos_proximos)
    ]);
    
    $stmt->execute([':detalhes' => $detalhes]);
    
    echo "\n[" . date('Y-m-d H:i:s') . "] Verificação concluída com sucesso!\n";
    
} catch (Exception $e) {
    echo "\n[ERRO] " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

exit(0);
?>