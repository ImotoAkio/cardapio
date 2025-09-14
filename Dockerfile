# =====================================================
# 🍃 TEMPERO E CAFÉ - CONFIGURAÇÃO DOCKER
# =====================================================

# Dockerfile para PHP 7.4 com Apache
FROM php:7.4-apache

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    mysqli \
    gd \
    zip \
    mbstring \
    xml \
    curl \
    json \
    fileinfo

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar arquivos de configuração
COPY .htaccess /var/www/html/
COPY nginx.conf /etc/nginx/sites-available/default

# Copiar código da aplicação
COPY . /var/www/html/

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/dist/img \
    && chmod -R 777 /var/www/html/logs

# Configurar PHP
RUN echo "date.timezone = America/Sao_Paulo" >> /usr/local/etc/php/conf.d/timezone.ini \
    && echo "upload_max_filesize = 5M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 5M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 30" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 128M" >> /usr/local/etc/php/conf.d/uploads.ini

# Expor porta 80
EXPOSE 80

# Comando de inicialização
CMD ["apache2-foreground"]
