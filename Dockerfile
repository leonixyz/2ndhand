FROM ubuntu:16.04

MAINTAINER aurelia.pagano@unibz.it
MAINTAINER giulio.roman@unibz.it

VOLUME /var/www/html

# Set environment variables
ENV PGDATA /etc/postgresql/9.5/main/
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid
ENV APACHE_RUN_DIR /var/run/apache2
ENV APACHE_LOCK_DIR /var/lock/

# Install packages
RUN apt-get update && \
	DEBIAN_FRONTEND=noninteractive apt-get -y install apache2 php postgresql supervisor pwgen libapache2-mod-php php7.0-pgsql && \
	sed -i "s/^display_errors = Off$/display_errors = On/" /etc/php/7.0/apache2/php.ini && \
	sed -i "s/^display_startup_errors = Off$/display_startup_errors = On/" /etc/php/7.0/apache2/php.ini && \
	a2enmod rewrite ssl

# Add files into container
ADD ./entrypoint.sh /entrypoint.sh
ADD ./conf/supervisord-apache.conf /etc/supervisor/conf.d/supervisord-apache2.conf
ADD ./conf/supervisord-postgresql.conf /etc/supervisor/conf.d/supervisord-postgresql.conf
ADD https://github.com/vrana/adminer/releases/download/v4.3.0/adminer-4.3.0-en.php /var/www/html/adminer.php
ADD ./conf/apache.conf /etc/apache2/sites-enabled/000-default.conf

# PostgreSQL needs this directory to store statistics
RUN mkdir -p /var/run/postgresql/9.5-main.pg_stat_tmp
RUN chown postgres:postgres /var/run/postgresql/9.5-main.pg_stat_tmp -R

# Apache will listen on port 80
EXPOSE 80 443

CMD ["/bin/bash", "/entrypoint.sh"]