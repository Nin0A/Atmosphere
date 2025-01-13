FROM php:8.1-apache

# 📦 Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libxml2-dev \
    libxslt1-dev \
    curl \
    libcurl4-openssl-dev \
    && docker-php-ext-install xsl pdo pdo_mysql mysqli curl

# 🌐 Activer les modules Apache
RUN a2enmod rewrite

# 🚀 Copier les fichiers de l'application dans le conteneur
COPY . /var/www/html/
WORKDIR /var/www/html/

# 🌍 Ajuster les permissions sur les fichiers et répertoires
RUN chown -R www-data:www-data /var/www/html
