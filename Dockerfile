FROM php:8.2-apache

# Instalar extensões necessárias para PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Habilitar mod_rewrite do Apache (necessário para APIs com URLs limpas)
RUN a2enmod rewrite

# Configurar o DocumentRoot do Apache para a pasta /public do projeto
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar os arquivos do projeto para o container
COPY . /var/www/html/

# Ajustar permissões para o Apache (www-data)
RUN chown -R www-data:www-data /var/www/html

# Porta padrão do Apache
EXPOSE 80
