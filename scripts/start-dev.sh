#!/bin/bash
# Script para iniciar servidor de desenvolvimento
# E-SIC v3.0.0

clear
echo "╔══════════════════════════════════════════════════════════╗"
echo "║         🚀 E-SIC - Servidor de Desenvolvimento          ║"
echo "╚══════════════════════════════════════════════════════════╝"
echo ""
echo "📍 Servidor: http://localhost:8000"
echo "📁 Diretório: $(pwd)"
echo ""
echo "🔗 URLs disponíveis:"
echo "   • Login:        http://localhost:8000/login.php"
echo "   • Dashboard:    http://localhost:8000/dashboard.php"
echo "   • Novo Pedido:  http://localhost:8000/novo-pedido.php"
echo "   • Acompanhar:   http://localhost:8000/acompanhar.php"
echo ""
echo "⚠️  Pressione Ctrl+C para parar o servidor"
echo ""
echo "════════════════════════════════════════════════════════════"
echo ""

# Verificar se PHP está instalado
if ! command -v php &> /dev/null; then
    echo "❌ PHP não encontrado!"
    echo "Instale com: brew install php@8.2"
    exit 1
fi

# Verificar se porta 8000 está livre
if lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null ; then
    echo "⚠️  Porta 8000 já está em uso!"
    echo "Deseja usar porta 8001? (s/n)"
    read -r response
    if [[ "$response" =~ ^([sS])$ ]]; then
        PORT=8001
    else
        exit 1
    fi
else
    PORT=8000
fi

# Iniciar servidor PHP
php -S localhost:$PORT -t .
