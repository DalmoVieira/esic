#!/bin/bash
# ====================================================
# COMANDOS RÁPIDOS - E-SIC
# Para uso após deploy em produção
# ====================================================

# CORES
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${BLUE}============================================${NC}"
echo -e "${BLUE}   E-SIC - Menu de Comandos Rápidos${NC}"
echo -e "${BLUE}============================================${NC}"
echo ""

PS3='Escolha uma opção: '
options=(
    "Status dos Serviços"
    "Ver Logs em Tempo Real"
    "Fazer Backup Manual"
    "Restaurar Backup"
    "Reiniciar Serviços"
    "Verificar Espaço em Disco"
    "Testar Conexão MySQL"
    "Renovar SSL"
    "Limpar Cache"
    "Ver Estatísticas"
    "Atualizar Sistema"
    "Sair"
)

select opt in "${options[@]}"
do
    case $opt in
        "Status dos Serviços")
            echo -e "\n${GREEN}Verificando status dos serviços...${NC}\n"
            systemctl status apache2 --no-pager
            echo ""
            systemctl status mysql --no-pager
            echo ""
            ;;
            
        "Ver Logs em Tempo Real")
            echo -e "\n${GREEN}Qual log deseja ver?${NC}"
            echo "1) Apache Error"
            echo "2) Apache Access"
            echo "3) PHP Error"
            echo "4) MySQL Error"
            echo "5) Cron E-SIC"
            read -p "Escolha (1-5): " LOG_CHOICE
            
            case $LOG_CHOICE in
                1) tail -f /var/log/apache2/esic-error.log ;;
                2) tail -f /var/log/apache2/esic-access.log ;;
                3) tail -f /var/log/php/error.log ;;
                4) tail -f /var/log/mysql/error.log ;;
                5) tail -f /var/log/esic-cron.log ;;
            esac
            ;;
            
        "Fazer Backup Manual")
            echo -e "\n${GREEN}Executando backup...${NC}\n"
            /usr/local/bin/backup-esic.sh
            echo -e "\n${GREEN}Backup concluído!${NC}"
            ls -lh /backup/esic/ | tail -5
            ;;
            
        "Restaurar Backup")
            echo -e "\n${GREEN}Backups disponíveis:${NC}"
            ls -lh /backup/esic/
            echo ""
            read -p "Nome do arquivo SQL.gz: " BACKUP_FILE
            
            if [ -f "/backup/esic/$BACKUP_FILE" ]; then
                read -p "Confirma restauração? (s/n): " CONFIRM
                if [ "$CONFIRM" == "s" ]; then
                    gunzip < /backup/esic/$BACKUP_FILE | mysql -u esic_user -p esic_db
                    echo -e "${GREEN}Backup restaurado!${NC}"
                fi
            else
                echo -e "${RED}Arquivo não encontrado!${NC}"
            fi
            ;;
            
        "Reiniciar Serviços")
            echo -e "\n${GREEN}Reiniciando serviços...${NC}\n"
            systemctl restart apache2
            systemctl restart mysql
            echo -e "${GREEN}Serviços reiniciados!${NC}"
            ;;
            
        "Verificar Espaço em Disco")
            echo -e "\n${GREEN}Uso de disco:${NC}\n"
            df -h | grep -E '(Filesystem|/dev/|/var)'
            echo ""
            echo -e "${GREEN}Tamanho dos diretórios principais:${NC}"
            du -sh /var/www/esic/
            du -sh /var/www/esic/uploads/
            du -sh /backup/esic/
            ;;
            
        "Testar Conexão MySQL")
            echo -e "\n${GREEN}Testando conexão MySQL...${NC}\n"
            mysql -u esic_user -p -e "SELECT 'Conexão OK!' as Status; SELECT COUNT(*) as Total_Pedidos FROM pedidos;" esic_db
            ;;
            
        "Renovar SSL")
            echo -e "\n${GREEN}Renovando certificado SSL...${NC}\n"
            certbot renew --force-renewal
            systemctl reload apache2
            echo -e "${GREEN}SSL renovado!${NC}"
            ;;
            
        "Limpar Cache")
            echo -e "\n${GREEN}Limpando cache...${NC}\n"
            # Limpar cache do Apache
            rm -rf /var/cache/apache2/*
            
            # Limpar logs antigos
            find /var/log/apache2/ -name "*.log.*" -mtime +30 -delete
            
            # Limpar sessões PHP antigas
            find /var/lib/php/sessions/ -type f -mtime +1 -delete
            
            echo -e "${GREEN}Cache limpo!${NC}"
            ;;
            
        "Ver Estatísticas")
            echo -e "\n${GREEN}Estatísticas do E-SIC:${NC}\n"
            mysql -u esic_user -p esic_db <<SQL
SELECT 
    'Total de Pedidos' as Metrica,
    COUNT(*) as Valor
FROM pedidos
UNION ALL
SELECT 
    'Pedidos Aguardando',
    COUNT(*) 
FROM pedidos 
WHERE status = 'aguardando'
UNION ALL
SELECT 
    'Pedidos Respondidos',
    COUNT(*) 
FROM pedidos 
WHERE status = 'respondido'
UNION ALL
SELECT 
    'Total de Usuários',
    COUNT(*) 
FROM usuarios
UNION ALL
SELECT 
    'Total de Recursos',
    COUNT(*) 
FROM recursos
UNION ALL
SELECT 
    'Total de Anexos',
    COUNT(*) 
FROM anexos;
SQL
            ;;
            
        "Atualizar Sistema")
            echo -e "\n${GREEN}Atualizando sistema...${NC}\n"
            
            # Fazer backup antes
            /usr/local/bin/backup-esic.sh
            
            # Atualizar do Git
            cd /var/www/esic
            git pull origin main
            
            # Atualizar banco se necessário
            mysql -u esic_user -p esic_db < database/schema_novo.sql
            
            # Ajustar permissões
            chmod 775 uploads logs
            chown -R www-data:www-data uploads logs
            
            # Reiniciar serviços
            systemctl restart apache2
            
            echo -e "\n${GREEN}Sistema atualizado!${NC}"
            ;;
            
        "Sair")
            echo -e "\n${BLUE}Até logo!${NC}\n"
            break
            ;;
            
        *) 
            echo -e "${RED}Opção inválida${NC}"
            ;;
    esac
    
    echo ""
    read -p "Pressione ENTER para voltar ao menu..."
    clear
    echo -e "${BLUE}============================================${NC}"
    echo -e "${BLUE}   E-SIC - Menu de Comandos Rápidos${NC}"
    echo -e "${BLUE}============================================${NC}"
    echo ""
done