FROM ubuntu:16.04

MAINTAINER aurelia.pagano@unibz.it
MAINTAINER giulio.roman@unibz.it

# Set environment variables
ENV PGDATA /etc/postgresql/9.5/main/
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid
ENV APACHE_RUN_DIR /var/run/apache2
ENV APACHE_LOCK_DIR /var/lock/

# Add files into container
ADD ./entrypoint.sh /entrypoint.sh
ADD ./conf/supervisord-apache.conf /etc/supervisor/conf.d/supervisord-apache2.conf
ADD ./conf/supervisord-postgresql.conf /etc/supervisor/conf.d/supervisord-postgresql.conf

# Install packages
RUN apt-get update && \
	DEBIAN_FRONTEND=noninteractive apt-get -y install apache2 php postgresql supervisor pwgen libapache2-mod-php

# PostgreSQL needs this directory to store statistics
RUN mkdir -p /var/run/postgresql/9.5-main.pg_stat_tmp
RUN chown postgres:postgres /var/run/postgresql/9.5-main.pg_stat_tmp -R

# Apache will listen on port 80
EXPOSE 80

CMD ["/bin/bash", "/entrypoint.sh"]