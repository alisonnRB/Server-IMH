# Use a imagem base do PHP com Apache
FROM php:8.2-apache

# Instale extensões necessárias e outras dependências
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libpq-dev && \
    docker-php-ext-install pdo_pgsql && \
    a2enmod rewrite

# Instale o Composer globalmente
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copie os arquivos do projeto
COPY . /var/www/html

# Instale as dependências do Composer
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader

# Defina permissões corretas
RUN chown -R www-data:www-data /var/www/html

# Exponha a porta do Apache
EXPOSE 80

# Comando padrão para iniciar o servidor
CMD ["apache2-foreground"]
