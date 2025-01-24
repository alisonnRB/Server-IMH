# Usar a imagem base do PHP com Apache
FROM php:8.0-apache

# Habilitar o módulo mod_rewrite
RUN a2enmod rewrite

# Habilitar módulos necessários
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libicu-dev \
    libxml2-dev \
    libpq-dev \ 
    git \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pdo_pgsql intl opcache

# Baixar o Composer (caso não esteja presente)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Atualizar o Composer para a versão mais recente
RUN composer self-update

# Definir o diretório de trabalho
WORKDIR /var/www/html

# Copiar os arquivos do projeto para dentro do container
COPY . /var/www/html/

# Garantir as permissões corretas para o Apache
RUN chown -R www-data:www-data /var/www/html

# Copiar o script de entrada para o contêiner
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

# Dar permissão de execução ao script
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expor a porta 80
EXPOSE 80

CMD ["php", "servidor_chat.php"]

# Usar o script de entrada
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
