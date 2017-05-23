#!/bin/bash


if [ ! -f /var/www/postgresql-www-data.php ]
then
	# This is the first time the container has been started
	echo "Configuring container..."
	
	# Generate a random password for PostgreSQL www user
	PG_USER_PW=$(pwgen -c -n -1 12)

	# Start PostgreSQL
	su postgres -c '/usr/lib/postgresql/9.5/bin/pg_ctl -w start'

	# Create PostgreSQL 'www' user and grant privileges on 'www' database 
	PG_INIT_CMD="create database www;
		create role www with password '$PG_USER_PW';
		grant all privileges on database www to www;
		alter role www with login;"
	su postgres -c psql <<< "$PG_INIT_CMD"

	# Save database data
	echo "<?php" 						>> /var/www/postgresql-www-data.php
	echo '$DBCONFIG = array(' 			>> /var/www/postgresql-www-data.php
	echo "  'HOST' => 'localhost'," 	>> /var/www/postgresql-www-data.php
	echo "  'NAME' => 'www'," 			>> /var/www/postgresql-www-data.php
	echo "  'USER' => 'www'," 			>> /var/www/postgresql-www-data.php
	echo "  'PASS' => '$PG_USER_PW'," 	>> /var/www/postgresql-www-data.php
	echo ");" 							>> /var/www/postgresql-www-data.php

	# Initialize database with dummy data
	su postgres -c 'psql --dbname=www' < /init.sql
fi

# Start supervisor
exec supervisord -n