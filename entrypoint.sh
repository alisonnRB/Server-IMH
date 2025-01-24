#!/bin/bash
set -e

# Entrar no diretório de trabalho
cd /var/www/html

# Instalar dependências via Composer, se necessário
composer install --no-dev --optimize-autoloader -vvv

# Iniciar o Supervisor para gerenciar o Apache e o WebSocket
exec supervisord -c /etc/supervisor/supervisord.conf
