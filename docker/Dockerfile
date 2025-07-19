FROM php:8.3-apache

# Instala dependências do sistema e extensões PHP
RUN apt-get update && apt-get install -y \
    git zip unzip curl libzip-dev && \
    docker-php-ext-install zip pdo pdo_mysql && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug

# Copia o composer do container oficial para cá
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configura o diretório de trabalho
WORKDIR /var/www/html

# Copia a configuração do Xdebug
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Configura o Git para aceitar o diretório /var/www/html
RUN git config --global --add safe.directory /var/www/html

# Ativa mod_rewrite do Apache, necessário para projeto (caso precise de rotas)
# RUN a2enmod rewrite

# COPY ./apache2.conf /etc/apache2/apache2.conf

# Configura o DocumentRoot do Apache para a pasta 'public' do projeto
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf

# Expõe a porta 80 (Apache)
EXPOSE 80

# Comando padrão para rodar o Apache no foreground
CMD ["apache2-foreground"]
