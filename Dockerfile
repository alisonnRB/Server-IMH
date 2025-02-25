# Usar a imagem base do PHP com Apache
FROM php:8.0-apache

# Habilitar o módulo mod_rewrite
RUN a2enmod rewrite

# Instalar dependências necessárias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libicu-dev \
    libxml2-dev \
    libpq-dev \
    git \
    unzip \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pdo_pgsql intl opcache

# Instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer self-update

# Definir o diretório de trabalho
WORKDIR /var/www/html

# Copiar os arquivos do projeto para o contêiner
COPY . /var/www/html/

# Ajustar permissões
RUN chown -R www-data:www-data /var/www/html

# Copiar o script de entrada
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Configurar o Supervisor para gerenciar os processos (Apache + WebSocket)
COPY supervisord.conf /etc/supervisor/supervisord.conf

# Expor as portas necessárias (80 para Apache e 8080 para WebSocket)
EXPOSE 80
EXPOSE 8080

# Comando principal: Supervisor para gerenciar processos
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]

# Usar o script de entrada
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
