#!/bin/bash


if [ ! -f /var/www/html/index.php ]
then
	echo "First run detected."
	
	PG_USER_PW=$(pwgen -c -n -1 12)
	PG_INIT_CMD="create database www;
		create role www with password '$PG_USER_PW';
		grant all privileges on database www to www;
		alter role www with login;"

	su postgres -c '/usr/lib/postgresql/9.5/bin/pg_ctl -w start'
	su postgres -c psql <<< "$PG_INIT_CMD"

	echo "$PG_USER_PW" > /root/password
fi

exec supervisord -n