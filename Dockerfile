FROM php:8.2-apache

# Instale dependências do sistema
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql

# Instale o Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Defina o diretório de trabalho
WORKDIR /var/www/html

# Copie os arquivos do projeto
COPY . .

# Defina permissões corretas
RUN chown -R www-data:www-data /var/www/html

# Limpe o cache do Composer
RUN composer clear-cache

# Instale as dependências com debug ativado
RUN composer install --no-dev --optimize-autoloader -vvv
