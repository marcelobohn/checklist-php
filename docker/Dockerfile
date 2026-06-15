# Ambiente de época para rodar o checklist legado SEM alterar o fonte.
# PHP 5.6 é a última versão que ainda traz a extensão `mysql_*` (ext/mysql),
# usada em conexaoBD.php e em todos os módulos.
FROM php:5.6-apache

# Instala a extensão antiga ext/mysql (mysql_connect, mysql_query, ...).
# Removida no PHP 7; por isso fixamos o PHP 5.6.
RUN docker-php-ext-install mysql \
    && a2enmod rewrite

# conexaoBD.php conecta em 'localhost', e a API mysql_* trata 'localhost'
# como conexão via SOCKET Unix (ignora a porta). Apontamos o socket para o
# caminho compartilhado com o container do MySQL (volume em docker-compose).
RUN { \
      echo 'mysql.default_socket=/var/run/mysqld/mysqld.sock'; \
      echo 'mysqli.default_socket=/var/run/mysqld/mysqld.sock'; \
      echo 'pdo_mysql.default_socket=/var/run/mysqld/mysqld.sock'; \
    } > /usr/local/etc/php/conf.d/mysql-socket.ini

WORKDIR /var/www/html
