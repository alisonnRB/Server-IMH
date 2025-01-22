#!/bin/bash
# Entrar no diretório de trabalho
cd /var/www/html

# Instalar dependências via Composer se necessário
composer install --no-dev --optimize-autoloader -vvv

# Iniciar o Apache
apache2-foreground
