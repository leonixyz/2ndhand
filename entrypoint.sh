#!/bin/bash


if [ ! -f /root/postgresql-www-data.yml ]
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

	# Save data for disaster recovery
	echo "PostgreSQL:" 			>> /root/postgresql-www-data.yml
	echo "  User: www" 			>> /root/postgresql-www-data.yml
	echo "  Pass: $PG_USER_PW" 	>> /root/postgresql-www-data.yml
fi

# Start supervisor
exec supervisord -n