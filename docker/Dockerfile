# Ambiente moderno: PHP 8 + Apache, acesso a dados via PDO (pdo_mysql).
FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite

# conexaoBD.php conecta com DSN host=localhost; no PDO, 'localhost' usa SOCKET
# Unix. Apontamos o socket para o caminho compartilhado com o container do
# MySQL (volume em docker-compose). Tambem habilitamos a exibicao de erros
# para facilitar o desenvolvimento.
RUN { \
      echo 'pdo_mysql.default_socket=/var/run/mysqld/mysqld.sock'; \
      echo 'display_errors=On'; \
      echo 'error_reporting=E_ALL'; \
    } > /usr/local/etc/php/conf.d/checklist.ini

WORKDIR /var/www/html
