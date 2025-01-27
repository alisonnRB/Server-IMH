#!/bin/bash
set -e

# Entrar no diretório de trabalho
cd /var/www/html

# Instalar dependências via Composer
echo "Instalando dependências do Composer..."
composer install --no-dev --optimize-autoloader

# Ajustar permissões de diretórios críticos
echo "Ajustando permissões..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Iniciar o Supervisor para gerenciar o Apache e o WebSocket
echo "Iniciando Supervisor..."
exec supervisord -c /etc/supervisor/supervisord.conf